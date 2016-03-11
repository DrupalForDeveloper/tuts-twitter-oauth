<?php

require_once 'vendor/autoload.php';
use Abraham\TwitterOAuth\TwitterOAuth;

session_start();

$config = require_once 'config.php';

// get and filter oauth verifier
$oauth_verifier = filter_input(INPUT_GET, 'oauth_verifier');

// check tokens
if (empty($oauth_verifier) ||
    empty($_SESSION['oauth_token']) ||
    empty($_SESSION['oauth_token_secret'])
) {
    // something's missing, go and login again
    header('Location: ' . $config['url_login']);
}

// connect with application token
$connection = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $_SESSION['oauth_token'],
    $_SESSION['oauth_token_secret']
);

// request user token
$token = $connection->oauth(
    'oauth/access_token', [
        'oauth_verifier' => $oauth_verifier
    ]
);

// connect with user token
$twitter = new TwitterOAuth(
    $config['consumer_key'],
    $config['consumer_secret'],
    $token['oauth_token'],
    $token['oauth_token_secret']
);

$user = $twitter->get('account/verify_credentials');

// if something's wrong, go and log in again
if(isset($user->error)) {
    header('Location: ' . $config['url_login']);
}

// post a tweet
$status = $twitter->post(
    "statuses/update", [
        "status" => "Thank you @nedavayruby, now I know how to authenticate users with Twitter because of this tutorial https://goo.gl/N2Znbb"
    ]
);

echo ('Created new status with #' . $status->id . PHP_EOL);

print_r($status);