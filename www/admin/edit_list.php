<?php
require "checkpwd.php";

require "../Database.php";
$db=new Database();

$dir="../images/import/";
$dir_screen="../images/screen/";
$dir_thumb="../images/thumbs/";

echo "<h1>Edit Images</h1><ul>";

//DB-Werte lesen
$dbresult=$db->select("SELECT `route`, `date`, count(*) as `counter` from `images` GROUP BY `route`, `date` ORDER BY `route` ASC, `date` ASC;");
while ($dbrow = $dbresult->fetch_object())
{
	$unedited=$db->count("SELECT * FROM `images` WHERE `route`='".$dbrow->route."' AND `date`=".$dbrow->date." AND `tp_mlc`=1 AND `tp_tsg`=1 AND `tp_hct`=1 AND `tp_plc`=1 AND `tp_oct`=1 AND `comment_de`='' AND `comment_en`='';");
	echo "<li>".$dbrow->route." - ".date("Y/m/d", $dbrow->date)." (".$dbrow->counter." images, ".($unedited>0?"<font color='red'>".$unedited." unedited</font>":"0 unedited").") ";
	echo "<a href='edit.php?d=".$dbrow->date."&r=".$dbrow->route."'>edit</a>";
	echo "</li>";
}

echo "</ul><a href=\".\">back</a>";

?>