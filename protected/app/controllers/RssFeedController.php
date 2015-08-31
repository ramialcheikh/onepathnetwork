<?php
use Illuminate\Support\Facades\Config;

/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 12/08/15
 * Time: 6:02 PM
 */

class RssFeedController extends BaseController{

    public function index() {
        return self::generateRssFeed();
    }

    public static function generateRssFeed() {

        $config = Config::get('siteConfig');
        // create new feed
        $feed = Feed::make();

        // cache the feed for 10 minutes (second parameter is optional)
        $feed->setCache(10, 'list-rss-feed');

        // check if there is cached feed and build new only if is not
        if (!$feed->isCached())
        {
            // creating rss feed with our most recent 20 posts
            $lists = ViralList::orderBy('created_at', 'desc')->take(20)->get();

            // set your feed's title, description, link, pubdate and language
            $feed->title = $config['main']['siteTitle'];
            $feed->description = @$config['main']['siteDescription'];
            $feed->logo = @asset($config['main']['logo']);
            $feed->link = URL::route('rssFeed');
            $feed->setDateFormat('datetime'); // 'datetime', 'timestamp' or 'carbon'
            $feed->pubdate = $lists->count() ? $lists[0]->created_at : time();
            $feed->lang = $config['languages']['activeLanguage'];
            $feed->setShortening(true); // true or false
            $feed->setTextLimit(100); // maximum length of description text

            foreach ($lists as $list)
            {
                // set item's title, author, url, pubdate, description and content
                $listContentHtml = View::make('lists.rssFeedListContent', ['list'   =>  $list]);
                $feed->add($list->title, $list->creator->name, ListHelpers::viewListUrl($list), $list->created_at->toDateTimeString(), $list->description, $listContentHtml);
            }

        }

        // first param is the feed format
        // optional: second param is cache duration (value of 0 turns off caching)
        // optional: you can set custom cache key with 3rd param as string
        return $feed->render('rss');

        // to return your feed as a string set second param to -1
        // $xml = $feed->render('atom', -1);
    }
}