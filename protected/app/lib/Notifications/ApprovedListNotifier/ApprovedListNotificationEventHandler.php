<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 26/07/15
 * Time: 5:04 PM
 */

namespace Notifications\ApprovedListNotifier;

class ApprovedListNotificationEventHandler {

    /*
     * Subscribe to the events and attach a handler to process sending notification
     */
    public static function enable()
    {
        \Event::listen('list:approved-by-admin', function($list) {
            $approvedListNotification = new ApprovedListNotifications($list);
            $approvedListNotification->notify();
        });
    }
}