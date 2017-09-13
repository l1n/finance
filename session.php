<?php
error_reporting(E_ALL);
session_start();

$materializeVersion = "0.100.2";
$timeout_duration = 1800;
global $dbh;
try {
    if (strpos($_SERVER['REQUEST_URI'], '/finance') === 0) {
        $dbh = new PDO('sqlite:/afs/umbc.edu/public/virthost/sites/appsga/prod/htdocs/osl.sqlite');
        $timeout_duration = 18000;
    }
} catch (PDOException $e) {
    print('SQL DB Connection failed: ' . $e->getMessage());
}


$time = $_SERVER['REQUEST_TIME'];
if (isset($_SESSION['LAST_ACTIVITY']) && ($time - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset(); 
    session_destroy();
    session_start();    
}

$_SESSION['LAST_ACTIVITY'] = $time;
if (isset($_SERVER["HTTP_EPPN"]) && isset($_SERVER["HTTP_UMBCUSERNAME"]) && isset($_SERVER["HTTP_MAIL"])) {
    $_SESSION["HTTP_MAIL"] = $_SERVER["HTTP_MAIL"];
    $_SESSION["HTTP_EPPN"] = $_SERVER["HTTP_EPPN"];
    $_SESSION["HTTP_DISPLAYNAME"] = $_SERVER["HTTP_DISPLAYNAME"];
    $_SESSION["HTTP_UMBCUSERNAME"] = $_SERVER["HTTP_UMBCUSERNAME"];
}

function goBack() {
    header('Location: '.(
        !empty($_REQUEST['redirect_uri']) ? 
        $_REQUEST['redirect_uri'] : (
            !empty($_SERVER['HTTP_REFERER']) ?
            $_SERVER['HTTP_REFERER'] : 
            '/finance-tools')));
}

function REQUEST($varName) {
    if (isset($_POST[$varName])) {
        return $_POST[$varName];
    } elseif (isset($_GET[$varName])) {
        return $_GET[$varName];
    } elseif (isset($_SESSION[$varName])) {
        return $_SESSION[$varName];
    }
    return;
}

