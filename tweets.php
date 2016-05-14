<?php
/**
 * Created by PhpStorm.
 * User: Chathura Widanage
 * Date: 5/14/16
 * Time: 11:10 AM
 */
require_once './toauth/twitter.php';
require_once './toauth/Exception/TwitterExecption.php';
use TwitterOAuth\TwitterOAuth;
date_default_timezone_set('UTC');
/**
 * Array with the OAuth tokens provided by Twitter when you create application
 *
 * output_format - Optional - Values: text|json|array|object - Default: object
 */
$config = array(
    'consumer_key'       => '', // API key
    'consumer_secret'    => '', // API secret
    'oauth_token'        => '', // not needed for app only
    'oauth_token_secret' => '',
    'output_format'      => 'json'
);
/**
 * Instantiate TwitterOAuth class with set tokens
 */
$connection = new TwitterOAuth($config);

$bearer_token = $connection->getBearerToken();

$params = array(
    'q' => '#gsoc'
);
$response = $connection->get('search/tweets', $params);

echo $response;