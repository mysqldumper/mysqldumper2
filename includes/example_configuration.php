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
if(!mysql_connect("$host:$port", $username, $password)) {
die('Could not connect to MySQL: ' . mysql_error());
}
?>