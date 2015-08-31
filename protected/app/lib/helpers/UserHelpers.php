<?php
class UserHelpers {
    public static function getUrlString($string) {

        return Helpers::slug($string);

    }
    public static function viewUserProfileUrlParams($user){
        return array('nameString' => self::getUrlString($user->name), 'id' => $user->id);
    }

    public static function userProfileUrl($user){
        $viewUserProfileUrlParams = self::viewUserProfileUrlParams($user);
        $url = route('userProfile', $viewUserProfileUrlParams);
        return $url;
    }

    public static function getSquareProfilePic($user, $size=50) {
        if(empty($user->photo))
            return asset('images/profile-pic.png');
        if(strpos($user->photo, 'graph.facebook.com'))
            return $user->photo . '&width=' . $size . '&height=' . $size;
        return asset($user->photo);
    }
}