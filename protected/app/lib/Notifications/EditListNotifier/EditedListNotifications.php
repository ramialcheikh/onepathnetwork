<?php
namespace Notifications\EditListNotifier;

class EditedListNotifications {
    public static $listEditNotificationMailMessageTemplateKey = 'listEditNotificationMailTemplate';
    public static $listEditNotificationMailSubjectTemplateKey = 'listEditNotificationMailSubjectTemplate';

    public $newList;
    public $newListCreator;
    public $originalList;
    public $originalListCreator;
    public function __construct($newList) {
        $this->newList = $newList;
        $this->readListData();
    }

    /*
     * Notify the creator of original list
     */
    public function notify() {
        if($this->isCreatedFromAnotherList()) {
            $notificationMessageTemplate = $this->getNotificationMessageTemplate();
            $notificationSubjectTemplate = $this->getNotificationSubjectTemplate();
            $notificationData = $this->getNotificationData();
            $notification = new \Notifications\Notification($notificationMessageTemplate, $notificationSubjectTemplate, $notificationData);
            if($this->isMailNotificationEnabled()) {
                $this->sendMailNotification($notification);
            }
        }
    }

    public function getNotificationMessageTemplate() {
        $config = \Config::get('siteConfig');
        if(empty($config['list'][self::$listEditNotificationMailMessageTemplateKey]))
            return '';
        return $config['list'][self::$listEditNotificationMailMessageTemplateKey];
    }

    public function getNotificationSubjectTemplate() {
        $config = \Config::get('siteConfig');
        if(empty($config['list'][self::$listEditNotificationMailSubjectTemplateKey]))
            return '';
        return $config['list'][self::$listEditNotificationMailSubjectTemplateKey];
    }

    public function getNotificationData() {
        $newListCreator = $this->newListCreator;
        $originalList = $this->originalList;
        $originalListCreator = $this->originalListCreator;
        return [
            'RecipientName' =>  $originalListCreator->name,
            'NewListLink'   =>  '<a href="'. \ListHelpers::viewListUrl($this->newList) . '">'. $this->newList->title .'</a>',
            'OriginalListTitle'    => $originalList->title,
            'OriginalListLink'   =>  '<a href="'. \ListHelpers::viewListUrl($originalList) . '">'. $originalList->title .'</a>',
            'NewListCreatorName'    =>  $newListCreator->name
            //'NewListCreatorProfileLink'    =>  $newListCreator
        ];
        /*
         *
         * TODO: Add NewListCreatorProfileLink after creating user profile pages.
         *
         * */
    }

    public function readListData() {
        $this->newListCreator = $this->newList->creator;
        $this->originalList = $this->newList->createdFromList;
        if($this->originalList)
            $this->originalListCreator = $this->originalList->creator;
    }

    public function isCreatedFromAnotherList() {
        return !!$this->newList->created_from_list_id;
    }

    public function sendMailNotification($notification) {
        $notifier = new \Notifications\UserMailNotifier();
        $notifier->notifyUser($this->originalListCreator, $notification);
    }

    public function isMailNotificationEnabled() {
        $config = \Config::get('siteConfig');
        $enableListEditNotification = $config['list']['enableListEditNotification'];
        return ($enableListEditNotification === true || $enableListEditNotification == 'true');
    }
}
