<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 28/03/15
 * Time: 4:28 AM
 */

class AdminSitemapController extends AdminBaseController{

    public function getRegenerate() {
        SitemapController::generateSitemap(true);
        return View::make('admin.sitemap.regenerate');
    }
}