<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 27/07/15
 * Time: 6:15 PM
 */

class CustomEmailSettings {

    public static function loadEmailConfigFromDB() {
        try {
            $config = Config::get('siteConfig');
            $laravelMailConfig = Config::get('mail');
            if(empty($config['email']))
                return;
            $emailConfig = $config['email'];
            switch($emailConfig['driver']) {
                case 'smtp':
                    $laravelMailConfig['driver'] = 'smtp';
                    break;
                case 'PHP-mail-function':
                    $laravelMailConfig['driver'] = 'mail';
                    break;
            }

            $laravelMailConfig['from'] = [
                'address'   =>  $emailConfig['fromEmail'],
                'name'      =>  $emailConfig['fromName']
            ];
            
            if($emailConfig['driver'] == 'smtp') {
                $laravelMailConfig['host'] = $emailConfig['smtpHost'];
                $laravelMailConfig['port'] = $emailConfig['smtpPort'];
                $laravelMailConfig['encryption'] = !empty($emailConfig['smtpEncryption']) ? $emailConfig['smtpEncryption'] : '';
                $laravelMailConfig['username'] = $emailConfig['smtpUsername'];
                $laravelMailConfig['password'] = $emailConfig['smtpPassword'];
            }
            Config::set('mail', $laravelMailConfig);
        } catch(Exception $e) {
            Log::error('Error loading custom email config from database: ' . $e->getMessage());
        }
    }
}