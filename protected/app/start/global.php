<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

ClassLoader::addDirectories(array(

	app_path().'/commands',
	app_path().'/controllers',
	app_path().'/models',
	app_path().'/database/seeds',
	app_path().'/lib/helpers',
	app_path().'/lib'
));

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/

//Log::useFiles(storage_path().'/logs/laravel.log');
$logFile = 'log-'.php_sapi_name().'.txt';

Log::useDailyFiles(storage_path().'/logs/'.$logFile);


/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function(Exception $exception, $code)
{
    $pathInfo = Request::getPathInfo();
    $message = $exception->getMessage() ?: 'Exception';
    Log::error("$code - $message @ $pathInfo\r\n$exception");
    if (Config::get('app.debug')) {
        return;
    }
    return Response::view('errors/500', array(), 500);
});

Response::macro('error', function($message, $title = null, $errorCode =  500)
{
    if(Request::ajax()){
        return Response::make($message, $errorCode);
    } else{
        return Response::view('errors.error', array('title' => $title, 'message' => $message), $errorCode);
    }
});

Response::macro('configurationError', function($message, $title = null, $errorCode =  500)
{
    if(Request::ajax()){
        $response = $title .'<br>' . $message;
    } else{
        $response = View::make('errors.plainError')->with(array('title' => $title, 'message' => $message));
    }
    die($response);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function()
{
	if(!empty($_COOKIE['dev'])) {
			return NULL;
	}
	return Response::view('maintenance', array(), 503);
});


/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path().'/filters.php';
require app_path().'/composers.php';

require(app_path().'/lib/helpers/translate.php');
require(app_path().'/lib/Jitheshgopan/app-installer/vendor/autoload.php');
require(app_path().'/lib/Jitheshgopan/app-installer/src/Jitheshgopan/AppInstaller/Installer.php');

use Jitheshgopan\AppInstaller\Installer;