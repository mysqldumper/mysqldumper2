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
$get_dbs = mysql_query("SHOW DATABASES");
echo "<div id='hover'>
<center>
<a href='./index.php'>[~Home~]</a>  
$username@$host 
<a href='./hash_dump.php'>[~Hash Dumper~]</a>
<br>
<table border='1' width='65%'>
<tr>
<th>Database</th>
<th>Tables</th>
<th>Dump</th>
<th>Drop</th>
</tr>";
while ($row = mysql_fetch_array($get_dbs)) {
    $db = $row[0];
    $num_tables = mysql_fetch_object(mysql_query("SELECT COUNT(TABLE_NAME) as num_rows FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$db'"));
    $num_tables2 = $num_tables->num_rows;
    echo "<tr>
<td><a href='?database=$db&limit=0'>$db</a></td>
<td align='center'>$num_tables2</td>
<td align='center'><a href='./dump_db.php?current=0&next=1&db=$db'>Dump</a></td>
<td align='center'><a href='?drop=database&db=$db'>Drop</a></td>
</tr>";
}
echo "</table></center></div><br>";
//Get tables in database selected
if (isset($_GET['database'])) {
    $database = $_GET['database'];
    $limit = $_GET['limit'];
    $limit2 = $limit + 50;
    $limit3 = $limit - 50;
    $tables_query = mysql_query("SELECT COUNT(TABLE_NAME) as num_rows FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database'");
    $tables_query2 = mysql_query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = '$database' LIMIT $limit, 50");
    $num = mysql_fetch_object($tables_query);
    $num2 = $num->num_rows;
    echo "<center>
<a href='?database=$database&limit=$limit3'><~~~~~ Previous Page</a>     
There are $num2 tables in database: $database     
<a href='?database=$database&limit=$limit2'>Next Page ~~~~~></a><br>
<div id='hover'>
<table width='95%' border='1'>
<tr>
<th>Table</th>
<th>Rows</th>
<th>Dump</th>
<th>Drop</th>
<th>Prune</th>
<th>Insert New Row</th>
</tr>";
    while ($row = mysql_fetch_array($tables_query2)) {
        $table_name = $row[0];
        $num_rows = mysql_query("SELECT COUNT(*) as num_rows FROM $database.$table_name");
        $nr = mysql_fetch_object($num_rows);
        $num_rows2 = $nr->num_rows;
        $lim = $_GET['limit'];
        echo "<tr>
<td><a href='?explore&db=$database&table=$table_name&limit=0'>$table_name</a></td>
<td align='center'>$num_rows2</td>";
        if ($num_rows2 > 50000) {
            echo "<td align='center'><a href='./split_table2.php?db=$database&table=$table_name&lim=0&nr=$num_rows2'>Dump</a></td>";
        } else {
            echo "<td align='center'><a href='./dump_table.php?db=$database&table=$table_name'>Dump</a></td>";
        }
        echo "<td align='center'><a href='?drop=table&db=$database&table=$table_name&limit=$lim'>Drop</a></td>
<td align='center'><a href='?prune=$table_name&db=$database&limit=$lim'>Prune</a></td>
<td align='center'><a href='?insert&db=$database&table=$table_name&limit=$lim'>Insert Row</a></td>
</tr>";
    }
    echo "</table></div></center>";
    $pages = floor($num2 / 50);
    $i = 0;
    while ($i <= $pages) {
        $lim2 = $i * 50;
        echo "<u><a href='?database=$database&limit=$lim2'>$i</a></u> ";
        $i++;
    }
}
//Drop DB and table stuff
if (isset($_GET['drop'])) {
    $type = $_GET['drop'];
    if ($type == "database") {
        $database = $_GET['db'];
        $drop_query = mysql_query("DROP DATABASE $database");
        if ($drop_query) {
            echo "<script>window.location = './index.php';</script>";
        } else {
            error("Failed to drop database or table!");
        }
    }
    if ($type == "table") {
        $database = $_GET['db'];
        $table = $_GET['table'];
        $lim = $_GET['limit'];
        $drop_query = mysql_query("DROP TABLE $database.$table");
        if ($drop_query) {
            echo "<script>window.location = './index.php?database=$database&limit=$lim';</script>";
        } else {
            error("Failed to drop database or table!");
        }
    }
}
//Prune table stuff
if (isset($_GET['prune'])) {
    $table = $_GET['prune'];
    $db = $_GET['db'];
    $lim = $_GET['limit'];
    $prune = mysql_query("TRUNCATE $db.$table");
    if ($prune) {
        echo "<script>window.location = '?database=$db&limit=$lim';</script>";
    } else {
        error("Failed to prune table!");
    }
}
//Insert row stuff
if (isset($_POST['insert_row'])) {
    $table = $_GET['table'];
    $db = $_GET['db'];
    $insertdata = array();
    $insertdata2 = array();
    foreach ($_POST as $test => $value) {
        if ($value == "Insert") {
        } else {
            $insertdata[$test] = $value;
            array_push($insertdata2, $test);
        }
    }
    $iquery = "INSERT INTO $db.$table (";
    $last = end($insertdata2);
    foreach ($insertdata2 as $data2) {
        if ($data2 == "$last") {
            $iquery.= "`$data2`";
        } else {
            $iquery.= "`$data2`, ";
        }
    }
    $iquery.= ") VALUES (";
    $last2 = end(array_keys($insertdata));
    foreach ($insertdata as $data => $dvalue) {
        $dvalue2 = addslashes($dvalue);
        if ($data == "$last2") {
            $iquery.= "'$dvalue2'";
        } else {
            $iquery.= "'$dvalue2', ";
        }
    }
    $iquery.= ")";
    if (mysql_query($iquery)) {
        $lim = $_GET['limit'];
        echo "<script>window.location = '?database=$db&limit=$lim';</script>";
    } else {
        error("Failed to insert row into table!");
    }
}
if (isset($_GET['insert'])) {
    $db = $_GET['db'];
    $table = $_GET['table'];
    $getcolumnsquery = mysql_query("SHOW COLUMNS FROM $db.$table");
    $insertcolumns = array();
    $types = array();
    while ($row = mysql_fetch_array($getcolumnsquery)) {
        $columns = $row['Field'];
        $type = $row['Type'];
        array_push($insertcolumns, $columns);
        $types[$columns] = $type;
    }
    echo "<center>
Inserting row into table: $table<br>
<form action='' method='post'>
<table cellpadding='8'>";
    foreach ($insertcolumns as $icolumns) {
        echo "<tr><td>$icolumns</td><td><input type='text' class='regular' name='$icolumns'></td><td>" . $types[$icolumns] . "</td></tr>";
    }
    echo "</table><input type='submit' name='insert_row' value='Insert'></form></center><br>";
}
//Delete row stuff
if (isset($_GET['delete'])) {
    $table = $_GET['table'];
    $database = $_GET['db'];
    $column = $_GET['col'];
    $value = $_GET['value'];
    $limit = $_GET['limit'];
    if (mysql_query("DELETE FROM $database.$table WHERE $column='$value'")) {
        echo "<script>window.location = '?explore&db=$database&table=$table&limit=$limit';</script>";
    } else {
        error("Failed to delete row!");
    }
}
//Explore table stuff
//Do search table
if (isset($_POST['do_search'])) {
    $table = $_GET['table'];
    $database = $_GET['db'];
    $limit = $_GET['limit'];
    $getcolumnsquery = mysql_query("SHOW COLUMNS FROM $database.$table");
    $s_column = $_POST['search_column'];
    $s_value = $_POST['search_value'];
    $search_q = mysql_query("SELECT * FROM $database.$table WHERE $s_column='$s_value'");
    $search_q2 = mysql_query("SELECT COUNT(*) as num_rows FROM $database.$table WHERE $s_column='$s_value'");
    $search_rows = mysql_fetch_object($search_q2);
    $search_rows2 = $search_rows->num_rows;
    if ($search_rows2 == 0) {
        error("No search results found!");
    } else {
        echo "<div id='hover'>
<center>
Search Results:<br>
<table border='1' width='95%'>
<tr>";
        $cdata = array();
        while ($row = mysql_fetch_array($getcolumnsquery)) {
            $columns = $row['Field'];
            array_push($cdata, $columns);
            echo "<th>$columns</th>";
        }
        echo "<th>Edit Row</th><th>Delete Row</th></tr>";
        while ($row = mysql_fetch_array($search_q)) {
            echo "<tr>";
            for ($i = 0;$i < count($cdata) + 2;$i++) {
                if ($i == count($cdata)) {
                    $r = $row[0];
                    $col2 = $cdata[0];
                    $output2 = "<td align='center'><a href='?edit&table=$table&db=$database&col=$col2&value=$r'>Edit</a></td>";
                } else if ($i == count($cdata) + 1) {
                    $r = $row[0];
                    $col2 = $cdata[0];
                    $output2 = "<td align='center'><a href='?delete&table=$table&db=$database&col=$col2&value=$r&limit=$limit'>Delete</a></td>";
                } else {
                    $output2 = "<td>" . htmlspecialchars($row[$i]) . "</td>";
                }
                echo "$output2";
            }
            echo "</tr>";
        }
		echo "</table></center></div><br><br>";
    }
}
//Get table contents
if (isset($_GET['explore'])) {
    $table = $_GET['table'];
    $database = $_GET['db'];
    $limit = $_GET['limit'];
    $limit2 = 500;
    $limit3 = $limit + 500;
    $limit4 = $limit - 500;
    $getcolumnsquery = mysql_query("SHOW COLUMNS FROM $database.$table");
    $lastp = mysql_fetch_object(mysql_query("SELECT COUNT(*) as num_rows FROM $database.$table"));
    $lastp2 = $lastp->num_rows - 500;
    echo "<center>
<form action='' method='post'>
Column: <input type='text' class='regular' name='search_column'>
Search Value: <input type='text' class='regular' name='search_value'>
<input type='submit' name='do_search' value='Search'></form></center><br>";
    echo "<div id='hover'>
<center>
<a href='?explore&db=$database&table=$table&limit=0'><~~~ First page</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='?explore&db=$database&table=$table&limit=$limit4'><~~~ Previous page</a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='?explore&db=$database&table=$table&limit=$limit3'>Next page ~~~></a>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href='?explore&db=$database&table=$table&limit=$lastp2'>Last page ~~~></a>
<table border='1' width='95%'>
<tr>";
    $data = array();
    $getcolumndata = mysql_query("SELECT * FROM $database.$table LIMIT $limit, $limit2");
    while ($row = mysql_fetch_array($getcolumnsquery)) {
        $columns = $row['Field'];
        array_push($data, $columns);
        echo "<th>$columns</th>";
    }
    echo "<th>Edit Row</th><th>Delete Row</th></tr>";
    while ($row = mysql_fetch_array($getcolumndata)) {
        echo "<tr>";
        for ($i = 0;$i < count($data) + 2;$i++) {
            if ($i == count($data)) {
                $r = $row[0];
                $col = $data[0];
                $output = "<td align='center'><a href='?edit&table=$table&db=$database&col=$col&value=$r&limit=$limit'>Edit</a></td>";
            } else if ($i == count($data) + 1) {
                $r = $row[0];
                $col = $data[0];
                $output = "<td align='center'><a href='?delete&table=$table&db=$database&col=$col&value=$r&limit=$limit'>Delete</a></td>";
            } else {
                $output = "<td>" . htmlspecialchars($row[$i]) . "</td>";
            }
            echo "$output";
        }
    }
    echo "</tr></table></center></div>";
    $allrows = mysql_query("SELECT COUNT(*) as num_rows FROM $database.$table");
    $numrows = mysql_fetch_object($allrows);
    $numrows4 = $numrows->num_rows;
    $numrows2 = floor($numrows4 / 500);
    $numrows3 = $numrows4 / 500;
    $i = 0;
    while ($i <= $numrows2) {
        $lim2 = $i * 500;
        echo "<u><a href='?explore&db=$database&table=$table&limit=$lim2'>$i</a></u> ";
        $i++;
    }
}
//Edit row stuff
if (isset($_POST['do_edit_table'])) {
    $table = $_GET['table'];
    $value2 = $_GET['value'];
    $column = $_GET['col'];
    $db = $_GET['db'];
	$lim = $_GET['limit'];
    $earray = array();
    $earray2 = array();
    foreach ($_POST as $epdata => $value) {
        if ($value == "Edit") {
            echo "";
        } else {
            $valuea = addslashes($value);
            $combine = "$epdata='$valuea'";
            array_push($earray, $combine);
            array_push($earray2, $epdata);
        }
    }
    $end = end($earray);
    $equery = "UPDATE $db.$table SET ";
    foreach ($earray as $edita) {
        if ($edita == "$end") {
            $equery.= "$edita";
        } else {
            $equery.= "$edita, ";
        }
    }
    $equery.= " WHERE $column='$value2'";
    if (mysql_query($equery)) {
        echo "<script>window.location = '?explore&db=$db&table=$table&limit=$lim';</script>";
    } else {
        echo "$equery";
        error("Falied to edit row in table!");
    }
}
if (isset($_GET['edit'])) {
    $db = $_GET['db'];
    $table = $_GET['table'];
    $col = $_GET['col'];
    $value = $_GET['value'];
    $getcolumnsquery = mysql_query("SHOW COLUMNS FROM $db.$table");
    $columns = array();
    $column_type = array();
    while ($row = mysql_fetch_array($getcolumnsquery)) {
        $columns2 = $row['Field'];
        $type = $row['Type'];
        array_push($columns, $columns2);
        array_push($column_type, $type);
    }
    $editdataq = mysql_query("SELECT * FROM $db.$table WHERE $col='$value'");
    echo "<form action ='' method='post'>
<center>
Editing row in table: $table<br>
<table>";
    while ($row = mysql_fetch_array($editdataq)) {
        for ($i = 0;$i < count($columns);$i++) {
            $row2 = $row[$i];
            $row3 = $columns[$i];
            $row4 = $column_type[$i];
            echo '<tr><td>' . $row3 . '<br>' . $row4 . '</td><td><textarea name="' . $row3 . '" class="regulartextarea" rows="6" cols="45">' . $row2 . '</textarea></td></tr>';
        }
    }
    echo "</table><input type='submit' name='do_edit_table' value='Edit'></form></center><br>";
}
?>