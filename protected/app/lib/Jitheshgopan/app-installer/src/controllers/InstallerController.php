<?php
namespace Jitheshgopan\AppInstaller;
use Illuminate\Support\Facades\Config;


class InstallerController extends \BaseController {
    public function index(){
        //If install.php is not present of database config is already set, Abort - disable installation
        if(!file_exists(public_path('install.php'))) {
            die("Sorry! Installation disabled!");
        }

        //$stages = Config::get('app-installer::stages');
        $installer = new Installer();

        //Requirements check stage
        $this->_setupRequirementsStage($installer);

        //Directory permissions stage
        $this->_directoryPermissionsStage($installer);

        //Database config stage
        $this->_setupDbConnectionStage($installer);

        //Import database files
        $this->_setupImportDbStep($installer);

        //Finish
        $this->_setupFinishStage($installer);

        try {
            return $installer->run();
        } catch (InstallerException $e){
            return Response::make($e->getMessage(), 400);
        }

    }

    public function _setupRequirementsStage($installer){
        $requirementsStage = $installer->addStage("System requirements", [
            'banner' => asset('images/installation/system.png')
        ]);

        //Php version
        $requirementsStage->addPhpVersionCheckStep('5.4', '>=');

        //GD extension check
        $gdExtensionCheck = $requirementsStage->addStep("Checking GD Extension", [
            'type' => 'ExtensionCheck'
        ]);
        $gdExtensionCheck->check('gd');

        //PDO extension
        $pdoCheck = $requirementsStage->addStep("Checking PDO Extension", [
            'type' => 'ExtensionCheck'
        ]);
        $pdoCheck->check('pdo');

        //CURL extension
        $curlCheck = $requirementsStage->addStep("Checking CURL Extension", [
            'type' => 'ExtensionCheck'
        ]);
        $curlCheck->check('curl');

        //allow_url_fopen settings
        $allowUrlFopenCheck = $requirementsStage->addStep("Checking allow_url_fopen settings", [
            'type' => 'IniGetCheck'
        ]);
        $allowUrlFopenCheck->check('allow_url_fopen', null, "'allow_url_fopen' must be enabled. You may comtact your hosting support or change it in php.ini(if you have access to it)");
    }

    public function _setupDbConnectionStage($installer){
        $dbConfigStage = $installer->addStage("Database connection", [
            'banner' => asset('images/installation/database.png')
        ]);
        $dbConfigStep = $dbConfigStage->addDbConfigStep('mysql', [
            'configSampleFilePath' => app_path('config/database-sample.php'),
            'configFilePath' => app_path('config/database.php')
        ]);
    }

    public function _setupImportDbStep($installer){
        $dbImportStage = $installer->addStage("Loading database with necessary data", [
            'banner' => asset('images/installation/importdb.png')
        ]);
        $dbImportStep = $dbImportStage->addStep('Importing', [
            'type' => 'ImportDb',
            'dbFiles' => [
                Config::get('install.dbBasicStructureFile'),
                Config::get('install.dbBasicDataFile')
            ]
        ]);
    }

    public function _directoryPermissionsStage($installer) {
        $directoryWritableStage = $installer->addStage("Directory permissions", [
            'banner' => asset('images/installation/permissions.png')
        ]);
        //Is directory writable check
        $configCheck = $directoryWritableStage->addStep("Checking if the 'config' directory is writable", [
            'type' => 'WritableCheck'
        ]);
        $mediaCheck = $directoryWritableStage->addStep("Checking if the 'media' directory is writable", [
            'type' => 'WritableCheck'
        ]);
        $configCheck->checkWritable(app_path('config'));
        $mediaCheck->checkWritable(public_path('media'));
    }

    public function _setupFinishStage($installer){
        $finishStage = $installer->addFinishStage("Installation Complete", [
            'proceedUrl' => './admin/login',
            'proceedUrlText' => 'Go to admin panel'
        ]);
    }
}