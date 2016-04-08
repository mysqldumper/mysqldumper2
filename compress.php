<link rel="stylesheet" href="./styles/stylesheet.css" type="text/css" />
<div id="logo">
<center><a href='./index.php'><img src="./styles/images/logo.png"></a></center>
</div><br>
<?php
require_once ("./includes/functions.php");
$startingbyte = $_GET['sbytes'];
$length = "52428800"; //50 MB in bytes
$endbyte = $startingbyte + $length;
$filename = $_GET['name'];
$filesize = filesize($filename);
//Compression complete
if ($startingbyte > $filesize) {
    unlink($filename);
    echo "<font color='green'><b>Compression Complete!</b></font><br><br>";
    echo "<script>window.location = '$filename.gz';</script>";
} else {
    echo "Compression status:<br><progress max='$filesize' value='$endbyte'></progress>";
    $openfile = fopen($filename, "r");
    //Place file pointer
    fseek($openfile, $startingbyte);
    $readfile = fread($openfile, $length);
    $gzfile = "$filename.gz";
    $gzop = gzopen($gzfile, 'a9');
    gzwrite($gzop, $readfile);
    gzclose($gzop);
    fclose($openfile);
    echo "<script>window.location = 'compress.php?sbytes=$endbyte&name=$filename';</script>";
}
?>