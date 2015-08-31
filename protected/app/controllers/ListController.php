<?php

use \Notifications;

class ListController extends BaseController {
    const DEFAULT_ORDER_BY = 'created_at';
    const DEFAULT_ORDER_BY_TYPE = 'desc';
    const JUST_SAVED_LIST_PARAM = 'just-saved';
    const OG_IMAGE_WIDTH = 600;
    const OG_IMAGE_HEIGHT = 315;
    const IMAGE_MAX_WIDTH = 720;
    public static function _getListFormRules() {
        $rules = ['title' => 'required|max:160', 'description' => 'required|max:512', 'category_id' => 'required|min:1', 'tags' => 'required', 'image' => 'required'];
        return $rules;
    }
    public static function incrementListStats($list, $type) {
        switch($type) {
            case 'views':
                $statColumnName = 'views';
                break;
            default:
                $statColumnName = '';
                $methodName = '';
        }

        if(empty($statColumnName) || empty($methodName)) {
            throw new Exception('Invalid activity');
        }
        $list->$statColumnName += 1;
        $list->save();
        return true;
    }

    public function index($options = array()) {

        $loadListOptions = ['limit' => self::getPerPageLimit()];
        $stream = 'latest';
        if(isset($options['stream'])) {
            $streamOptions = self::getListQueryStreamOptions($options);
            $stream = $options['stream'];
            $loadListOptions = array_merge($loadListOptions, $streamOptions);
        }

        if(isset($options['category'])) {
            $category = $options['category'];
            $loadListOptions['categoryId'] = $category->id;
            View::share('categoryName', $category->name);
        }

        self::_loadLists($loadListOptions);
        $titleLangKey = ($stream == "latest") ? 'latestLists' : (($stream == "popular") ? 'popularLists' : 'lists');

        $pageHeading = __($titleLangKey);
        if(!empty($options['ofTag'])) {
            $pageHeading = $options['ofTag'];
        }
        if(!empty($options['stream']) && $options['stream'] == 'search' && !empty($options['query'])) {
            $pageHeading = '"'. $options['query'] .'"';
        }

        //If it is a category page, use custom meta tags assigned for that category
        if(!empty($category)) {
            $pageHeading = $category->name;
            $pageTitle = $category->meta_title;
            $pageDescription = $category->meta_description;
        } else {
            $pageTitle = $pageHeading . ' | ' . Config::get('siteConfig')['main']['siteName'];
            //No custom meta description available, use default home page meta description
            $pageDescription = Config::get('siteConfig')['main']['siteDescription'];
        }


        return View::make('lists/index')->with(array(
            'currentPage' => 'listsIndex',
            'title' => $pageTitle,
            'ogTitle' => $pageTitle,
            'description' => $pageDescription,
            'ogDescription' => $pageDescription,
            'isStream' . ucfirst($stream) => true,
            'mainHeading' => $pageHeading
        ));
    }

    public function category($categorySlug){
        $category = Category::findBySlug($categorySlug);
        if(!$category)
            return Response::notFound();
        return $this->index(['category' => $category]);
    }

    public function popular(){
        return $this->index(['stream' => 'popular']);
    }

    public function iframeList(){
        $loadListsOptions = array();
        $loadListsOptions['limit'] = Input::get('limit');
        self::_loadLists($loadListsOptions);

        $pageTitle = __('lists') . ' | ' . Config::get('siteConfig')['main']['siteName'];
        $pageDescription = __('hereAreSomeLists');
        return View::make('lists/iframeList')->with(array(
            'currentPage' => 'listsIndex',
            'title' => $pageTitle,
            'ogTitle' => $pageTitle,
            'description' => $pageDescription,
            'ogDescription' => $pageDescription
        ));
    }

