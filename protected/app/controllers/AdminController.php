<?php

class AdminController extends AdminBaseController {

    const INSTALL_FILE = 'install.php';
    public function __construct() {
        try{
            $adminData = self::getAdminCredentials();
            define('ADMIN_USERNAME', $adminData['username']);
            define('ADMIN_PASSWORD', $adminData['password']);

            if(file_exists($this->getInstallFilePath())) {
                View::share('installFileExistsError', "You must remove the file '" . self::INSTALL_FILE . "' on the script's base folder for security reasons.");
            }
        } catch(Exception $e){
            die(Response::error(
                "<h4>Original error message:</h4>" . $e->getMessage(),
                "Error loading admin config. Check Admin config file: /protected/app/config/admin.php"
            ));
        }
    }

    public static function getAdminCredentials($key = null){
        $adminCredentials = Config::get('admin');
        //If admin config is empty "admin.php" has not yet been created, load config from admin-sample.php
        if(empty($adminCredentials)) {
            $adminCredentials = Config::get('admin-sample');
        }
        if($key && isset($adminCredentials[$key])) {
            return $adminCredentials[$key];
        }
        return $adminCredentials;
    }

    public function index()
    {
        $stats = new Stats();
        $stats->addStatSubject('ViralList', 'List');
        $todayStats = $stats->getTodayStats();
        $overallStats = $stats->getOverallStats();

        View::share(array(
            'overallStats' => $overallStats,
            'todayStats' => $todayStats
        ));
/*
        $last30DaysNewItems = Stats::getDailyStatsFor('ItemModelName', 30);
        View::share(array('last30DaysNewItems' => json_encode($last30DaysNewItems)));
*/
        $last30DaysUserRegistrations = Stats::getDailyStatsFor('User', 30);
        $last30DaysNewLists = Stats::getDailyStatsFor('ViralList', 30);
        View::share(array(
            'last30DaysUserRegistrations' => json_encode($last30DaysUserRegistrations),
            'last30DaysNewLists'    =>  json_encode($last30DaysNewLists)
        ));

        return View::make('admin/index');
    }

    public function login(){
        View::share(array('redirect' => Input::get('redirect')));
        if(Request::isMethod('get')) {
            return View::make('admin/login');
        } else {
            $username = Input::get('username');
            $password = Input::get('password');
            if($username == ADMIN_USERNAME && $password == ADMIN_PASSWORD) {
                //Login success
                Session::set('admin', 'admin');

                //Remove install file
                $this->removeInstallFile();

                if(Input::get('redirect')) {
                    return Redirect::to('/' . urldecode(Input::get('redirect')));
                } else {
                    return Redirect::route('admin');
                }
            } else {
                return View::make('admin/login')->with(array('error' => 'Incorrect username or password!'));
            }
        }
    }
    public function logout(){
        Session::forget('admin');
        return 'Logged out successfully';
    }


    public function changePassword(){
        $currentUsername = AdminController::getAdminCredentials('username');
        $configFilePath = app_path('config/admin.php');
        $errors = [];
        $formSuccess = null;
        if(Request::isMethod('post')){
            //Form sumbitted
            $username = Input::get('username');
            $password = Input::get('password');
            $repeatPassword = Input::get('repeatPassword');
            if(!$username || !$password || !$repeatPassword) {
                $errors[] = "Username or password empty! Please fill in all fields";
            } else if($password != $repeatPassword){
                $errors[] = "The passwords doesn't match. Repeat the same password to make sure it is correct.";
            }
            if(!$errors){
                //No error. Save new credentials
                $userCredentials = [
                    'username' => $username,
                    'password' => $password
                ];
                $configFileContent =
                    '<?php return '. var_export($userCredentials, true) .';';
                //dd($configFileContent);
                try {
                    if(!file_put_contents($configFilePath, $configFileContent)) {
                        throw new Exception();
                    } else {
                        $formSuccess = true;
                    }
                } catch(Exception $e) {
                    $errors[] = "Failed storing new admin credentials to config file. Make sure '" . $configFilePath . "' is writable.'";
                }
            }
        }

        return View::make('admin/config/changePassword')->with(array(
            'currentUsername' => $currentUsername,
            'formErrors' => $errors,
            'formSuccess' => $formSuccess
        ));
    }
    public function getInstallFilePath() {
        return public_path(static::INSTALL_FILE);
    }
    public function removeInstallFile() {
        if(file_exists($this->getInstallFilePath())) {
            try {
                unlink($this->getInstallFilePath());
            } catch(Exception $e) {
                //May be a permission problem. Discard - warning message will be shown in admin panel to inform user to remove it manually
            }
        }
    }
}
