<?php
require "checkpwd.php";

require "../Database.php";
$db=new Database();

echo "<h1>Reindex Images</h1>";
$num=0;

//Sprachdaten
require "../html/db_de.php";
$days_de=$lang["days"];
$vm_de=$lang["tp"];
$vm_de=$lang["tp"];
$date_de=$lang["date"];
require "../html/db_en.php";
$days_en=$lang["days"];
$vm_en=$lang["tp"];
$date_en=$lang["date"];

$dbresult=$db->select("SELECT * from `images`;");

while ($dbrow = $dbresult->fetch_object())
{
	$set=array();
	$val="";
	//Wochentag
	$val.=$days_en[$dbrow->date_wd]." ";
	$val.=$days_de[$dbrow->date_wd]." ";
	//Filename
	$val.=$dbrow->filename." ";
	//Verkehrsmuster
	foreach($vm_de as $key => $value)
	{
		$prop="tp_".$key;
		if ($dbrow->$prop > 0)
		{
			$val.=$value." ";
		}
	}
	foreach($vm_en as $key => $value)
	{
		$prop="tp_".$key;
		if ($dbrow->$prop > 0)
		{
			$val.=$value." ";
		}
	}
	//Datum
	$val.=date($date_de, $dbrow->date)." ";
	$val.=date($date_en, $dbrow->date)." ";
	
	$set[]="`comment_int` = '".str_replace(array("<", ">", "'", '"', "\n", "\r"), "", $val)."'";
	$num+=$db->execute("UPDATE `images` SET ".implode(", ",$set)." WHERE `id`=".$dbrow->id.";");
}

echo $num." affeted row(s).<br />";
echo "<a href=\"index.php\">back</a>";

?>