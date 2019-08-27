<link rel="stylesheet" href="./styles/stylesheet.css" type="text/css" />
<div id="logo">
<center><a href='./index.php'><img src="./styles/images/logo.png"></a></center>
</div><br>
<?php
require_once ("./includes/functions.php");
$current = $_GET['current'];
$next = $_GET['next'];
$db = $_GET['db'];
$file_date = date("m_d_Y");
$file_name = $db . "_$file_date.sql";
$mysqlver = mysql_get_server_info();
$date = date('F j, Y, g:i a');
$all_tables = array();
$get_tables = mysql_fetch_object(mysql_query("SELECT COUNT(TABLE_NAME) as num_rows FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db'"));
$get_tables2 = mysql_query("SHOW TABLES FROM $db");
$rows = $get_tables->num_rows;
$dbheader = "-- MySQL Database Dump 
-- Host: $host  Database: $db
-- -----------------------------------------------------
-- Server Version: $mysqlver
-- Dumped on: $date\n\n";
#If start of dump
if ($current == 0) {
    //Make sure log file isn't still there
    unlink('./log.txt');
    //Add DB dump header to file
    file_put_contents($file_name, $dbheader);
}
if ($current == $rows) {
    //Dump complete, start compression
    if (filesize($file_name) > "52428800") {
        echo "<script>window.location = 'compress.php?sbytes=0&name=$file_name';</script>";
        //Delete log file for the next dump.
        unlink('./log.txt');
    } else {
        $fileg2 = file_get_contents($file_name);
        $gzfile = "$file_name.gz";
        $gzop = gzopen($gzfile, 'w9');
        gzwrite($gzop, $fileg2);
        gzclose($gzop);
        unlink($file_name);
        //Delete log file for the next dump.
        unlink('./log.txt');
        echo "<font color='green'><b>Database Dump Complete!</b></font><br><br>";
        echo "<script>window.location = '$gzfile';</script>";
    }
} else {
    while ($arr = mysql_fetch_array($get_tables2)) {
        array_push($all_tables, $arr[0]);
    }
    $current_t = $all_tables[$current];
    $rows2 = $rows - 1;
    if (file_exists("./log.txt")) {
        echo "Database dump progress:<br>
<progress max='$rows2' value='$current'></progress>";
        $content = file_get_contents('./log.txt');
        echo "<pre>
$content
</pre>";
    }
    //Dump table
    dump_table($current_t, $db, $file_name, $next, $rows2);
    //When finished dumping write to log and move onto the next table.
    file_put_contents('./log.txt', "Dumped table: $current_t\n", FILE_APPEND);
    $next2 = $next + 1;
    echo "<script>window.location = 'dump_db.php?current=$next&next=$next2&db=$db';</script>";
}
?>