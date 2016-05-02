<?php
/**
 * Created by PhpStorm.
 * User: carlos
 * Date: 30/04/16
 * Time: 10:46 AM
 */

session_start();

require 'vendor/autoload.php';

/************************************************
 * ATTENTION: Fill in these values! Make sure
 * the redirect URI is to this page, e.g:
 * http://localhost:8080/oauth2callback.php
 ************************************************/

$client_id = 'your client id';
$client_secret = 'your client secrete';
$redirect_uri = 'http://localhost:8080/oauth2callback.php';

/************************************************
 * Make an API request on behalf of a user. In
 * this case we need to have a valid OAuth 2.0
 * token for the user, so we need to send them
 * through a login flow. To do this we need some
 * information from our API console project.
 ************************************************/
$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope("openid email profile");

/************************************************
 * If we're logging out we just need to clear our
 * local access token in this case
 ************************************************/
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
    unset($_SESSION['user']);
    echo "Sesi&oacute;n cerrada";
    return;
}


/************************************************
 * If we have a code back from the OAuth 2.0 flow,
 * we need to exchange that with the authenticate()
 * function. We store the resultant access token
 * bundle in the session, and redirect to ourself.
 ************************************************/
if (isset($_GET['code'])) {
    $client->authenticate($_GET['code']);
    $_SESSION['access_token'] = $client->getAccessToken();
    $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
}

/************************************************
 * If we have an access token, we can make
 * requests, else we generate an authentication URL.
 ************************************************/
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $client->setAccessToken($_SESSION['access_token']);
} else {
    $authUrl = $client->createAuthUrl();
}


/************************************************
 * If we're signed in and have a request to shorten
 * a URL, then we create a new URL object, set the
 * unshortened URL, and call the 'insert' method on
 * the 'url' resource. Note that we re-store the
 * access_token bundle, just in case anything
 * changed during the request - the main thing that
 * might happen here is the access token itself is
 * refreshed if the application has offline access.
 ************************************************/

if (isset($authUrl)) {
    echo "<meta http-equiv='refresh' content='0; url=" . $authUrl . "' />";
} else {
    $var1 = $client->verifyIdToken();
    $email = $var1->getAttributes()['payload']['email'];

    $_SESSION['user'] = $email;

    $plus = new Google_Service_Plus($client);
    $_SESSION['me'] = $plus->people->get('me');;



    if (isset($_SESSION['redirect'])) {
        $uri_redirect = $_SESSION['redirect'];
        unset($_SESSION['redirect']);
    } else {
        $uri_redirect = "/index.php";
    }

    echo "<meta http-equiv='refresh' content='0; url=" . $uri_redirect . "' />";
}



