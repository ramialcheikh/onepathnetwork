<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 26/07/15
 * Time: 5:04 PM
 */

namespace Notifications\EditListNotifier;

class EditListModelObserver {

    /*
     * Called when a new list record is saved(created or updated)
     * Notifications are sent only when the list is approved
     */
    public function saved($list)
    {
        //If the creator is already notified or if the list is not approved, skip notification
        if($list->original_creator_notified || !$list->isApproved())
            return;

        $editListNotification = new EditedListNotifications($list);
        $editListNotification->notify();
        $list->markAsOriginalCreatorNotified();
        $list->save();
    }
}