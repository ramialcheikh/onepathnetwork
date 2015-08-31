<?php

class @@singular-pascalCase@@Controller extends BaseController {
    const DEFAULT_ITEMS_LIMIT = 50;
    const PER_PAGE = 10;
    const DEFAULT_ORDER_BY = 'created_at';
    const DEFAULT_ORDER_BY_TYPE = 'desc';
    public static function increment@@singular-pascalCase@@Stats($@@singular@@, $type) {
        switch($type) {
            case 'attempt':
                $statColumnName = 'attempts';
                $methodName = 'attemptedUsers';
                break;
            case 'completion':
                $statColumnName = 'completions';
                $methodName = 'completedUsers';
                break;
            case 'like':
                $statColumnName = 'likes';
                $methodName = 'likedUsers';
                break;
            case 'share':
                $statColumnName = 'shares';
                $methodName = 'sharedUsers';
                break;
            case 'comment':
                $statColumnName = 'comments';
                $methodName = 'commentedUsers';
                break;
            default:
                $statColumnName = '';
                $methodName = '';
        }

        if(empty($statColumnName) || empty($methodName)) {
            throw new Exception('Invalid activity');
        }
        $activityCount = $@@singular@@->$methodName()->count();

        $@@singular@@Stat = $@@singular@@->stats ? $@@singular@@->stats : new @@model@@Stats();
        $@@singular@@Stat->$statColumnName = $activityCount;
        $@@singular@@->stats()->save($@@singular@@Stat);
        return true;
    }
    public function index($options = array()) {

        $load@@singular-pascalCase@@Options = ['limit' => self::PER_PAGE];
        $stream = 'latest';
        if(isset($options['stream'])) {
            if($options['stream'] == "popular"){
                $load@@singular-pascalCase@@Options['order_by'] = '@@singular@@_stats.attempts';
                $load@@singular-pascalCase@@Options['order_by_type'] = 'desc';
            }
            $stream = $options['stream'];
        }

        if(isset($options['category'])) {
            $category = $options['category'];
            $load@@singular-pascalCase@@Options['categoryId'] = $category->id;
            View::share('categoryName', $category->name);
        }

        self::_load@@plural-pascalCase@@($load@@singular-pascalCase@@Options);
        $titleLangKey = ($stream == "latest") ? 'latest@@plural-pascalCase@@' : (($stream == "popular") ? 'popular@@plural-pascalCase@@' : '@@plural@@');
        if(!empty($category)) {
            $titleLangKey = $category->name;
        }
        $pageTitle = __($titleLangKey) . ' | ' . Config::get('siteConfig')['main']['siteName'];
        $pageDescription = __('hereAreSome@@plural-pascalCase@@');
        return View::make('@@plural@@/index')->with(array(
            'currentPage' => '@@plural@@Index',
            'title' => $pageTitle,
            'ogTitle' => $pageTitle,
            'description' => $pageDescription,
            'ogDescription' => $pageDescription,
            'isStream' . ucfirst($stream) => true,
            'mainHeading' => __($titleLangKey)
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
        $load@@plural-pascalCase@@Options = array();
        $load@@plural-pascalCase@@Options['limit'] = Input::get('limit');
        self::_load@@plural-pascalCase@@($load@@plural-pascalCase@@Options);

        $pageTitle = __('@@plural@@') . ' | ' . Config::get('siteConfig')['main']['siteName'];
        $pageDescription = __('hereAreSome@@plural-pascalCase@@');
        return View::make('@@plural@@/iframeList')->with(array(
            'currentPage' => '@@plural@@Index',
            'title' => $pageTitle,
            'ogTitle' => $pageTitle,
            'description' => $pageDescription,
            'ogDescription' => $pageDescription
        ));
    }

    public static function _get@@plural-pascalCase@@($options = array()) {
        $orderBy = !empty($options['order_by']) ? $options['order_by'] : self::DEFAULT_ORDER_BY;
        $orderByType = !empty($options['order_by_type']) ? $options['order_by_type'] : self::DEFAULT_ORDER_BY_TYPE;

        $@@plural@@Query = @@model@@::where('active', '=', true);
        $@@plural@@Query->leftJoin('@@singular@@_stats', '@@plural@@.id', '=', '@@singular@@_stats.@@singular@@_id');
        $@@plural@@Query->orderBy($orderBy, $orderByType);
        $limit = isset($options['limit']) ? $options['limit'] : self::DEFAULT_ITEMS_LIMIT;
        if(!empty($options['exclude'])) {
            $@@plural@@Query->whereNotIn('id', array($options['exclude']));
        }
        if(!empty($options['categoryId'])) {
            $@@plural@@Query->where('category', $options['categoryId']);
        }
        //dd($@@plural@@Query->toSql());
        $@@plural@@ = $@@plural@@Query->simplePaginate($limit);
        foreach($@@plural@@ as $key => $@@singular@@) {
            $@@plural@@[$key] = $@@singular@@;
        }
        self::touchUp@@plural-pascalCase@@($@@plural@@);
        //dd($@@plural@@->toArray());
        return $@@plural@@;
    }

    public static function _load@@plural-pascalCase@@($options = array()) {
        $get@@plural-pascalCase@@Options = $options;
        if(!empty($options['related_to'])) {
            $get@@plural-pascalCase@@Options['exclude'] = $options['related_to'];
        }

        $@@plural@@ = self::_get@@plural-pascalCase@@($get@@plural-pascalCase@@Options);
        View::share('@@plural@@', $@@plural@@);
    }

    public function getRouteParams($@@singular@@) {
        return @@singular-pascalCase@@Helpers::view@@singular-pascalCase@@UrlParams($@@singular@@);
    }

    public static function getView@@singular-pascalCase@@Url($@@singular@@, $result = null) {
        return @@singular-pascalCase@@Helpers::view@@singular-pascalCase@@Url($@@singular@@, $result);
    }

    public function view@@singular-pascalCase@@($nameString, $@@singular@@Id = null, $resultId = null) {
        try {
            $sharedUserId = Input::get('user-fb-id');
            $@@singular@@ = @@model@@::findOrFail($@@singular@@Id);
            self::_load@@plural-pascalCase@@(array('related_to' => $@@singular@@Id));
            $@@singular@@->view@@singular-pascalCase@@Url = self::getView@@singular-pascalCase@@Url($@@singular@@);
            if(!$@@singular@@->active) {
                View::share('@@singular@@Inactive', true);
                if(!Session::get('admin')) {
                    App::abort(404);
                }
            }
            $ogTitle = $@@singular@@->topic;
            if($resultId) {
                foreach($@@singular@@->results as $res) {
                    if($res->id == $resultId) {
                        $result = $res;
                    }
                }
                if(!empty($result)) {
                    $ogImage = @$@@singular@@->ogImages->$resultId;
                    if(!empty($result->title)) {
                        $ogTitle = __('iGot') . ' "' . $result->title . '" | ' . $@@singular@@->topic;;
                    }
                }
                View::share('@@singular@@ResultId', $resultId);
            }
            $ogUrl = $canonicalUrl = !isset($result) ? $@@singular@@->view@@singular-pascalCase@@Url : self::getView@@singular-pascalCase@@Url($@@singular@@, $result);
            $ogImage = URL::asset(!empty($ogImage) ? $ogImage : @$@@singular@@->ogImages->main);
            return View::make('@@plural@@/view@@singular-pascalCase@@')->with(array(
                '@@singular@@' => $@@singular@@,
                'view@@singular-pascalCase@@Url' => self::getView@@singular-pascalCase@@Url($@@singular@@),
                'currentPage' => 'view@@singular-pascalCase@@',
                'sharedUserId' => $sharedUserId,
                'ogImage' => $ogImage,
                'ogTitle' => $ogTitle,
                'ogUrl' => $ogUrl,
                'title' => $@@singular@@->topic,
                'ogDescription' => $@@singular@@->description,
                'description' => $@@singular@@->description,
                'canonicalUrl' => $canonicalUrl
            ));
        }catch(Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Response::notFound('@@singular-pascalCase@@ not found');
        }
    }

    public static function touchUp@@plural-pascalCase@@(&$@@plural@@) {
        /*foreach ($@@plural@@ as $key => $@@singular@@) {
            self::touchUp@@singular-pascalCase@@($@@plural@@[$key]);
        }*/
    }

    public static function touchUp@@singular-pascalCase@@(&$@@singular@@) {
        /*$@@singular@@->image = !$@@singular@@->image ? : asset($@@singular@@->image);
        $ogImages = new stdClass();
        foreach ($@@singular@@->ogImages as $key => $image) {
            $ogImages->$key = !$@@singular@@->ogImages->$key ? : asset($@@singular@@->ogImages->$key);
        }
        $@@singular@@->ogImages = json_encode($ogImages);
        return $@@singular@@;*/
    }
}