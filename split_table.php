<link rel="stylesheet" href="./styles/stylesheet.css" type="text/css" />
<div id="logo">
<center><a href='./index.php'><img src="./styles/images/logo.png"></a></center>
</div><br>
<?php
require_once ("./includes/functions.php");
$current = $_GET['current'];
$current2 = $current - 1;
$next = $_GET['next'];
$db = $_GET['db'];
$table = $_GET['table'];
$lim = $_GET['lim'];
$nr = $_GET['nr'];
$r = $_GET['r'];
$file_date = date("m_d_Y");
$file_name = $db . "_$file_date.sql";
$divide = floor($nr / 10000);
if (file_exists("./log.txt")) {
    echo "Table dump progress: $table<br>
<progress max='$divide' value='$lim'></progress><br>
Total database progress:<br>
<progress max='$r' value='$current2'></progress>";
    $content = file_get_contents('./log.txt');
    echo "<pre>
$content
</pre>";
}
if ($lim > $divide) {
    file_put_contents('./log.txt', "Dumped table: $table\n", FILE_APPEND);
    echo "<script>window.location = './dump_db.php?current=$current&next=$next&db=$db';</script>";
} else {
    //Dump table
    dump_table2($table, $db, $file_name, $lim, $divide);
    $lim2 = $lim + 1;
    echo "<script>window.location = 'split_table.php?current=$current&next=$next&db=$db&lim=$lim2&table=$table&nr=$nr&r=$r';</script>";
}
?>