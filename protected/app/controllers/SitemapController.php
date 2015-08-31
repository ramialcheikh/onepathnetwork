<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 12/08/15
 * Time: 6:02 PM
 */

class SitemapController extends BaseController{

    public function index() {
        return self::generateSitemap();
    }

    public static function generateSitemap($reGenerate = false) {

        // create new sitemap object
        $sitemap = App::make("sitemap");

        $sitemap->setCache('laravel.sitemap', 3600);

        if($reGenerate) {
            Cache::forget($sitemap->model->getCacheKey());
        }

        // check if there is cached sitemap and build new only if is not
        if (!$sitemap->isCached())
        {
            // add item to the sitemap (url, date, priority, freq)
            $sitemap->add(URL::to('/'), null, '1.0', 'daily');


            $categories = Category::get();
            foreach($categories as $category) {
                $sitemap->add(URL::route('category', [$category->slug]), null, '1.0', 'daily');
            }

            // get all lists from db
            $lists = ViralList::orderBy('created_at', 'desc')->get();

            // add every post to the sitemap
            foreach ($lists as $list)
            {
                $sitemap->add(ListHelpers::viewListUrl($list), $list->updated_at, '1.0', 'monthly');
            }
        }

        // show your sitemap (options: 'xml' (default), 'html', 'txt', 'ror-rss', 'ror-rdf')
        return $sitemap->render('xml');
    }
}