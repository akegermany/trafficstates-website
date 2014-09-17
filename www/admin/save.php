<?php
require "checkpwd.php";

require "../Database.php";
$db=new Database();

echo "<h1>Edit Images</h1>";
$num=0;

foreach ($_REQUEST["id"] as $id => $values)
{
	$dir="../images/import/";
	$dir_screen="../images/screen/";
	$dir_thumb="../images/thumbs/";

	$set=array();
	
	$set[]="`comment_de` = '".str_replace(array("<", ">", "'", '"', "\n", "\r"), "", $values["comment_de"])."'";
	$set[]="`comment_en` = '".str_replace(array("<", ">", "'", '"', "\n", "\r"), "", $values["comment_en"])."'";
	$set[]="`tp_mlc` = ".($values["mlc"])."";
	$set[]="`tp_tsg` = ".($values["tsg"])."";
	$set[]="`tp_hct` = ".($values["hct"])."";
	$set[]="`tp_plc` = ".($values["plc"])."";
	$set[]="`tp_oct` = ".($values["oct"])."";
	
	if (isset($values["delete"]) && $values["delete"] == 1)
	{
		$img=$db->selectOne("SELECT * FROM `images` WHERE `id`=".$id.";");
		unlink($dir_screen.$img->filename);
		unlink($dir_thumb."tn_".$img->filename.".jpg");
		$num+=$db->execute("DELETE FROM `images` WHERE `id`=".$id.";");

	}
	else
	{
		$num+=$db->execute("UPDATE `images` SET ".implode(", ",$set)." WHERE `id`=".$id.";");
	}
	
}

echo $num." affeted row(s).<br />";
echo "<a href=\"edit_list.php\">back</a>";

?>