<?php
require_once ("./includes/functions.php");
$db = $_GET['db'];
$table = $_GET['table'];
$file_date = date("m_d_Y");
$file_name = $db . "_" . $table . "_" . $file_date . ".sql";
$mysqlver = mysql_get_server_info();
$date = date('F j, Y, g:i a');
$dbtableheader = "-- MySQL Table Dump 
-- Host: $host  Database: $db Table: $table
-- -----------------------------------------------------
-- Server Version: $mysqlver
-- Dumped on: $date\n\n";
//Write table header to file
file_put_contents($file_name, $dbtableheader);
//Dump table
dump_table($table, $db, $file_name);
//Compress dump and download
$fileg2 = file_get_contents($file_name);
$gzfile = "$file_name.gz";
$gzop = gzopen($gzfile, 'w9');
gzwrite($gzop, $fileg2);
gzclose($gzop);
unlink($file_name);
Download($gzfile);
?>