    public static function _getLists($options = array()) {
        $orderBy = !empty($options['order_by']) ? $options['order_by'] : self::DEFAULT_ORDER_BY;
        $orderByType = !empty($options['order_by_type']) ? $options['order_by_type'] : self::DEFAULT_ORDER_BY_TYPE;

        $listsQuery = ViralList::approved()->with('category');

        //Tag filter
        if(!empty($options['ofTag']))
            $listsQuery->withAllTags($options['ofTag']);

        $listsQuery->orderBy($orderBy, $orderByType);
        $limit = isset($options['limit']) ? $options['limit'] : self::getPerPageLimit();
        if(!empty($options['exclude'])) {
            $listsQuery->exclude($options['exclude']);
        }
        if(!empty($options['categoryId'])) {
            $listsQuery->ofCategory($options['categoryId']);
        }
        if(!empty($options['search'])) {
            $listsQuery->search($options['search']);
        }

        //dd($listsQuery->toSql());
        $lists = $listsQuery->simplePaginate($limit);
        foreach($lists as $key => $list) {
            $lists[$key] = $list;
        }
        self::touchUpLists($lists);
        //dd($lists->toArray());
        return $lists;
    }

    public static function _loadLists($options = array()) {
        $getListsOptions = $options;
        if(!empty($options['related_to'])) {
            $getListsOptions['exclude'] = $options['related_to'];
        }

        $lists = self::_getLists($getListsOptions);
        View::share('lists', $lists);
    }

    public function getRouteParams($list) {
        return ListHelpers::viewListUrlParams($list);
    }

    public static function getViewListUrl($list) {
        return ListHelpers::viewListUrl($list);
    }

    public function viewList($nameString, $listId, $itemNumber = null) {
        $currentUser = Auth::user();
        $admin = App::make('loggedInAdmin');
        try {
            $sharedUserId = Input::get('user-fb-id');
            $list = ViralList::findOrFail($listId);
            $listCreator = $list->creator;
            $list->viewListUrl = self::getViewListUrl($list);
            $listTags = $list->tagged;

            if($admin) {
                $previewPendingChanges = Input::get('preview-pending-changes');
                if($previewPendingChanges) {
                    $pendingChanges = $list->pendingChanges;
                    if($pendingChanges) {
                        self::addListData($pendingChanges->content, $list);
                    }
                }
            }

            if(!$list->isApproved()) {
                if(!$list->isCreatedBy($currentUser) && !Session::get('admin')) {
                    App::abort(404);
                }
            }
            $config = Config::get('siteConfig');

            //If multi page mode is enabled, show one item per page
            if($config['list']['viewListMode'] == 'multi-page') {
                $itemNumber = !$itemNumber ? 1 : intval($itemNumber);
                $listContent = $list->content;
                View::share([
                    'oneItemPerPageMode' => true,
                    'hasNextItem' => true,
                    'showingItemNumber' => $itemNumber,
                    'hasNextItem' => false,
                    'hasPreviousItem' => false,
                    'showInterstitialAdNext' => false,
                    'showInterstitialAdNow' => false
                ]);
                if(!empty($listContent[$itemNumber]))
                    View::share(['hasNextItem' => true]);
                if($itemNumber > 1)
                    View::share('hasPreviousItem', true);

                $list->content = json_encode(array_slice($list->content, $itemNumber-1, 1, true));
                $showAdAfterEvery = intval($config['list']['showAdAfterEvery']);
                if($showAdAfterEvery) {
                    //Show ad after every n items - enabled
                    if(Input::get('ad')) {
                        View::share('showInterstitialAdNow', true);
                    }
                    if(($itemNumber % $showAdAfterEvery) == 0)
                        View::share('showInterstitialAdNext', true);
                }
            }
            $ogTitle = $list->title;
            $ogUrl = $canonicalUrl = $list->viewListUrl;
            $ogImage = URL::asset(ListHelpers::getListOGPathFromImage($list->image));
            return View::make('lists/viewList')->with(array(
                'list' => $list,
                'listCreator'  =>  $listCreator,
                'viewListUrl' => self::getViewListUrl($list),
                'listTags' =>  $listTags,
                'currentPage' => 'viewList',
                'sharedUserId' => $sharedUserId,
                'ogType'    => 'article',
                'ogImage' => $ogImage,
                'ogTitle' => $ogTitle,
                'ogImageWidth'  =>self::OG_IMAGE_WIDTH,
                'ogImageHeight'  =>self::OG_IMAGE_HEIGHT,
                'metaAuthorName'    =>  $listCreator->name,
                'metaArticlePublishedTime'  =>  $list->created_at,
                'ogUrl' => $ogUrl,
                'title' => $list->title,
                'ogDescription' => $list->description,
                'description' => $list->description,
                'canonicalUrl' => $canonicalUrl
            ));
        }catch(Illuminate\Database\Eloquent\ModelNotFoundExautception $e) {
            return Response::notFound('List not found');
        }
    }

