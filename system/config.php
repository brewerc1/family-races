<?php

global $config;

$config = array(
    'prod_mode' => false, // set to false for development
    'db_host' => 'informatics.plus',
    'db_name' => 'races',
    'db_user' => 'racesdbuser',
    'db_password' => 'racesdb!',
    'db_charset' => 'utf8mb4',
    'db_pdo_options' => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
    ],
    'path_to_system' => __DIR__,
);

$alert_styles = array(
    'primary' => "alert-primary",
    'secondary' => "alert-secondary",
    'success' => "alert-success",
    'danger' => "alert-danger",
    'warning' => "alert-warning",
    'info' => "alert-info",
    'light' => "alert-light",
    'dark' => "alert-dark"
);

if($config['prod_mode'] == false){
    // Set error reporting for development/debug
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Include the database configuration
require_once $config['path_to_system'] . '/database.php';