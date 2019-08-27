<?php
//Set some ini settings
//These are crucial so don't edit them
@ini_set("memory_limit","9999M");
@ini_set("max_input_time", 0);
@ini_set("max_execution_time", 0);

//MySQL Configuration
$host = 'localhost';
$port = '3306';
$username = 'root';
$password = 'root';
try {
	$pdo = new PDO('mysql:host='.$host.';port='.$port.';dbname=;charset=utf8', 
	$username,
	$password, 
	array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
	);
} catch(PDOException $e) {
		die($e->getMessage());
	}
?>
