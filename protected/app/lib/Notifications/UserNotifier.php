<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 26/07/15
 * Time: 5:00 PM
 */

namespace Notifications;

abstract class UserNotifier {

    public function notifyUser($user, $notification) {
        $message = $notification->renderMessage();
        $subject = $notification->renderSubject();
        $this->sendNotification($user, $message, $subject);
    }

    public abstract function sendNotification($user, $message, $subject);
}