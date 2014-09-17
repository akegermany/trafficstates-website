<?php
require "checkpwd.php";

require "../Database.php";
$db=new Database();

echo "<h1>Delete set of images</h1><ul>";

//DB-Werte lesen
$dbresult=$db->select("SELECT `route`, `date` from `images` ORDER BY `route` ASC, `date` ASC;");

$akt_year=0;
$akt_date=0;
$akt_route=0;
$img_count=0;

while ($dbrow = $dbresult->fetch_object())
{
	if ($dbrow->route !== $akt_route)
	{
		if ($akt_route != 0)
		{
			echo "(".$img_count." images)</li>";
		}
		$akt_route=$dbrow->route;
		$akt_year=0;
		echo "\n\n<li>";
		echo $dbrow->route.": ";

	}
		
	if ($dbrow->date != $akt_date && date("Y", $dbrow->date) != $akt_year)
	{
		if ($akt_year != 0)
		{
			echo "(".$img_count." images), ";
		}
		$akt_year=date("Y", $dbrow->date);
		echo "<a href=\"";
		echo "del.php?route=".$akt_route."&year=".$akt_year;
		echo "\">".$akt_year."</a> ";
		$img_count=1;
	}
	else
	{
		$akt_date = $dbrow->date;
		$img_count++;
	}
}
echo "(".$img_count." images)</li>";
echo "</ul><a href=\".\">back</a>";

?>