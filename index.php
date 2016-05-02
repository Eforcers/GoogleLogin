<?php
/**
 * Created by PhpStorm.
 * User: carlos
 * Date: 30/04/16
 * Time: 10:34 AM
 */

require 'vendor/autoload.php';

session_start();


if (!isset($_SESSION['user'])) {
    $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
    header("Location: /oauth2callback.php");
} else {
    echo "Hola mundo<br>";
    echo $_SESSION['user'] . "<br>";
    $me = $_SESSION['me'];
    echo "Display Name: {$me['displayName']} <br>";
    echo " <img src = \"{$me['image']['url']}\">";

    echo "<br><a href='/oauth2callback.php?logout=true'>Cerrar sesi&oacute;n</a>";
}