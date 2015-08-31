<?php

use \LaravelTranslate\Languages as Languages;

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

Route::matched(function($route, $request) {
    $isInstalling = false;
    $config = app('siteConfig');
    //If is install route, skip config check
    if($route->getName() == Config::get('app-installer::routeName')) {
        $isInstalling = true;
    }

    if(!Config::get('database') && !$isInstalling) {
        die(Response::configurationError("Seems like you are starting up! Go on. Install it! Refer to documentation for instructions.", "Installation not completed"));
    }
    //If config is empty and if the user is not running the installer, Show error
    if(!$config && !$isInstalling) {
        if(!DB::connection()) {
            return Response::configurationError("Oops! The database cant be read!", "Database Connection error");
        } else {
            return Response::configurationError("Oops! The database is not configured properly!", "Database Configuration error");
        }
    }
});

App::before(function($request)
{
	//dd($request->getPathInfo() == route(Config::get('app-installer::routeName'), [], false));
	App::singleton('siteConfig', function(){
		//Loading config
		$config = array(0);
        try {
            $configRows = SiteConfig::all();
        } catch (Exception $e) {
            //Config cant be read! DB cant be accessed or Installation not completed
            //return Response::configurationError("Please complete installation and check again.", "Installation incomplete!");
            return false;
        }
		foreach($configRows as $row) {
			$config[$row->name] = (array) json_decode($row->value, true);
		}
		return $config;
	});


    $config = app('siteConfig');
    //If config is empty, skip the rest
    if(!$config)
        return;

	Config::set('siteConfig', $config);

	$language = new Languages($config['languages']['languages'], 'en');
	//Activate Language set in DB
	if(!empty($config['languages']['activeLanguage'])) {
		$activeLanguage = $config['languages']['activeLanguage'];
	} else {
		$activeLanguage = $language->getDefaultLanguage();
	}
	$language->activateLanguage($activeLanguage);

	View::share('config', $config);
	$safeToExposeMainConfig = $config['main'];
	unset($safeToExposeMainConfig['social']['facebook']['secret']);
	View::share('mainConfigJSON', json_encode($safeToExposeMainConfig));
    if(!empty($config['@@singular@@']))
        View::share('@@singular@@ConfigJSON', json_encode($config['@@singular@@']));

	//Loading colors from config for theming
	if(!empty($config['main']['navbarColor'])) {
		View::share('navbarColor', $config['main']['navbarColor']);
	}
	if(!empty($config['main']['mainBtnColor'])) {
		View::share('mainBtnColor', $config['main']['mainBtnColor']);
	}
	if(!empty($config['main']['linkColor'])) {
		View::share('linkColor', $config['main']['linkColor']);
	}

	//Loading widgets
	$widgets = array('widgets'=> array());
	if(!empty($config['widgets']) && !empty($config['widgets']['widgets'])) {
		$widgetPlacements = (array) $config['widgets']['widgets'];
		foreach($widgetPlacements as $widgetPlacement) {
			$widgetItems = !empty($widgetPlacement['widgets']) ? $widgetPlacement['widgets'] : array();
			//Remove disabled widgets
			$widgetItems = array_filter($widgetItems, function($widgetItem){
				if(isset($widgetItem['disabled']) && ($widgetItem['disabled'] === true || $widgetItem['disabled'] == "true"))
					return false;
				return true;
			});
			$widgets[$widgetPlacement['id']] = $widgetItems;
		}
	}
	View::share('widgets', $widgets);
	if(Auth::check()) {
		View::share('userData', Auth::user());
	}
});

//Getting Categories
App::before(function($request) {
    BaseController::loadCategories();
});

App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});
