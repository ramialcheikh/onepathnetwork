<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

    public static function setupShortCodes() {
        App::singleton('shortCodeEngine', function() {
            $shortCodeEngine = new ShortCodeEngine();
            return $shortCodeEngine;
        });
        $shortCodeEngine = App::make('shortCodeEngine');
        $shortCodes = require(app_path('lib/shortcodes.php'));
        $shortCodeEngine->loadShortCodes($shortCodes);
    }

    public static function loadCategories() {
        $categories = new \Illuminate\Database\Eloquent\Collection();
        try {
            $categories = Category::all();
        } catch(Exception $e) {
            //Discard query exception.. categories table not found.. discard
        }
        $categoriesMap = [];
        if($categories->count()) {
            foreach ($categories as $category) {
                $categoriesMap[$category->id] = $category->name;
            }
        }

        View::share('categories', $categories);
        View::share('categoriesMap', $categoriesMap);
    }

}
