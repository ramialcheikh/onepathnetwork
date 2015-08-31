<?php

class Admin@@plural-pascalCase@@Controller extends BaseController{
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
        if($sort == 'shareRate') {
            $sort = DB::raw('@@singular@@_stats.shares/@@singular@@_stats.attempts');
        }
        return array('sort' => $sort, 'sortType' => $sortType);
    }
    public function list@@plural-pascalCase@@(){
        $search = Input::get('search', null);
        $sortOptions = self::processSort();
        //Uses normal left join than "with('stats')" to be able to sort by shareRate
        $@@plural@@Query = @@model@@::joinStats()->orderBy($sortOptions['sort'], $sortOptions['sortType']);
        if($search){
            $@@plural@@Query->where('topic', 'like', '%' . $search . '%');
        }
        $@@plural@@ = $@@plural@@Query->paginate(self::$perPage);
        if($search){
            $@@plural@@->appends(['search' => $search]);
        }
        //dd(DB::getQueryLog());
        //dd($@@plural@@->toArray());
        self::addShareRates($@@plural@@);

        return View::make('admin/@@plural@@/view')->with(array(
            '@@plural@@' => $@@plural@@,
            'search' => $search
        ));
    }

    public function createEdit(){

        $@@singular@@Id = Input::get('@@singular@@Id', null);
        $@@singular@@Data = json_decode(Input::get('@@singular@@', '{}'), true);
        $duplicate@@singular-pascalCase@@ = Input::get('duplicate-@@singular@@', array());
        $duplicate@@singular-pascalCase@@Data = null;
        try {
            if($duplicate@@singular-pascalCase@@) {
                $duplicate@@singular-pascalCase@@Object = @@model@@::findOrFail($duplicate@@singular-pascalCase@@);
                $duplicate@@singular-pascalCase@@Data = $duplicate@@singular-pascalCase@@Object->toArray();
                unset($duplicate@@singular-pascalCase@@Data['id']);
                View::share(array(
                    'duplicate@@singular-pascalCase@@' =>  $duplicate@@singular-pascalCase@@Object
                ));
            }
            if($@@singular@@Id || !empty($@@singular@@Data['id'])) {
                //die(var_dump($@@singular@@Id));
                $@@singular@@ = @@model@@::findOrFail($@@singular@@Id ? $@@singular@@Id : $@@singular@@Data['id']);
            } else {
                $@@singular@@ = new @@model@@;
            }
        } catch(ModelNotFoundException $e) {
            return Response::json(array(
                'error' => 1,
                'message' => $e->getMessage()
            ));
        }

        if(Request::ajax() && Request::isMethod('post')) {
            //Form submitted- Create the @@singular@@

            //$keys = ['topic', 'description', 'pageContent', 'image', 'questions', 'results', 'ogImages'];

            foreach($@@singular@@Data as $key => $val) {
                $@@singular@@->$key = is_array($@@singular@@Data[$key]) ? json_encode($@@singular@@Data[$key]) : $@@singular@@Data[$key];
            }
            $@@singular@@->active = (!empty($@@singular@@Data['active']) && ($@@singular@@Data['active'] === "true" || $@@singular@@Data['active'] === true)) ? true : false;
            try {
                self::saveThumbnails($@@singular@@);
            } catch (InvalidArgumentException $e) {
                return Response::error("Error! @@singular-pascalCase@@ not saved! " . $e->getMessage());
            }

            $@@singular@@->save();
            return Response::json(array(
                'success' => 1,
                '@@singular@@' => $@@singular@@
            ));
        } else {
            //Form submitted- Create the @@singular@@ or parse and show forms if basic @@singular@@Data is passed
            $populate@@singular-pascalCase@@Data = Input::get('@@singular@@Data', '{}');
            $populate@@singular-pascalCase@@Data = json_decode($populate@@singular-pascalCase@@Data);
            if(!empty($duplicate@@singular-pascalCase@@Data)) {
                $populate@@singular-pascalCase@@Data = $duplicate@@singular-pascalCase@@Data;
            }

            $@@singular@@Schema = new \Schemas\@@singular-pascalCase@@Schema();
            $questionSchema = new \Schemas\QuestionSchema();
            $choiceSchema = new \Schemas\ChoiceSchema();
            $resultSchema = new \Schemas\ResultSchema();
            /*if(!empty(Input::get('test')))
                die(var_dump($@@singular@@));*/
            return View::make('admin/@@plural@@/create')->with(array(
                '@@singular@@Schema' => $@@singular@@Schema->getSchema(),
                'questionSchema' => $questionSchema->getSchema(),
                'choiceSchema' => $choiceSchema->getSchema(),
                'resultSchema' => $resultSchema->getSchema(),
                '@@singular@@Data' => $@@singular@@->id ? json_encode($@@singular@@) : json_encode($populate@@singular-pascalCase@@Data),
                '@@singular@@' => $@@singular@@,
                'editingMode' => $@@singular@@Id ? true : false,
                'creationMode' => $@@singular@@Id ? false : true
            ));
        }
    }

    public function delete(){

        $@@singular@@Id = Input::get('@@singular@@Id', null);
        if(!$@@singular@@Id){
            return Response::error("@@singular-pascalCase@@ not found");
        }
        try {
            $@@singular@@ = @@model@@::findOrFail($@@singular@@Id ? $@@singular@@Id : $@@singular@@Data['id']);
        } catch(ModelNotFoundException $e) {
            return Response::error("Error finding @@singular@@ with id " . $@@singular@@Id);
        }
        if($@@singular@@->delete()){
            return Response::json(array(
                'success' => true
            ));
        } else {
            return Response::error("Some error occured while deleting @@singular@@ : '" . $@@singular@@Id->topic . "'");
        }
    }

    public function embedCodes(){
        return View::make('admin/@@plural@@/embedCodes');
    }

    public static function saveThumbnails($@@singular@@) {
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

        $ogImages = (array) $@@singular@@->ogImages;
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

    public static function getThumbnail($@@singular@@) {

    }

    public static function addShareRates(&$@@plural@@){
        foreach($@@plural@@ as $@@singular@@){
            $@@singular@@Stats = $@@singular@@->stats;
            $@@singular@@ShareRate = $@@singular@@LikeRate = number_format(0, 2);
            if($@@singular@@Stats['attempts']) {
                $@@singular@@ShareRate =  number_format(($@@singular@@Stats['shares'] / $@@singular@@Stats['attempts']) * 100, 2);
                $@@singular@@LikeRate =  number_format(($@@singular@@Stats['likes'] / $@@singular@@Stats['attempts']) * 100, 2);
            }
            $@@singular@@->shareRate = $@@singular@@ShareRate;
            $@@singular@@->likeRate = $@@singular@@LikeRate;
            if($@@singular@@->shareRate > 40)
                $@@singular@@->shareRateRange = "high";
            else if($@@singular@@->shareRate > 20)
                $@@singular@@->shareRateRange = "fair";
            else if($@@singular@@->shareRate > 10)
                $@@singular@@->shareRateRange = "medium";
            else
                $@@singular@@->shareRateRange = "low";
        }
    }
}