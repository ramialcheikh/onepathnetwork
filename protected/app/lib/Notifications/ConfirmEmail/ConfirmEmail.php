<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 03/08/15
 * Time: 8:55 PM
 */

namespace Notifications;


class ConfirmEmail {

    public static $confirmationMailMessageTemplateKey = 'confirmationMailMessageTemplate';
    public static $confirmationMailSubjectKey = 'confirmationMailSubject';
    public $user;
    public $confirmationCode;
    public function __construct($user, $confirmationCode) {
        $this->user = $user;
        $this->confirmationCode = $confirmationCode;
    }
    public function send() {
        $notificationMessageTemplate = $this->getNotificationMessageTemplate();
        $notificationSubjectTemplate = $this->getNotificationSubjectTemplate();
        $notificationData = $this->getNotificationData();

        $notification = new \Notifications\Notification($notificationMessageTemplate, $notificationSubjectTemplate, $notificationData);
        $this->sendMailNotification($notification);
    }

    public function getNotificationMessageTemplate() {
        $config = \Config::get('siteConfig');
        if(empty($config['email'][self::$confirmationMailMessageTemplateKey]))
            return '';
        return $config['email'][self::$confirmationMailMessageTemplateKey];
    }

    public function getNotificationSubjectTemplate() {
        $config = \Config::get('siteConfig');
        if(empty($config['email'][self::$confirmationMailSubjectKey]))
            return '';
        return $config['email'][self::$confirmationMailSubjectKey];
    }

    public function getNotificationData() {
        return([
           'ConfirmationLink'   =>  route('confirmEmail', [$this->confirmationCode])
        ]);
    }

    public function sendMailNotification($notification) {
        $notifier = new \Notifications\UserMailNotifier();
        $notifier->notifyUser($this->user, $notification);
    }
}