<?php

$routes = [
    // Routes liées à l'authentification et à la gestion des utilisateurs
    'login' => [
        'controller' => 'LoginController', 
        'method' => 'login',
    ],
    'register' => [
        'controller' => 'RegisterController',
        'method' => 'registerView',
    ],
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

    'facebook-login' => [
        'controller' => 'LoginController',
        'method' => 'loginUsingFacebook',
    ],
    'forgot-password' => [
        'controller' => 'LoginController',
        'method' => 'forgotPassword',
    ],
    'reset-password' => [
        'controller' => 'LoginController',
        'method' => 'resetPassword',
    ],
    'reset-password-request' => [
        'controller' => 'LoginController',
        'method' => 'resetPasswordRequest',
    ],

    // Routes liées à la gestion des abonnements et des membres
    'all-memberships' => [
        'controller' => 'UserMembershipController',
        'method' => 'viewAllMemberships'
    ],
  
    'add-membership' => [
        'controller' => 'UserMembershipController',
        'method' => 'addMember',
    ],
    'user-membership' => [
        'controller' => 'UserMembershipController',
        'method' => 'viewMembership',
    ],
    'user-memberships' => [
        'controller' => 'UserMembershipController',
        'method' => 'subscribeMembership',
    ],
    'subscribe-membership' => [
        'controller' => 'UserMembershipController',
        'method' => 'subscribeMembership',
    ],
    'view-available-memberships' => [
        'controller' => 'UserMembershipController',
        'method' => 'viewAvailableMemberships',
    ],
    
    // Routes réservées à l'administration des abonnements
    'admin-active-memberships-list' => [
        'controller' => 'AdminMembershipController',
        'method' => 'viewActiveMemberships'
    ],
    'admin-memberships-list' => [
        'controller' => 'AdminMembershipController',
        'method' => 'viewMembership',
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
    'manage_ship' => [
        'controller' => 'AdminMembershipController',
        'method' => 'viewMembership',
    ],

    // Routes liées à la gestion des pages statiques et du contenu


    'payment-success' => [
        'controller' => 'PaymentStatusController',
        'method' => 'paymentSuccess',
    ],
    'payment-failed' => [
        'controller' => 'PaymentStatusController',
        'method' => 'paymentFailed',
    ],


    // Routes liées à la gestion des pages statiques et du contenu
    '' => [
        'controller' => 'HomeController', 
        'method' => 'news',
    ],
    'home' => [
        'controller' => 'HomeController', 
        'method' => 'news',
    ],
    'details_news' => [
        'controller' => 'NewsController', 
        'method' => 'details_news',
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
    'manage_visites' => [
        'controller' => 'AdminVisitesController',
        'method' => 'manageVisites',
    ],
    'manage_campsites' => [
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
    
    'manage_article' => [
        'controller' => 'AdminArticleController',
        'method' => 'manageArticles',
    ],
    'manage_users' => [
        'controller' => 'AdminUsersController',
        'method' => 'manageUsers',
    ],
    'manage_admin' => [
        'controller' => 'AdminAdminController',
        'method' => 'manageAdmin',
    ],


    // Routes liées à la gestion des profils utilisateurs
    'admin_profil' => [
        'controller' => 'AdminProfilController',
        'method' => 'editAdminProfile',
    ],
    'profile' => [
        'controller' => 'ProfileController',
        'method' => 'viewProfile',
    ],
    'update-profile' => [
        'controller' => 'ProfileController',
        'method' => 'updateProfile',
    ],
    'profile-form' =>[
        'controller' => 'ProfileController',
        'method' => 'ProfileForm',
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
    ],
    'create_ship' => [
        'controller' => 'AdminAdminController',
        'method' => 'addMembership',
    ],
    'create_articles' => [
        'controller' => 'AdminArticleController',
        'method' => 'createArticle',
    ],
    'manage-favorite-trail' => [
        'controller' => 'FavoritesTrailsController',
        'method' => 'manageFavoriteTrail',
    ],
    'manage-favorite-trail-ajax' => [
        'controller' => 'FavoritesTrailsController',
        'method' => 'manageFavoriteTrailAjax',
    ],

    'manage-completed-trail' => [
        'controller' => 'CompletedTrailsController',
        'method' => 'manageCompletedTrail',
    ],
    'manage-completed-trail-ajax' => [
        'controller' => 'CompletedTrailsController',
        'method' => 'manageCompletedTrailAjax',
    ],
    'campsite' => [
        'controller' => 'campsiteController',
        'method' => 'getCampsiteById',
    ],

    'deleteReservation' =>[
        'controller' => 'ProfileController',
        'method' => 'deleteReservation',
        'params' => ['reservation_id']
    ],

    'campsite' => [
        'controller' => 'campsiteController',
        'method' => 'getAllCampsites',
    ],
    'campsiteDetails' => [
        'controller' => 'CampsiteController',
        'method' => 'getCampsiteById'
    ],


    // Routes liées à la gestion des réservations

    'reservation_history' => [
        'controller' => 'ReservationController',
        'method' => 'getReservationsByUser',
    ],
    'download-receipt' => [
        'controller' => 'ReservationController',
        'method' => 'downloadReceipt',
    ],
    'cancel-reservation' => [
        'controller' => 'ReservationController',
        'method' => 'cancelReservation',
    ],

    // Routes liées au paiement
    'payment-form' => [
        'controller' => 'PaymentController',
        'method' => 'showPaymentForm',
    ],

    'payment' => [
        'controller' => 'PaymentController',
        'method' => 'processPayment',
    ],

    'apply-promo-code' => [ 
            'controller' => 'PaymentController',
            'method' => 'applyPromoCode',
        ],


    'calendar' => [
        'controller' => 'CalendarController',
        'method' => 'showCalendar',
    ],
    'coves' => [
        'controller' => 'CoveController',
        'method' => 'getAllCoves',
    ],

    'ressources' => [
        'controller' => 'RessourceController',
        'method' => 'getAllRessources',
    ],
    'ressourceDetails' => [
        'controller' => 'RessourceController',
        'method' => 'getRessourceById',
    ],

];

return $routes;