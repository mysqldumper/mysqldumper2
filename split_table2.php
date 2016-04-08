<link rel="stylesheet" href="./styles/stylesheet.css" type="text/css" />
<div id="logo">
<center><a href='./index.php'><img src="./styles/images/logo.png"></a></center>
</div><br>
<?php
require_once ("./includes/functions.php");
$db = $_GET['db'];
$table = $_GET['table'];
$lim = $_GET['lim'];
$nr = $_GET['nr'];
$file_date = date("m_d_Y");
$file_name = $db . "_" . $table . "_" . $file_date . ".sql";
$divide = floor($nr / 10000);
$mysqlver = mysql_get_server_info();
$date = date('F j, Y, g:i a');
$dbtableheader = "-- MySQL Table Dump 
-- Host: $host  Database: $db Table: $table
-- -----------------------------------------------------
-- Server Version: $mysqlver
-- Dumped on: $date\n\n";
if ($lim == 0) {
    //Write table header to file
    file_put_contents($file_name, $dbtableheader);
}
if ($lim > $divide) {
    if (filesize($file_name) > "52428800") {
        echo "<script>window.location = 'compress.php?sbytes=0&name=$file_name';</script>";
    } else {
        $fileg2 = file_get_contents($file_name);
        $gzfile = "$file_name.gz";
        $gzop = gzopen($gzfile, 'w9');
        gzwrite($gzop, $fileg2);
        gzclose($gzop);
        unlink($file_name);
        echo "<script>window.location = '$gzfile';</script>";
    }
} else {
    echo "Big table dump progress:<br>
<progress max='$divide' value='$lim'></progress>";
    //Dump table
    dump_table2($table, $db, $file_name, $lim, $divide);
    $lim2 = $lim + 1;
    echo "<script>window.location = 'split_table2.php?db=$db&lim=$lim2&table=$table&nr=$nr';</script>";
}
?>