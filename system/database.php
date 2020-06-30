<?php
global $pdo;

// Configure the DSN (Data Source Name)
$dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['db_charset']}";

try {
	$pdo = new PDO($dsn, $config['db_user'], $config['db_password'], $config['db_pdo_options']);
} catch (\PDOException $e) {
	throw new \PDOException($e->getMessage(), (int)$e->getCode());
}
?>