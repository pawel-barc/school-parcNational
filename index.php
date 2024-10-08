<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'localhost', 
    'httponly' => true, 
]);

session_start();
session_regenerate_id(true);
require 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$routes = [
    //The login displays the login page, then selects the Controller and the method
    'login' => [
        'controller' => 'LoginController', 
        'method' => 'login',
    ],
    //Display register Page
    'register' => [
        'controller' => 'RegisterController',
        'method' => 'registerView',
    ],
    //Save register form
    'register-form' => [
        'controller' => 'RegisterController',
        'method' => 'registerSaveForm',
    ],
    'loginForm' => [
        'controller' => 'LoginController',
        'method' => 'loginSaveForm',
    ],
    'homePageAdmin' => [
        'controller' => 'HomePageAdminController',
        'method' => 'homePageAdmin',
    ],
    'logout' => [
        'controller' => 'LoginController',
        'method' => 'logout',
    ],
    'login-using-google' => [
        'controller' => 'LoginController',
        'method' => 'loginUsingGoogle',
    ],
    'google-login' => [
        'controller' => 'LoginController',
        'method' => 'getDataFromGoogle',
    ],
    'ip-form' => [
        'controller' => 'IpController',
        'method' => 'displayForm',
    ],
    'ip-save' => [
        'controller' => 'IpController',
        'method' => 'saveIp',
    ],
    'ip-block' => [
        'controller' => 'IpController',
        'method' => 'getBlockIp',
    ],
    'facebook-login' =>[
        'controller' => 'Logincontroller',
        'method' => 'loginUsingFacebook',
    ],
    'add-membership' =>[
        'controller' => 'UserMembershipController',
        'method' => 'addMember',
    ],
    'subscribe-3-months' => [
        'controller' => 'UserMembershipController',
        'method' => 'subscribe3Months',
    ],
    'subscribe-6-months' => [
        'controller' => 'UserMembershipController',
        'method' => 'subscribe6Months',
    ],
    'subscribe-12-months' => [
        'controller' => 'UserMembershipController',
        'method' => 'subscribe12Months',
    ],
    'payment-success' =>[
        'controller' => 'PaymentStatusController',
        'method' => 'paymentSuccess',
    ],
    'payment-failed' =>[
        'controller' => 'PaymentStatusController',
        'method' => 'paymentFailed',
    ],
    'user-membership' =>[
        'controller' => 'UserMembershipController',
        'method' => 'viewMembership',
    ],
    'admin-memberships' => [
        'controller' => 'AdminMembershipController',
        'method' => 'viewMemberships',
    ],
    'admin-memberships-add' => [
        'controller' => 'AdminMembershipController',
        'method' => 'addMembership',
    ],
    'admin-memberships-edit' => [
        'controller' => 'AdminMembershipController',
        'method' => 'editMembership',
    ],
    'admin-memberships-delete' => [
        'controller' => 'AdminMembershipController',
        'method' => 'deleteMembership',
    ],    
    '' => [
        'controller' => 'HomeController', 
        'method' => 'news',
    ],
    'home' => [
        'controller' => 'HomeController', 
        'method' => 'news',
    ],
    'about' => [
        'controller' => 'AboutController',
        'method' => 'about',
    ],
    'trails' => [
        'controller' => 'TrailsController',
        'method' => 'trails',
    ],
    'details_trails' => [
        'controller' => 'TrailsController',
        'method' => 'details_trails',
    ],
    'map' => [
        'controller' => 'MapController',
        'method' => 'map',
    ],
    'admin_home' => [
        'controller' => 'AdminController',
        'method' => 'home',
    ],
    'manage_trails' => [
        'controller' => 'AdminTrailsController',
        'method' => 'manageTrails',
    ],
    'manage_campsite' => [
        'controller' => 'AdminCampsitesController',
        'method' => 'manageCampsites',
    ],
    'manage_ressources' => [
        'controller' => 'AdminRessourcesController',
        'method' => 'manageRessources',
    ],
    'manage_reports' => [
        'controller' => 'AdminReportsController',
        'method' => 'manageReports',
    ],
    'manage_users' => [
        'controller' => 'AdminUsersController',
        'method' => 'manageUsers',
    ],
    'manage_admin' => [
        'controller' => 'AdminAdminController',
        'method' => 'manageAdmin',
    ],
    'create_trails' => [
        'controller' => 'AdminTrailsController',
        'method' => 'createTrails',
    ],
    'create_campsite' => [
        'controller' => 'AdminCampsitesController',
        'method' => 'createCampsites',
    ],
    'create_ressources' => [
        'controller' => 'AdminRessourcesController',
        'method' => 'createRessources',
    ],
    'create_reports' => [
        'controller' => 'AdminReportsController',
        'method' => 'createReports',
    ],
    'create_admin' => [
        'controller' => 'AdminAdminController',
        'method' => 'createAdmin',
    ]
];

$url = str_replace("/parcNational/", '', $_SERVER['REQUEST_URI']);//Removal of the string 'parkNational' from the link
$urlArray = explode('?', $url);
if(isset($routes[$urlArray[0]])){
    $className = $routes[$urlArray[0]]['controller'];
    $methodName = $routes[$urlArray[0]]['method'];
   // var_dump($methodName);

    require_once 'model/BlockIp.php';
    $blockIp = new BlockIp('block_ips');
    if($blockIp->isIpBlocked()){
        echo 'Your Ip is blocked';
        return;
    }
    require_once 'model/Log.php';
    $log = new Log('logs');
    $log->saveLog($url);
    require_once 'controllers/' . $className . '.php';

    $object = new $className; 
    $object->{$methodName}();
    
}else{
    var_dump("pas d'adresse");
}