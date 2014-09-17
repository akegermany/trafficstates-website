<?php
require "checkpwd.php";

require "../Database.php";
$db=new Database();

echo "<h1>Delete set of Images</h1>";

$route=$_REQUEST["route"];
$year=$_REQUEST["year"];
echo "Deleting ".$route.", year ".$year."<br/><br/>";

$dir="../images/import/";
$dir_screen="../images/screen/";
$dir_thumb="../images/thumbs/";

//Datumszeitraum berechnen
$date_from=mktime (0, 0, 0, 1, 1, $year);
$date_to=mktime (0, 0, 0, 1, 1, $year+1)-1;

//Zu löschende Bilder holen
$dbresult=$db->select("SELECT * FROM `images` WHERE `route` = '".$route."' AND `date` >= ".$date_from." AND `date` <= ".$date_to.";");

//Zähler "gelöschte Bilder"
$num=0;

while ($img = $dbresult->fetch_object())
{
	unlink($dir_screen.$img->filename);
	unlink($dir_thumb."tn_".$img->filename.".jpg");
	$num+=$db->execute("DELETE FROM `images` WHERE `id`=".$img->id.";");
}

echo $num." affeted row(s).<br />";
echo "<a href=\"del_set.php\">back</a>";

?>