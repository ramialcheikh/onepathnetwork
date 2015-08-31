<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 08/08/15
 * Time: 7:16 AM
 */

class AdminBaseController extends BaseController{
    public static function populateView() {
        $itemsAwaitingApproval = ViralList::where('status', 'awaiting_approval')->count();
        $itemsAwaitingChangesApproval   =   ViralListChanges::all()->count();
        View::share(array(
            'itemsAwaitingApproval' => $itemsAwaitingApproval + $itemsAwaitingChangesApproval
        ));
    }
}