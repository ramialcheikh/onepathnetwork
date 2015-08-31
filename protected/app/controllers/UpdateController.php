<?php
/**
 * Created by PhpStorm.
 * User: jitheshgopan
 * Date: 29/03/15
 * Time: 1:31 AM
 */

class UpdateController extends BaseController{

    const UPDATES_DIR = 'updates';
    public function __construct() {

    }
    public function getUpdatesDirPath() {
        return public_path(self::UPDATES_DIR);
    }
    public function index() {
        $updateDirs = File::glob($this->getUpdatesDirPath() . '/update-*', GLOB_ONLYDIR);
        include($updateDirs[0] . '/update.php');
    }
}