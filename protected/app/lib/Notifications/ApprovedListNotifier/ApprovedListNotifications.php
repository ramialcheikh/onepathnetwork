<?php
namespace Notifications\ApprovedListNotifier;

class ApprovedListNotifications {
    public static $listApprovedNotificationMailMessageTemplateKey = 'listApprovedNotificationMailTemplate';
    public static $listApprovedNotificationMailSubjectTemplateKey = 'listApprovedNotificationMailSubjectTemplate';

    public $list;
    public $creator;
    public function __construct($list) {
        $this->list = $list;
        $this->readListData();
    }

    /*
     * Notify the creator of the list
     */
    public function notify() {
        $notificationMessageTemplate = $this->getNotificationMessageTemplate();
        $notificationSubjectTemplate = $this->getNotificationSubjectTemplate();
        $notificationData = $this->getNotificationData();

        $notification = new \Notifications\Notification($notificationMessageTemplate, $notificationSubjectTemplate, $notificationData);
        if($this->isMailNotificationEnabled()) {
            $this->sendMailNotification($notification);
        }
    }

    public function getNotificationMessageTemplate() {
        $config = \Config::get('siteConfig');
        if(empty($config['list'][self::$listApprovedNotificationMailMessageTemplateKey]))
            return '';
        return $config['list'][self::$listApprovedNotificationMailMessageTemplateKey];
    }

    public function getNotificationSubjectTemplate() {
        $config = \Config::get('siteConfig');
        if(empty($config['list'][self::$listApprovedNotificationMailSubjectTemplateKey]))
            return '';
        return $config['list'][self::$listApprovedNotificationMailSubjectTemplateKey];
    }

    public function getNotificationData() {
        $creator = $this->creator;
        return [
            'RecipientName' =>  $creator->name,
            'ListTitle'    => $this->list->title,
            'ListLink'   =>  '<a href="'. \ListHelpers::viewListUrl($this->list) . '">'. $this->list->title .'</a>',
            'ListUrl'   =>  \ListHelpers::viewListUrl($this->list)
        ];
    }

    public function readListData() {
        $this->creator = $this->list->creator;
    }

    public function sendMailNotification($notification) {
        $notifier = new \Notifications\UserMailNotifier();
        $notifier->notifyUser($this->creator, $notification);
    }

    public function isMailNotificationEnabled() {
        $config = \Config::get('siteConfig');
        $enableListApprovedNotification = isset($config['list']['enableListApprovedNotification']) ? $config['list']['enableListApprovedNotification'] : false;
        return ($enableListApprovedNotification === true || $enableListApprovedNotification == 'true');
    }
}
