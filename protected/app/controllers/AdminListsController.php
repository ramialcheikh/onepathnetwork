<?php

class AdminListsController extends AdminBaseController{
    public static $perPage = 10;
    public static $sortableFields = array('created_at', 'shareRate');
    public static function processSort(){
        $sort = Input::get('sort') ? Input::get('sort') : 'created_at';
        $sortType = (Input::get('sortType') === 'asc') ? 'asc' : 'desc';

        if(!in_array($sort, self::$sortableFields)) {
            Response::notFound();
        }
        View::share(array(
            'sort' => $sort,
            'sortType' => $sortType
        ));
        return array('sort' => $sort, 'sortType' => $sortType);
    }
    public function listLists(){
        $search = Input::get('search', null);
        $filterStatus = Input::get('status', null);
        $sortOptions = self::processSort();
        $listsQuery = ViralList::orderBy($sortOptions['sort'], $sortOptions['sortType']);
        if($search){
            $listsQuery->where('topic', 'like', '%' . $search . '%');
        }
        if($filterStatus) {
            $listsQuery->where('status', $filterStatus);
            if($filterStatus == 'awaiting_approval') {
                $listsQuery->orHas('pendingChanges');
            }
        }
        $lists = $listsQuery->paginate(self::$perPage);
        if($search){
            $lists->appends(['search' => $search]);
        }
        //dd(DB::getQueryLog());


        return View::make('admin/lists/view')->with(array(
            'lists' => $lists,
            'search' => $search
        ));
    }

    public function createEdit(){

        $listId = Input::get('listId', null);
        $listData = json_decode(Input::get('list', '{}'), true);
        $duplicateList = Input::get('duplicate-list', array());
        $duplicateListData = null;
        try {
            if($duplicateList) {
                $duplicateListObject = ViralList::findOrFail($duplicateList);
                $duplicateListData = $duplicateListObject->toArray();
                unset($duplicateListData['id']);
                View::share(array(
                    'duplicateList' =>  $duplicateListObject
                ));
            }
            if($listId || !empty($listData['id'])) {
                //die(var_dump($listId));
                $list = ViralList::findOrFail($listId ? $listId : $listData['id']);
            } else {
                $list = new ViralList;
            }
        } catch(ModelNotFoundException $e) {
            return Response::json(array(
                'error' => 1,
                'message' => $e->getMessage()
            ));
        }

        if(Request::ajax() && Request::isMethod('post')) {
            //Form submitted- Create the list

            //$keys = ['topic', 'description', 'pageContent', 'image', 'questions', 'results', 'ogImages'];

            foreach($listData as $key => $val) {
                $list->$key = is_array($listData[$key]) ? json_encode($listData[$key]) : $listData[$key];
            }
            $list->active = (!empty($listData['active']) && ($listData['active'] === "true" || $listData['active'] === true)) ? true : false;
            try {
                self::saveThumbnails($list);
            } catch (InvalidArgumentException $e) {
                return Response::error("Error! List not saved! " . $e->getMessage());
            }

            $list->save();
            return Response::json(array(
                'success' => 1,
                'list' => $list
            ));
        } else {
            //Form submitted- Create the list or parse and show forms if basic listData is passed
            $populateListData = Input::get('listData', '{}');
            $populateListData = json_decode($populateListData);
            if(!empty($duplicateListData)) {
                $populateListData = $duplicateListData;
            }

            $listSchema = new \Schemas\ListSchema();
            $questionSchema = new \Schemas\QuestionSchema();
            $choiceSchema = new \Schemas\ChoiceSchema();
            $resultSchema = new \Schemas\ResultSchema();
            /*if(!empty(Input::get('test')))
                die(var_dump($list));*/
            return View::make('admin/lists/create')->with(array(
                'listSchema' => $listSchema->getSchema(),
                'questionSchema' => $questionSchema->getSchema(),
                'choiceSchema' => $choiceSchema->getSchema(),
                'resultSchema' => $resultSchema->getSchema(),
                'listData' => $list->id ? json_encode($list) : json_encode($populateListData),
                'list' => $list,
                'editingMode' => $listId ? true : false,
                'creationMode' => $listId ? false : true
            ));
        }
    }