    public function createEdit() {

        //Creating or saving a list as draft - or updating an existing list.
        $listId = Input::get('listId', null);
        $listData = Input::get('list', '{}');
        $listData = json_decode($listData, true);
        $duplicateList = Input::get('create-from');
        $user = Auth::user();
        $isNewList = false;

        $validateRules = self::_getListFormRules();

        try {
            if($duplicateList) {
                $duplicateListObject = ViralList::findOrFail($duplicateList);
                View::share(array(
                    'duplicateList' =>  $duplicateListObject
                ));
            }
            if($listId || !empty($listData['id'])) {
                //Editing a list
                $editListId = $listId ? $listId : $listData['id'];
                $list = ViralList::findOrFail($editListId);
                $pendingChanges = $list->pendingChanges;
                if($pendingChanges) {
                    View::share('hasChangesPendingApproval', true);
                    self::addListData($pendingChanges->content, $list);
                }
                $this->_ensurePermission($list);
            } else {
                $list = new ViralList();
                $list->creator_user_id = $user->id;
                if($duplicateList)
                    $list->created_from_list_id = $duplicateList;
                $isNewList = true;
            }
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if(Request::ajax()) {
                return Response::error($e->getMessage());
            } else {
                return Response::notFound();
            }
        } catch(PermissionDeniedException $e) {
            return Response::error($e->getMessage());
        }


        if(Request::ajax() && Request::isMethod('post')) {
            //Form submitted- Create/update the list

            $validator = Validator::make($listData, $validateRules);
            if($validator->fails()) {
                //Validation failed - Respond with error
                return Response::error(__('someErrorsInInput'));
            }

            //Set status explicitly - Don't allow editor/admin to alter it via listData - Approval option has a separate controller action
            $this->_setApprovalStatusOnSave($list);

            $markedForApprovalAgain = false;

            //If the list is marked for approval again or if there is pending changes for approval already(regardless of current approval status)
            if(!self::isAutoApproveUpdatesEnabled() && $list->isApproved()) {
                $markedForApprovalAgain = true;
                if(!$pendingChanges) {
                    $pendingChanges = new ViralListChanges(['id' => $list->id]);
                }
                $pendingChanges->content = json_encode($listData);
                $pendingChanges->save();
            } else {
                self::addListData($listData, $list);
                try {
                    self::saveThumbnails($list);
                    self::saveOgImage($list);
                } catch (Exception $e) {
                    return Response::error("Error! List not saved! " . $e->getMessage());
                }
            }
            $list->save();

            //Add tags
            $list->retag($listData['tags']);

            return Response::json(array(
                'success' => 1,
                'list' => $list,
                'markedForApprovalAgain' => $markedForApprovalAgain,
                'redirect' => $isNewList ? self::_getJustSavedListEditurl($list) : null
            ));
        } else {
            //Show list editor page
            $populateQuizData = Input::get('listData', '{}');
            $populateQuizData = json_decode($populateQuizData);
            if(!empty($duplicateQuizData)) {
                $populateQuizData = $duplicateQuizData;
            }

            if($list->exists) {
                View::share('list', $list);
            }

            $justSavedTheNewList = Input::get(self::JUST_SAVED_LIST_PARAM, false);
            View::share('justSavedTheNewList', $justSavedTheNewList);
            return View::make('lists.create');
        }
    }

    public function previewList() {
        $listId = Input::get('list-id');
        if(!$listId) {
            return Response::notFound();
        }
        return $this->viewList('', $listId);
    }

