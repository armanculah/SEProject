<?php

session_start(); // Start the PHP session
$_SESSION['user_id'] = 5; // Set user_id to 5 for testing purposes

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require '../vendor/autoload.php'; //run autoloader
require 'rest/routes/routes.php'; // Include the routes file

Flight::route('/', function(){  //define route and define function to handle request
   echo 'Hello world!';
});

Flight::start();  //start FlightPHP
?>
