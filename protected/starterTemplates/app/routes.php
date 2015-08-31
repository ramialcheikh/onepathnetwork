<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as' => 'home', 'uses' => 'HomeController@index'));

Route::get('@@plural@@', array('as' => '@@plural@@', 'uses' => '@@singular-pascalCase@@Controller@index'));
Route::get('@@plural@@/popular', array('as' => 'popular@@plural-pascalCase@@', 'uses' => '@@singular-pascalCase@@Controller@popular'));
Route::get('@@plural@@/{nameString}/{@@singular@@Id}', array('as' => 'view@@singular-pascalCase@@', 'uses' => '@@singular-pascalCase@@Controller@view@@singular-pascalCase@@'));
Route::get('pages/{nameString}.html', array('as' => 'viewPage', 'uses' => 'PageController@viewPage'));
Route::get('category/{slug}', array('as' => 'category', 'uses' => '@@singular-pascalCase@@Controller@category'));

Route::filter('adminAuth', function()
{
    $admin = Session::get('admin');
    /*if (!$admin && !Input::get('logmein'))
    {
        return Response::notFound();
    } else */

    if(!$admin) {
        if(Request::ajax()) {
            return Response::make('You have been logged out or your session has expired. Please login on another tab and try again.<br><br><a target="_blank" href="'. route('adminLogin') .'" class="btn btn-success">Login again</a></a>', 400);
        } else{
            return Redirect::route('adminLogin', ['redirect' => urlencode(Request::path())]);
        }
    }
});

Route::match(array('get', 'post'), 'admin/login', array('as' => 'adminLogin', 'uses' => 'AdminController@login'));
Route::get('admin/logout', array('as' => 'adminLogout', 'uses' => 'AdminController@logout'));
Route::group(array('before' => 'adminAuth'), function() {
    //Admin home
    Route::get('admin', array('as' => 'admin', 'uses' => 'AdminController@index'));

    //Item management
    Route::get('admin/@@plural@@/view', array('as' => 'adminView@@plural-pascalCase@@', 'uses' => 'Admin@@plural-pascalCase@@Controller@list@@plural-pascalCase@@'));
    Route::match(array('GET', 'POST'), 'admin/@@plural@@/create', array('as' => 'adminCreate@@singular-pascalCase@@', 'uses' => 'Admin@@plural-pascalCase@@Controller@createEdit'));
    Route::match(array('POST'), 'admin/@@plural@@/delete', array('as' => 'adminDelete@@singular-pascalCase@@', 'uses' => 'Admin@@plural-pascalCase@@Controller@delete'));

    //Pages
    Route::get('admin/pages/view', array('as' => 'adminViewPages', 'uses' => 'AdminPagesController@listPages'));
    Route::match(array('GET', 'POST'), 'admin/pages/create', array('as' => 'adminCreatePage', 'uses' => 'AdminPagesController@createEdit'));
    Route::match(array('GET', 'POST'), 'admin/pages/delete', array('as' => 'adminDeletePage', 'uses' => 'AdminPagesController@delete'));

    //Config
    Route::match(array('GET', 'POST'), 'admin/config', array('as' => 'adminConfig', 'uses' => 'AdminConfigController@index'));
    Route::match(array('GET', 'POST'), 'admin/config/widgets', array('as' => 'adminConfigWidgets', 'uses' => 'AdminConfigController@widgets'));
    Route::match(array('GET', 'POST'), 'admin/config/languages', array('as' => 'adminConfigLanuages', 'uses' => 'AdminConfigController@languages'));
	Route::match(array('GET', 'POST'), 'admin/config/@@singular@@', array('as' => 'adminConfig@@singular-pascalCase@@', 'uses' => 'AdminConfigController@@@singular@@Config'));

    //Change Password
    Route::match(array('GET', 'POST'), 'admin/change-password', array('as' => 'adminChangePassword', 'uses' => 'AdminController@changePassword'));

    //Users
    Route::match(array('GET', 'POST'), 'admin/users/', array('as' => 'adminUsersHome', 'uses' => 'AdminUsersController@index'));

    //Categories
    Route::match(array('GET', 'POST'), 'admin/categories', array('as' => 'adminCategories', 'uses' => 'AdminCategoriesController@view'));
    Route::match(array('GET', 'POST', 'PATCH', 'DELETE'), 'admin/categories/addEdit', array('as' => 'adminCategoriesAddEdit', 'uses' => 'AdminCategoriesController@addEdit'));

    //Update
    Route::get('admin/update', array('as' => 'update', 'uses' => 'UpdateController@index'));
});



//Media manager
Route::group(array(), function()
{
    \Route::get('media', 'W3G\MediaManager\MediaManagerController@showStandalone');
    \Route::any('media/connector', array('as' => 'mediaConnector', 'uses' => 'W3G\MediaManager\MediaManagerController@connector'));
});

Route::get('/login', array('as' => 'login', 'uses' => 'UserController@login'));
Route::get('login/fb', array('as' => 'loginWithFb', 'uses' => 'UserController@loginWithFb'));

Route::get('logout', array('as' => 'logout', 'uses' => 'UserController@logout'));

//404 macro
Response::macro('notFound', function($value = null)
{
    @@singular-pascalCase@@Controller::_load@@plural-pascalCase@@();
    return Response::view('errors.404', array('errorMsg' => strtoupper($value)), 404);
});

App::missing(function($exception)
{
    @@singular-pascalCase@@Controller::_load@@plural-pascalCase@@();
    return Response::view('errors.404', array('errorMsg' => strtoupper($exception->getMessage())), 404);
});