    public function delete(){

        $listId = Input::get('listId', null);
        if(!$listId){
            return Response::error("List not found");
        }
        try {
            $list = ViralList::findOrFail($listId ? $listId : $listData['id']);
        } catch(ModelNotFoundException $e) {
            return Response::error("Error finding list with id " . $listId);
        }
        if($list->delete()){
            return Response::json(array(
                'success' => true
            ));
        } else {
            return Response::error("Some error occured while deleting list : '" . $listId->topic . "'");
        }
    }

    public function embedCodes(){
        return View::make('admin/lists/embedCodes');
    }

    public static function saveThumbnails($list) {
        function saveThumbnail($imagePath, $width, $height) {
            $imgContent = file_get_contents($imagePath);
            $image = imagecreatefromstring($imgContent);
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);
            $pathParts = pathinfo($imagePath);
            $thumbPath = $pathParts['dirname'] . '/' . $pathParts['basename'] . '_thumb.jpg';
            $thumbImage = imagecreatetruecolor($width, $height);
            imagecopyresampled($thumbImage, $image, 0, 0, 0, 0, $width, $height, $origWidth, $origHeight);
            imagejpeg($thumbImage, $thumbPath, 90);
        }

        $ogImages = (array) $list->ogImages;
        if(!empty($ogImages['main'])) {
            if(strpos($ogImages['main'], 'http://') === 0 || strpos($ogImages['main'], 'https://') === 0) {
                throw new InvalidArgumentException("You shouldn't use remote images for Og images. Please upload them!");
            }
            if(file_exists(public_path($ogImages['main']))) {
                saveThumbnail(public_path($ogImages['main']), 400, 210);
            }
        }
        /*foreach($ogImages as $ogImage) {
            if(!empty($ogImage) && file_exists('.' . $ogImage)) {
                saveThumbnail('.' . $ogImage, 210, 400);
            }
        }*/

    }

    public function approveList() {
        $admin = App::make('loggedInAdmin');
        $listId = Input::get('list-id');
        $redirectUrl= Input::get('redirect-url');
        if(!$listId) {
            return('List ID not passed');
        }
        try {
            $list = ViralList::findOrFail($listId);
            $pendingChanges = ViralListChanges::find($listId);
            if($pendingChanges) {
                ListController::addListData($pendingChanges->content, $list);
                $pendingChanges->delete();
            }
            $list->markAsApproved();
            try {
                ListController::saveThumbnails($list);
                ListController::saveOgImage($list);
            } catch (Exception $e) {
                return Response::error("Error! List not saved! " . $e->getMessage());
            }
            $list->save();
            \Event::fire('list:approved-by-admin', $list);
            return Redirect::to(Helpers::getUrlWithQuery(array('status' => 'awaiting_approval'), route('adminViewLists')));
        } catch(ModelNotFoundException $e) {
            return Response::error("Error finding list with id " . $listId);
        }
    }

    public function disapproveList() {
        $admin = App::make('loggedInAdmin');
        $listId = Input::get('list-id');
        $redirectUrl= Input::get('redirect-url');
        $changesOnly    =   Input::get('changes-only');
        if(!$listId) {
            return('List ID not passed');
        }
        try {
            $list = ViralList::findOrFail($listId);
            if($changesOnly){
                $list->pendingChanges()->delete();
            } else {
                $list->markAsDisapproved();
                $list->save();
            }
            return Redirect::to(Helpers::getUrlWithQuery(array('status' => 'awaiting_approval'), route('adminViewLists')));
        } catch(ModelNotFoundException $e) {
            return Response::error("Error finding list with id " . $listId);
        }
    }

    public static function getThumbnail($list) {

    }

}