<?php

session_start();
if (($loader = require_once __DIR__ . '/vendor/autoload.php') == null)  {
    die('Vendor directory not found, Please run composer install.');
}
$facebook = new Facebook\Facebook([
    'app_id' => "app_id",
    'app_secret' => "secret_id",
    'default_graph_version' => 'v2.11',
    'fileUpload' => 'true',//optional
]);

$pageID='page_id';
$facebook->setDefaultAccessToken('access_token_page');

$data = [
    'message' => 'My first post message with PHP',
    'link' => 'https://developers.facebook.com/docs/',
];

$dataMedia = [
    'caption' => 'My first post picture with PHP',
    'url' => 'https://i.imgur.com/mtYYQMb.jpg',
];

$deb = $facebook->post('/'.$pageID.'/feed/', $data);
$debMedia = $facebook->post('/'.$pageID.'/photos/', $dataMedia);

$graphNode = $deb->getGraphNode();
$graphNode = $debMedia->getGraphNode();

$deb = $facebook->get('/'.$pageID);
$debMedia = $facebook->get('/'.$pageID);
