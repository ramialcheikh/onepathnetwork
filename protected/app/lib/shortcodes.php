<?php

function do_shortcode($content) {
    $shortCodeEngine = App::make('shortCodeEngine');
    return $shortCodeEngine->do_shortcode($content);
}


return array(
    'latest_lists' => function($attrs = []) {
        $loadListOptions = ListController::getListQueryStreamOptions([
            'stream'    =>  'latest'
        ]);
        if(!empty($attrs['limit'])) {
            $loadListOptions['limit'] = $attrs['limit'];
        }
        $lists = ListController::_getLists($loadListOptions);
        return View::make('lists.listsList', [
            'lists' =>  $lists
        ])->render();
    },
    'popular_lists' => function($attrs = []) {
        $loadListOptions = ListController::getListQueryStreamOptions([
            'stream'    =>  'popular'
        ]);
        if(!empty($attrs['limit'])) {
            $loadListOptions['limit'] = $attrs['limit'];
        }
        $lists = ListController::_getLists($loadListOptions);
        return View::make('lists.listsList', [
            'lists' =>  $lists
        ])->render();
    },
    'random_lists' => function($attrs = []) {
        $loadListOptions = ListController::getListQueryStreamOptions([
            'stream'    =>  'random'
        ]);
        if(!empty($attrs['limit'])) {
            $loadListOptions['limit'] = $attrs['limit'];
        }
        $lists = ListController::_getLists($loadListOptions);
        return View::make('lists.listsList', [
            'lists' =>  $lists
        ])->render();
    }
);