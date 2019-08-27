<link rel="stylesheet" href="./styles/stylesheet.css" type="text/css" />
<div id="logo">
<center><a href='./index.php'><img src="./styles/images/logo.png"></a></center>
</div><br>
<?php
function CleanDir($directory) {
    $directory = str_replace("\\", "/", $directory);
    $directory = str_replace("//", "/", $directory);
    return $directory;
}
if (isset($_POST['install'])) {
    $username = $_POST['mysql_username'];
    $password = $_POST['mysql_password'];
    $host = $_POST['mysql_host'];
    $port = $_POST['mysql_port'];
    try {
        $pdo = new PDO('mysql:host='.$host.';port='.$port.';dbname=;charset=utf8', 
        $username,
        $password, 
        array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );
    } catch(PDOException $e) {
        die("MySQL Information was not correct!");
    }
    $var = "$";
    $qoute = '"';
    $host2 = $var . "host";
    $port2 = $var . "port";
    $username2 = $var . "username";
    $password2 = $var . "password";
    $pdo2 = $var . "pdo";
    $e2 = $var . "e";
    $memory = $qoute . "memory_limit" . $qoute;
    $memory2 = $qoute . "9999M" . $qoute;
    $input = $qoute . "max_input_time" . $qoute;
    $execution = $qoute . "max_execution_time" . $qoute;
    $config_file = "<?php
//Set some ini settings
//These are crucial so don't edit them
@ini_set($memory,$memory2);
@ini_set($input, 0);
@ini_set($execution, 0);

//MySQL Configuration
$host2 = '$host';
$port2 = '$port';
$username2 = '$username';
$password2 = '$password';
try {
    $pdo2 = new PDO('mysql:host='.$host2.';port='.$port2.';dbname=;charset=utf8', 
    $username2,
    $password2, 
    array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch(PDOException $e2) {
    die(".$e2."->getMessage());
}
?>";
    if (file_put_contents("./includes/configuration.php", $config_file)) {
        echo "<script>window.location = './index.php';</script>";
    } else {
         echo "<center><font size='4' color='#E42217'><b>Failed to write config file!</b></font></center>";
    }
}
$root_path = CleanDir($_SERVER['DOCUMENT_ROOT']);
$path1 = "$root_path/includes/config.php";
$path2 = "$root_path/forum/includes/config.php";
$path3 = "$root_path/forums/includes/config.php";
$path4 = "$root_path/foro/includes/config.php";
$path5 = "$root_path/foros/includes/config.php";
$path6 = "$root_path/board/includes/config.php";
$path7 = "$root_path/community/includes/config.php";
$path8 = "$root_path/vb/includes/config.php";
$path9 = "$root_path/vbulletin/includes/config.php";
if (file_exists($path1)) {
    include ($path1);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} elseif (file_exists($path2)) {
    include ($path2);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} elseif (file_exists($path3)) {
    include ($path3);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} elseif (file_exists($path4)) {
    include ($path4);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} elseif (file_exists($path5)) {
    include ($path5);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} elseif (file_exists($path6)) {
    include ($path6);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} elseif (file_exists($path7)) {
    include ($path7);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} elseif (file_exists($path8)) {
    include ($path8);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} elseif (file_exists($path9)) {
    include ($path9);
    $confighost = $config['MasterServer']['servername'];
    $configuser = $config['MasterServer']['username'];
    $configpassword = $config['MasterServer']['password'];
    $configport = $config['MasterServer']['port'];
    echo "<center><b><font color='green'>vB config information found!</font></b></center>";
} else {
    $confighost = "localhost";
    $configuser = "";
    $configpassword = "";
    $configport = "3306";
    echo "<center><b><font color='red'>No vB config information found!</font></b></center>";
}
echo "<center>
MySQL Dumper Install
<form action='' method='post'>
<table cellpadding='15'>
<tr>
<td>Username:</td>
<td><input type='text' name='mysql_username' value='$configuser' class='regular'></td>
</tr>
<tr>
<td>Password:</td>
<td><input type='password' name='mysql_password' value='$configpassword' class='regular'></td>
</tr>
<tr>
<td>Host:</td>
<td><input type='text' name='mysql_host' class='regular' value='$confighost'></td>
</tr>
<tr>
<td>Port:</td>
<td><input type='text' name='mysql_port' class='regular' value='$configport' size='5'></td>
</tr>
</table><input type='submit' name='install' value='Install'></form></center>";
?>
