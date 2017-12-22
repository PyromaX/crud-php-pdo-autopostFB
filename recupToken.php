<?php
session_start();
if (($loader = require_once __DIR__ . '/vendor/autoload.php') == null)  {
    die('Vendor directory not found, Please run composer install.');
}
$fb = new Facebook\Facebook([
    'app_id' => "app_id",
    'app_secret' => "secret_app",
    'default_graph_version' => 'v2.11',
]);
$helper = $fb->getRedirectLoginHelper();
$permissions = ['email','publish_pages','manage_pages','publish_actions','user_friends','public_profile','user_posts','user_likes']; // Optional permissions
$callback = 'http://yourPath/fb-callback.php';
$loginUrl = $helper->getLoginUrl($callback, $permissions);
echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
