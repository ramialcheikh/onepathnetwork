<?php

class HomeController extends BaseController {

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/

	public function index()
	{
		ListController::_loadLists(['limit' => ListController::getPerPageLimit()]);

		$pageTitle = Config::get('siteConfig')['main']['siteTitle'];
        $pageDescription = Config::get('siteConfig')['main']['siteDescription'];

        $ogTitle = $pageTitle;
        $ogDescription = $pageDescription;
        $ogImage = '';
        try {
            $ogTitle = Config::get('siteConfig')['main']['ogData']['siteOgTitle'];
            $ogDescription = Config::get('siteConfig')['main']['ogData']['siteOgDescription'];
            $ogImage = asset(Config::get('siteConfig')['main']['ogData']['siteOgImage']);
        } catch(Exception $e) {

        }
		return View::make('home')->with(array(
			'title' => $pageTitle,
			'ogTitle' => $ogTitle,
			'description' => $pageDescription,
			'ogDescription' => $ogDescription,
            'ogImage'  =>  $ogImage
		));
	}

}
