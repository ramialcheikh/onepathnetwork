<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 26/07/15
 * Time: 5:01 PM
 */

namespace Notifications;

class UserMailNotifier extends UserNotifier{

    public function sendNotification($user, $message, $subject) {
        if(!empty($user->email)) {
            \Mail::send('emails.raw', ['msg' => $message], function($message) use ($user, $subject){
                $message->to($user->email, $user->name)->subject($subject);
            });
        }
    }
}