<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checking minimum requirements</title>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css"/>
    <style>
        body {
            background: #eeeeee;
        }
        #mainColumn {
            background: #ffffff;
            padding: 30px;
        }
    </style>


    <script>(function() {
            var _fbq = window._fbq || (window._fbq = []);
            if (!_fbq.loaded) {
                var fbds = document.createElement('script');
                fbds.async = true;
                fbds.src = '//connect.facebook.net/en_US/fbds.js';
                var s = document.getElementsByTagName('script')[0];
                s.parentNode.insertBefore(fbds, s);
                _fbq.loaded = true;
            }
        })();
        window._fbq = window._fbq || [];
        window._fbq.push(['track', '6021241502394', {'value':'22.5','currency':'USD'}]);
    </script>
    <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6021241502394&amp;cd[value]=22.5&amp;cd[currency]=USD&amp;noscript=1" /></noscript>

</head>
<body>

<div class="text-center" style="margin: 40px 0px;"><h1>Checking Minimum requirements</h1></div>

<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1" id="mainColumn">

            <?php

            /*
             * Check if everything is ok to run laravel
             */

            class Errors {
                public static $errors = array();
            }
            $checks = array();
            define('MIN_PHP_VERSION', 5.4);

            function check($name, $callback) {
                global $checks;
                $checks[] = $name;
                call_user_func($callback);
            }

            check("Need PHP version >=" . MIN_PHP_VERSION, function() {
                if(!version_compare(phpversion(), MIN_PHP_VERSION, '>=')) {
                    Errors::$errors[] = array(
                        'title' => 'PHP version error',
                        'message' => "Laravel requires a minimum PHP version of " . MIN_PHP_VERSION . '<br>' . "You current PHP version is " . phpversion() . " which is not supported"
                    );
                }
            });
            check("Mcrypt Extension is required", function(){
                if(!extension_loaded('mcrypt')){
                    Errors::$errors[] = array(
                        'title' => "MCrypt Extension not loaded",
                        'message' => "Laravel requires MCrypt PHP Extension"
                    );
                }
            });

            check("PDO_MYSQL Extension is required", function(){
                if(!extension_loaded('pdo_mysql')){
                    Errors::$errors[] = array(
                        'title' => "pdo_mysql Extension not loaded",
                        'message' => "This script requires pdo_mysql PHP Extension for Database connections"
                    );
                }
            });

            check("MbString Extension is required", function(){
                if(!extension_loaded('mbstring')){
                    Errors::$errors[] = array(
                        'title' => "MbString Extension not loaded",
                        'message' => "This script requires MbString PHP Extension for Facebook login"
                    );
                }
            });

            check("'allow_url_fopen' must be enabled", function(){
                if(!ini_get('allow_url_fopen')){
                    Errors::$errors[] = array(
                        'title' => "allow_url_fopen is not enabled",
                        'message' => "This script requires allow_url_fopen be enabled"
                    );
                }
            });

            check("'PHP Fileinfo' Extension is required", function(){
                if(!extension_loaded('fileinfo')){
                    Errors::$errors[] = array(
                        'title' => "PHP Fileinfo Extension not loaded",
                        'message' => "This script requires PHP Fileinfo Extension"
                    );
                }
            });


            check("JSON module required", function(){
                if(!function_exists( 'json_encode' )) {
                    Errors::$errors[] = array(
                        'title' => "PHP JSON not available",
                        'message' => "PHP JSON should be enabled as we require json_encode and similar functions."
                    );
                }
            });

            check("'storage' directory should be writable", function(){
                if(file_exists('protected')){
                    //Is packaged app
                    $storageFilePath = __DIR__ . '/protected/app/storage';
                } else {
                    //Is development version
                    $storageFilePath = __DIR__ . '/../app/storage';
                }
                if(!is_writable($storageFilePath)) {
                    Errors::$errors[] = array(
                        'title' => "'storage' directory should be writable",
                        'message' => "Make '" . $storageFilePath . "' directory writable by assigning appropriate permissions.<br>( For eg, via shell command: chmod -R 777 " . $storageFilePath . " )"
                    );
                }
            });

            ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <div class="panel-title">Checking requirements</div>
                </div>
                <div class="panel-body">
                    <ol>
                        <?php
                        foreach ($checks as $check) {
                        ?>
                            <li><?php echo $check; ?></li>
                        <?php
                        }
                        ?>
                    </ol>
                </div>
            </div>


            <?php
            if(empty(Errors::$errors)) {
                ?>
                <h2>Success! You are ready to Install</h2>
                <a href="./install-it" class="btn btn-success">Go to Installation page</a>
                <?php
            } else {
                ?>
                    <h2>Sorry! You are missing some requirements</h2><br/>
                <?php
                foreach (Errors::$errors as $error) {
                    ?>
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <div class="panel-title">
                                <?php echo $error['title'] ?>
                            </div>
                        </div>
                        <div class="panel-body">
                            <?php echo $error['message'] ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                    <div class="text-center alert alert-warning">
                        <h3>Get them fixed</h3>
                        <p>Contact your hosting provider to fix them if possible or move to a different host that meets all the requirements stated above</p>
                        <br/>
                    </div>
                <?php
            }

            ?>
        </div>
    </div>
</div>

<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/js/bootstrap.min.js"></script>

</body>
</html>