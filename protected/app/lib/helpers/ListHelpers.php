<?php
//taken from wordpress
function utf8_uri_encode( $utf8_string, $length = 0 ) {
    $unicode = '';
    $values = array();
    $num_octets = 1;
    $unicode_length = 0;

    $string_length = strlen( $utf8_string );
    for ($i = 0; $i < $string_length; $i++ ) {

        $value = ord( $utf8_string[ $i ] );

        if ( $value < 128 ) {
            if ( $length && ( $unicode_length >= $length ) )
                break;
            $unicode .= chr($value);
            $unicode_length++;
        } else {
            if ( count( $values ) == 0 ) $num_octets = ( $value < 224 ) ? 2 : 3;

            $values[] = $value;

            if ( $length && ( $unicode_length + ($num_octets * 3) ) > $length )
                break;
            if ( count( $values ) == $num_octets ) {
                if ($num_octets == 3) {
                    $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
                    $unicode_length += 9;
                } else {
                    $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
                    $unicode_length += 6;
                }

                $values = array();
                $num_octets = 1;
            }
        }
    }

    return $unicode;
}

//taken from wordpress
function seems_utf8($str) {
    $length = strlen($str);
    for ($i=0; $i < $length; $i++) {
        $c = ord($str[$i]);
        if ($c < 0x80) $n = 0; # 0bbbbbbb
        elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
        elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
        elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
        elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
        elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
        else return false; # Does not match any model
        for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
            if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                return false;
        }
    }
    return true;
}

class ListHelpers {
    public static function getUrlString($title) {

        return Helpers::slug($title);

    }
    public static function viewListUrlParams($list){
        return array('nameString' => self::getUrlString($list->title), 'listId' => $list->id);
    }

    public static function viewListUrl($list, $item = null, $showAd = false){
        $viewListUrlParams = self::viewListUrlParams($list);
        if($showAd){
            $viewListUrlParams = array_merge($viewListUrlParams, ['ad' => $showAd]);
        }
        if($item) {
            $url = route('viewList', array_merge($viewListUrlParams, array('item' => $item)));
        } else {
            $url = route('viewList', $viewListUrlParams);
        }
        return $url;
    }
    public static function getListThumbPathFromImage($image) {
        return $image . '_thumb.jpg';
    }

    public static function getListOGPathFromImage($image) {
        return $image . '_OG.jpg';
    }

    public static function getOgImage($list) {
        return asset(self::getListOGPathFromImage($list->image));
    }

    public static function getThumb($list) {
        return asset(self::getListThumbPathFromImage($list->image));
    }

    public static function isMyList($list) {
        $user = Auth::user();
        if(!$user) {
            return false;
        }
        return self::isListCreatedByUser($list, $user);
    }
    public static function isListCreatedByUser($list, $user) {
        return $list->isCreatedBy($user);
    }

    public static function parseDescription($description) {
        $description = htmlentities($description, ENT_QUOTES, "UTF-8");
        $description = nl2br($description);
        $description = self::urlsToLinks($description);
        return $description;
    }

    public static function urlsToLinks($string) {
        // Add HTML anchor tag to URLs
        $string = preg_replace('/(((ftp|http|https){1}:\/\/)[a-zA-Z0-9-@:%_\+.~#?&\/=]+)([\s]{0,})/i', '<a href="\\1" rel="nofollow" target="_blank">\\1</a>', $string);
        return $string;
    }
    public static function isMediaYoutubeVideo($media) {
        $validDomains = ['youtube.com', 'www.youtube.com'];
        $urlData = parse_url($media['url']);
        if(in_array($urlData['host'], $validDomains))
            return true;
        return false;
    }

    public static function isMediaVimeoVideo($media) {
        $validDomains = ['vimeo.com', 'www.vimeo.com', 'player.vimeo.com'];
        $urlData = parse_url($media['url']);
        if(in_array($urlData['host'], $validDomains))
            return true;
        return false;
    }

    public static function validateAudioMedia($media) {
        $url = $media['url'];
        $urlData = parse_url($media['url']);
        if($urlData['host'] == 'soundcloud.com')
            return true;
    }

    public static function getSoundCloudEmbedUrl($url) {

    }
}