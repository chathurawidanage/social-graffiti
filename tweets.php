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
    'consumer_key' => 'Ik1x7Esq0kf418mDMxvOkZ205', // API key
    'consumer_secret' => 'bf47cZdIMlRFVJVLP6IUzOBGHEAjN135iBQW4CfK6cRyaFawc4', // API secret
    'oauth_token' => '4882032977-Q3R4fAslK9JrFxWsl7p8ThnvA7VMT5uxUa8fkEX', // not needed for app only
    'oauth_token_secret' => 'h05QCPGRgzuWbQF52WweBJvO8k1wcPJOg6uiyxmJM3p70',
    'output_format' => 'json'
);
/**
 * Instantiate TwitterOAuth class with set tokens
 */
$connection = new TwitterOAuth($config);

$bearer_token = $connection->getBearerToken();

$query = filter_input(INPUT_GET, "q");

$params = array(
    'q' => $query,
    'count'=>100
);

$response = $connection->get('search/tweets', $params);

echo $response;