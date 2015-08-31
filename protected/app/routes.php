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

Route::get('lists', array('as' => 'lists', 'uses' => 'ListController@index'));
Route::get('lists/popular', array('as' => 'popularLists', 'uses' => 'ListController@popular'));
Route::get('lists/{nameString}/{listId}/{item?}', array('as' => 'viewList', 'uses' => 'ListController@viewList'));
Route::get('tag/{slug}', array('as' => 'viewListsOfTag', 'uses' => 'ListController@viewListsOfTag'));
Route::get('search', array('as' => 'search', 'uses' => 'ListController@search'));

Route::get('users/confirm-email/{code}', array('as' => 'confirmEmail', 'uses' => 'UserController@confirmEmail'));
//User profile
Route::get('users/{id}/{nameString}', array('as' => 'userProfile', 'uses' => 'UserController@profile'));

Route::controller('users', 'UserController');
Route::controller('password', 'RemindersController');

Route::get('login', array('as' => 'login', 'uses' => 'UserController@getLogin'));
Route::get('login/fb', array('as' => 'loginWithFb', 'uses' => 'UserController@loginWithFb'));

Route::get('logout', array('as' => 'logout', 'uses' => 'UserController@logout'));

Route::get('sitemap.xml', array('as' => 'sitemap', 'uses'   =>  'SitemapController@index'));
Route::get('feed', array('as' => 'rssFeed', 'uses'   =>  'RssFeedController@index'));

Route::group(array('before' => 'auth'), function() {
    Route::match(array('get', 'post'), 'create', array('as' => 'createList', 'uses' => 'ListController@createEdit'));
    Route::match(array('get', 'post'), 'edit', array('as' => 'editList', 'uses' => 'ListController@createEdit'));
    Route::post('create/publish', array('as' => 'publishList', 'uses' => 'ListController@publishList'));
    Route::post('create/upload-image', array('as' => 'listUploadImage', 'uses' => 'ListController@uploadImage'));
    Route::get('preview-list', array('as' => 'previewList', 'uses' => 'ListController@previewList'));
    //My profile
    Route::get('me', array('as' => 'myProfile', 'uses' => 'UserController@myProfile'));
    Route::match(array('get', 'post'), 'me/settings', array('as' => 'myProfileSettings', 'uses' => 'UserController@myProfileSettings'));
});

Route::get('pages/{nameString}.html', array('as' => 'viewPage', 'uses' => 'PageController@viewPage'));
Route::get('category/{slug}', array('as' => 'category', 'uses' => 'ListController@category'));

$admin = Session::get('admin');
View::share('loggedInAdmin', $admin);

App::singleton('loggedInAdmin', function() use($admin) {
    return $admin;
});

Route::filter('adminAuth', function()
{
    $admin = Session::get('admin');

    //Populate view with common data for admins
    AdminBaseController::populateView();

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
    Route::get('admin/lists/view', array('as' => 'adminViewLists', 'uses' => 'AdminListsController@listLists'));
    Route::match(array('POST'), 'admin/lists/delete', array('as' => 'adminDeleteList', 'uses' => 'AdminListsController@delete'));
    Route::post('admin/lists/approve-list', array('as' => 'approveList', 'uses' => 'AdminListsController@approveList'));
    Route::post('admin/lists/disapprove-list', array('as' => 'disapproveList', 'uses' => 'AdminListsController@disapproveList'));

    //Pages
    Route::get('admin/pages/view', array('as' => 'adminViewPages', 'uses' => 'AdminPagesController@listPages'));
    Route::match(array('GET', 'POST'), 'admin/pages/create', array('as' => 'adminCreatePage', 'uses' => 'AdminPagesController@createEdit'));
    Route::match(array('GET', 'POST'), 'admin/pages/delete', array('as' => 'adminDeletePage', 'uses' => 'AdminPagesController@delete'));

    //Config
    Route::match(array('GET', 'POST'), 'admin/config', array('as' => 'adminConfig', 'uses' => 'AdminConfigController@index'));
    Route::match(array('GET', 'POST'), 'admin/config/widgets', array('as' => 'adminConfigWidgets', 'uses' => 'AdminConfigController@widgets'));
    Route::match(array('GET', 'POST'), 'admin/config/languages', array('as' => 'adminConfigLanuages', 'uses' => 'AdminConfigController@languages'));
	Route::match(array('GET', 'POST'), 'admin/config/list', array('as' => 'adminConfigList', 'uses' => 'AdminConfigController@listConfig'));
	Route::match(array('GET', 'POST'), 'admin/config/email', array('as' => 'adminConfigEmail', 'uses' => 'AdminConfigController@emailConfig'));

    //Preview OG image
    Route::match(array('GET', 'POST'), 'admin/config/preview-og-image', array('as' => 'adminPreviewOgImage', 'uses' => 'AdminConfigController@previewOgImage'));

    //Change Password
    Route::match(array('GET', 'POST'), 'admin/change-password', array('as' => 'adminChangePassword', 'uses' => 'AdminController@changePassword'));

    //Users
    Route::controller('admin/users', 'AdminUsersController');
    Route::match(array('GET', 'POST'), 'admin/users/', array('as' => 'adminUsersHome', 'uses' => 'AdminUsersController@index'));

    //Categories
    Route::match(array('GET', 'POST'), 'admin/categories', array('as' => 'adminCategories', 'uses' => 'AdminCategoriesController@view'));
    Route::match(array('GET', 'POST', 'PATCH', 'DELETE'), 'admin/categories/addEdit', array('as' => 'adminCategoriesAddEdit', 'uses' => 'AdminCategoriesController@addEdit'));

    //Update
    Route::get('admin/update', array('as' => 'update', 'uses' => 'UpdateController@index'));

    //Sitemap
    Route::controller('sitemap', 'AdminSitemapController');
});



//Media manager
Route::group(array(), function()
{
    \Route::get('media', 'W3G\MediaManager\MediaManagerController@showStandalone');
    \Route::any('media/connector', array('as' => 'mediaConnector', 'uses' => 'W3G\MediaManager\MediaManagerController@connector'));
});

//404 macro
Response::macro('notFound', function($value = null)
{
    ListController::_loadLists();
    return Response::view('errors.404', array('errorMsg' => strtoupper($value)), 404);
});

App::missing(function($exception)
{
    ListController::_loadLists();
    return Response::view('errors.404', array('errorMsg' => strtoupper($exception->getMessage())), 404);
});