<?php
require_once ("./includes/configuration.php");
function Download($file) {
    if (@!file_exists($file)) die("File Doesn't exist");
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    flush();
    readfile($file);
    exit;
}
function dump_table($d_table, $d_database, $name, $n, $r) {
    $dump_file = "";
    $n2 = $n + 1;
    $file_name = $name;
    $table_dump = mysql_query("SELECT * FROM $d_database.$d_table");
    $table_dump2 = mysql_query("SELECT COUNT(*) as num_rows FROM $d_database.$d_table");
    $res = mysql_fetch_object($table_dump2);
    $num_rows = $res->num_rows;
    if ($num_rows > 50000) {
        echo "<script>window.location = './split_table.php?current=$n&next=$n2&db=$d_database&lim=0&table=$d_table&nr=$num_rows&r=$r';</script>";
        exit;
    } else {
        while ($table_data = mysql_fetch_assoc($table_dump)) {
            $avalues = array_values($table_data);
            $akeys = array_keys($table_data);
            $addslash = array_map('addSlashes', $avalues);
            $fields = implode("`, `", $akeys);
            $values = implode("', '", $addslash);
            $dump_file.= "INSERT INTO `$d_table` (`$fields`) VALUES ('$values');\n";
        }
        $result2 = mysql_query("SHOW CREATE TABLE $d_database.$d_table");
        $data2 = array();
        while ($create_table = mysql_fetch_row($result2)) {
            $ct = $create_table[1];
            $ct2 = "$ct;\n";
            array_push($data2, $ct2);
        }
        $dropc = "DROP TABLE IF EXISTS `$d_table`;\n";
        $createtc = $data2[0];
        $lock = "LOCK TABLES `$d_table` WRITE;\n";
        $unlock = "UNLOCK TABLES;\n\n";
        file_put_contents($file_name, $dropc, FILE_APPEND);
        file_put_contents($file_name, $createtc, FILE_APPEND);
        file_put_contents($file_name, $lock, FILE_APPEND);
        file_put_contents($file_name, $dump_file, FILE_APPEND);
        file_put_contents($file_name, $unlock, FILE_APPEND);
    }
}
function dump_table2($d_table, $d_database, $name, $limit, $last) {
    $dump_file = "";
    $limit2 = $limit * 10000;
    $file_name = $name;
    $table_dump = mysql_query("SELECT * FROM $d_database.$d_table LIMIT $limit2, 10000");
    while ($table_data = mysql_fetch_assoc($table_dump)) {
        $avalues = array_values($table_data);
        $akeys = array_keys($table_data);
        $addslash = array_map('addSlashes', $avalues);
        $fields = implode("`, `", $akeys);
        $values = implode("', '", $addslash);
        $dump_file.= "INSERT INTO `$d_table` (`$fields`) VALUES ('$values');\n";
    }
    $result2 = mysql_query("SHOW CREATE TABLE $d_database.$d_table");
    $data2 = array();
    while ($create_table = mysql_fetch_row($result2)) {
        $ct = $create_table[1];
        $ct2 = "$ct;\n";
        array_push($data2, $ct2);
    }
    $dropc = "DROP TABLE IF EXISTS `$d_table`;\n";
    $createtc = $data2[0];
    $lock = "LOCK TABLES `$d_table` WRITE;\n";
    $unlock = "UNLOCK TABLES;\n\n";
    if ($limit == 0) {
        file_put_contents($file_name, $dropc, FILE_APPEND);
        file_put_contents($file_name, $createtc, FILE_APPEND);
        file_put_contents($file_name, $lock, FILE_APPEND);
    }
    file_put_contents($file_name, $dump_file, FILE_APPEND);
    if ($limit == $last) {
        file_put_contents($file_name, $unlock, FILE_APPEND);
    }
}
function error($mesg) {
    $error = "<center><font size='4' color='#E42217'><b>$mesg</b></font></center>";
    echo "$error";
}
?>