    public function publishList() {

        $listId = Input::get('list-id');
        if(!$listId) {
            return Response::notFound();
        }

        try {
            $list = ViralList::findOrFail($listId);
            $this->_ensurePermission($list);
        } catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            if(Request::ajax()) {
                return Response::error($e->getMessage());
            } else {
                return Response::notFound();
            }
        }
        $this->_setApprovalStatusOnPublish($list);
        $list->save();
        return Response::json(array(
            'success' => 1,
            'status'  => $list->status,
            'list' => $list,
            'viewListUrl'   =>  ListHelpers::viewListUrl($list),
            'previewListUrl'    =>  route('previewList', array('list-id' => $list->id))
        ));
    }

    public function uploadImage() {

        $fileMaxSize = Helpers::getPermittedFileMaxSize();
        $user = Auth::user();
        $targetFileRelativePath = 'media/uploads/' . $user->id .'/' . uniqid(). '.jpg';
        $targetFilePath = public_path($targetFileRelativePath);
        $targetDirname = dirname($targetFilePath);
        if(!File::exists($targetDirname)) {
            mkdir($targetDirname, 0777, true);
        }
        try {
            $file = Input::file('file');
            $remoteImageUrl = Input::get('remoteUrl');
            if(empty($_FILES) && !$remoteImageUrl) {
                /*If the file exceeds post_max_size, no exception is thrown, but the POST data will be empty.
                See this: http://stackoverflow.com/questions/2133652/how-to-gracefully-handle-files-that-exceed-phps-post-max-size*/
                return Response::json('invalidOrBig', 400);
            }
            $mimeType = '';
            if($file) {
                if($file->getSize() > ($fileMaxSize * 1000000)) {
                    return Response::json('tooBig', 400);
                }
                $mimeType = $file->getMimeType();
            } else if($remoteImageUrl){
                $remoteImageUrl = Helpers::urlWithHttp($remoteImageUrl);
                @set_time_limit( 60 );
                $fileContents = @file_get_contents($remoteImageUrl);
                if(pathinfo($remoteImageUrl, PATHINFO_EXTENSION) == 'gif') {
                    $mimeType = 'image/gif';
                }
            }
            if($mimeType == 'image/gif') {
                $targetFilePath = str_replace('.jpg', '.gif', $targetFilePath);
                $targetFileRelativePath = str_replace('.jpg', '.gif', $targetFileRelativePath);
                if(!empty($file))
                    File::copy($file, $targetFilePath);
                else
                    File::put($targetFilePath, $fileContents);
            } else {
                $image = Image::make(!empty($file) ? $file : $fileContents);
                self::resizeListImage($image);
                $image->save($targetFilePath);
            }
        } catch(Intervention\Image\Exception\NotReadableException $e) {
            return Response::make(__('uploadedImageInvalid'), 400);
        } catch(Intervention\Image\Exception\NotWritableException $e) {
            return Response::make('Error saving image! Permission issue! Contact site admin');
        } catch(Exception $e) {
            Log::error($e->getMessage());
            return Response::make('Error uploading image!', 400);
        }
        return Response::make($targetFileRelativePath, 200);
    }

    public function viewListsOfTag($tagSlug) {
        $matchingTag = Conner\Tagging\Tag::where('slug', $tagSlug)->get();
        if(!$matchingTag->count()) {
            return Response::notFound();
        }
        $matchingTag = $matchingTag[0];
        return $this->index(['stream' => 'ofTag', 'ofTag' => $matchingTag->name]);
    }

    public function search() {
        $query = Input::get('q');
        return $this->index(['stream' => 'search', 'query' => $query]);
    }

    public static function getPermittedFileMaxSize(){
        $file_max = ini_get('upload_max_filesize');
        $file_max_str_leng = strlen($file_max);
        $file_max_meassure_unit = substr($file_max,$file_max_str_leng - 1,1);
        $file_max_meassure_unit = $file_max_meassure_unit == 'K' ? 'kb' : ($file_max_meassure_unit == 'M' ? 'mb' : ($file_max_meassure_unit == 'G' ? 'gb' : 'unidades'));
        $file_max = substr($file_max,0,$file_max_str_leng - 1);
        $file_max = intval($file_max);

        $config = Config::get('siteConfig');
        if(!empty($config['list']['fileUploadMaxSize'])) {
            $maxSizeInConfig = floatval($config['list']['fileUploadMaxSize']);
            if($maxSizeInConfig < $file_max)
                $file_max = $maxSizeInConfig;
        }
        return $file_max;
    }
    public static function saveThumbnails($list) {
        function saveThumbnail($imagePath, $width, $height) {
            $thumbPath = ListHelpers::getListThumbPathFromImage($imagePath);
            try {
                Image::make($imagePath)->fit($width, $height)->save($thumbPath);
            } catch(Intervention\Image\Exception\NotReadableException $e) {
                throw(new Exception('Error saving thumbnail! Invalid image!'));
            } catch(Intervention\Image\Exception\NotWritableException $e) {
                throw(new Exception('Error saving thumbnail! Permission issue! Contact site admin'));
            }
        }

        if(file_exists(public_path(!$list->image))) {
            saveThumbnail(public_path($list->image), 400, 210);
        } else {
            throw(new Exception('Banner image of the list not found. Please add an image!'));
        }
    }

    public static function saveOgImage($list) {
        $imagePath = public_path($list->image);
        $ogImagePath = ListHelpers::getListOGPathFromImage($imagePath);
        try {
            $image = self::getOgImageObject($list);
            $image->save($ogImagePath);
        } catch(Intervention\Image\Exception\NotReadableException $e) {
            throw(new Exception('Error saving thumbnail! Invalid image!'));
        } catch(Intervention\Image\Exception\NotWritableException $e) {
            throw(new Exception('Error saving thumbnail! Permission issue! Contact site admin'));
        }
    }

    public static function getOgImageObject($list) {
        $imagePath = public_path($list->image);
        $image = Image::make($imagePath)->fit(self::OG_IMAGE_WIDTH, self::OG_IMAGE_HEIGHT);
        self::addOgImageUserCredits($image, $list);
        return $image;
    }

    public static function addOgImageUserCredits($imageObject, $list) {
        $config = Config::get('siteConfig');
        //If addUserPicAndNameOnListOG is disabled, skip
        if(!isset($config['list']['addUserPicAndNameOnListOG'])) {
            return;
        } else if($config['list']['addUserPicAndNameOnListOG'] !== true && $config['list']['addUserPicAndNameOnListOG'] != "true") {
            return;
        }

        $creator = $list->creator;
        $userImage = $creator->photo;
        self::processImageWithCredits($imageObject, $userImage, $creator->name);
    }

    /*
     * Resize an image to be added in the list to a maximum predefined width
     * @param Intervention\Image\Image $image
     */
    public static function resizeListImage($image) {
        // resize the image to a specific width and constrain aspect ratio (auto height)
        $image->resize(self::IMAGE_MAX_WIDTH, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
    }

    public static function processImageWithCredits($imageObject, $userImage, $name) {
        $config = Config::get('siteConfig');

        //If user image is not present, skip
        if(!$userImage)
            return;
        try {
            $userImageContent = file_get_contents($userImage);
        } catch(Exception $e) {
            Log::warning($e->getMessage());
            return false;
        }

        $xPos = 50;
        $yPos = self::OG_IMAGE_HEIGHT-100;
        $userImgWidth = 80;
        $userImgHeight = 80;
        $bgMaskMargin = 20;


        $nameRectangleColor = !empty($config['list']['listImageTextBgColor']) ? $config['list']['listImageTextBgColor'] : '#111111';
        $nameTextColor = !empty($config['list']['listImageTextColor']) ? $config['list']['listImageTextColor'] : '#ffffff';

        $imageObject->rectangle(0, $yPos + $bgMaskMargin, self::OG_IMAGE_WIDTH, $yPos + $userImgHeight - $bgMaskMargin, function ($draw) use($nameRectangleColor) {
            $draw->background($nameRectangleColor);
        });

        $userImageObject = \Intervention\Image\Facades\Image::make($userImageContent);
        $userImageObject->fit($userImgWidth, $userImgWidth);
        $imageObject->insert($userImageObject, null, $xPos, $yPos);
        $fontFile = !empty($config['list']['listImageTextFont']) ? $config['list']['listImageTextFont'] : 5;

        $fontSize = !empty($config['list']['listImageTextFontSize']) ? intval($config['list']['listImageTextFontSize']) : 40;

        $imageObject->text($name, $xPos + $userImgWidth + 20, $yPos + intval($userImgHeight/2), function($font) use($fontFile, $fontSize, $nameTextColor){
            $font->file($fontFile);
            $font->size($fontSize);
            $font->color($nameTextColor);
            $font->align('left');
            $font->valign('middle');
        });

    }

    public static function _getJustSavedListEditurl($list) {
        return route('editList', array('listId' => $list->id, self::JUST_SAVED_LIST_PARAM => true));
    }
    public function _setApprovalStatusOnSave($list) {
        //Approval settings
        $autoApprove = self::isAutoApproveEnabled();
        $autoApproveUpdates = self::isAutoApproveUpdatesEnabled();

        if(!$list->exists) {
            //Is a new list
            $list->markAsNotSubmitted();
        } else {
            //Existing list - updating
            /*//Keeps the list as approved and saves the changes separately for approval.
             * if($list->isApproved()) {
                //Already approved list
                ($autoApprove || $autoApproveUpdates) ? $list->markAsApproved() : $list->markAsSubmitted();
            }*/
        }
    }

    public function _setApprovalStatusOnPublish($list) {

        //Approval settings
        $autoApprove = self::isAutoApproveEnabled();
        if($autoApprove) {
            //Already approved list
            $list->markAsApproved();
        } else {
            $list->markAsSubmitted();
        }
    }

    public static function isAutoApproveEnabled() {
        $user = Auth::user();
        //If auto approve enabled for this particular user, respect it
        if($user->isAutoApproveEnabled()) {
            return true;
        }
        $listConfig = Config::get('siteConfig')['list'];
        return ($listConfig['autoApprove'] === true || $listConfig['autoApprove'] == 'true');
    }

    public static function isAutoApproveUpdatesEnabled() {
        $user = Auth::user();
        //If auto approve enabled for this particular user, respect it
        if($user->isAutoApproveEnabled()) {
            return true;
        }
        $listConfig = Config::get('siteConfig')['list'];
        return ($listConfig['autoApproveUpdates'] === true || $listConfig['autoApproveUpdates'] == 'true');
    }

    public function _ensurePermission($list) {
        $admin = App::make('loggedInAdmin');
        if($admin) {
            //Is a admin -  he can edit anyone's lists. let him go on.
            return;
        }
        $user = Auth::user();
        if($list->creator_user_id != $user->id) {
            throw(new PermissionDeniedException("You are not allowed to edit this list"));
        }
    }

    public static function _submitListForApproval($list) {
        $list->markAsSubmitted();
        $list->save();
    }

    public static function touchUpLists(&$lists) {
        /*foreach ($lists as $key => $list) {
            self::touchUpList($lists[$key]);
        }*/
    }

    public static function touchUpList(&$list) {
        /*$list->image = !$list->image ? : asset($list->image);
        $ogImages = new stdClass();
        foreach ($list->ogImages as $key => $image) {
            $ogImages->$key = !$list->ogImages->$key ? : asset($list->ogImages->$key);
        }
        $list->ogImages = json_encode($ogImages);
        return $list;*/
    }

    public static function getPerPageLimit() {
        $siteConfig = Config::get('siteConfig');
        return $siteConfig['list']['perPageLimit'];
    }

    public static function getListQueryStreamOptions($options) {
        $loadListOptions = [];
        switch($options['stream']) {
            case "popular":
                $loadListOptions['order_by'] = 'views';
                $loadListOptions['order_by_type'] = 'desc';
                break;
            case "latest":
                $loadListOptions['order_by'] = 'created_at';
                $loadListOptions['order_by_type'] = 'desc';
                break;
            case "random":
                $loadListOptions['order_by'] = DB::raw('rand()');
                $loadListOptions['order_by_type'] = 'desc';
                break;
            case "ofTag":
                if(!empty($options['ofTag']))
                    $loadListOptions['ofTag'] = $options['ofTag'];
                break;
            case "search":
                if(!empty($options['query']))
                    $loadListOptions['search'] = $options['query'];
                break;
        }
        return $loadListOptions;
    }

    /**
     * @param $listData
     * @param $list
     */
    public static function addListData($listData, $list)
    {
        $excludeKeys = ['created_at', 'updated_at', 'id', 'creator_user_id', 'views', 'status', 'tags', 'pendingChanges'];
        foreach ($listData as $key => $val) {
            if (in_array($key, $excludeKeys))
                continue;
            $list->$key = is_array($listData[$key]) ? json_encode($listData[$key]) : $listData[$key];
        }
    }
}