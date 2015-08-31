<?php

Route::match(array('GET', "POST"), Config::get('app-installer::route'), array(
    'as' => Config::get('app-installer::routeName'),
    'uses' => 'Jitheshgopan\AppInstaller\InstallerController@index')
);