<?php
require "checkpwd.php";

require "../Database.php";
$db=new Database();

echo "<h1>Edit Images</h1><table border='1'>";

echo "<form action='save.php' method='post' name='tme'>";
echo "<tr>";
echo "<td>ID</td>";
echo "<td></td>";
echo "<td>Thumbnail</td>";
echo "<td>Traffic Pattern</td>";
echo "<td>Comment DE</td>";
echo "<td>Comment EN</td>";
echo "<td>Delete</td>";
echo "</tr>";

//DB-Werte lesen
$dbresult=$db->select("SELECT * from `images` WHERE `route`='".$_REQUEST["r"]."' AND `date`=".$_REQUEST["d"].";");
while ($dbrow = $dbresult->fetch_object())
{
	echo "<tr>";
	echo "<td";
	if ($dbrow->tp_mlc.$dbrow->tp_tsg.$dbrow->tp_hct.$dbrow->tp_plc.$dbrow->tp_oct.$dbrow->comment_de.$dbrow->comment_en == "11111")
	{
		echo " bgcolor='red'";
	}
	echo ">".$dbrow->id."</td>";
	echo "<td>Dir from: ".$dbrow->direction."<br />";
	echo "Loc from: ".$dbrow->location_from."<br />";
	echo "Loc to: ".$dbrow->location_to."<br />";
	echo "Time from: ".$dbrow->hour_start."<br />";
	echo "Time to: ".$dbrow->hour_end."</td>";
	echo "<td><img src=\"../images/thumbs/tn_".$dbrow->filename.".jpg\" border=\"0\" /></td>";
	echo "<td>";
	echo "<input type='text' name='id[".$dbrow->id."][mlc]' size='1' maxlength='2' value='".$dbrow->tp_mlc."' /> MLC <br />";
	echo "<input type='text' name='id[".$dbrow->id."][plc]' size='1' maxlength='2' value='".$dbrow->tp_plc."' /> PLC <br />";
	echo "<input type='text' name='id[".$dbrow->id."][tsg]' size='1' maxlength='2' value='".$dbrow->tp_tsg."' /> TSG <br />";
	echo "<input type='text' name='id[".$dbrow->id."][oct]' size='1' maxlength='2' value='".$dbrow->tp_oct."' /> OCT <br />";
	echo "<input type='text' name='id[".$dbrow->id."][hct]' size='1' maxlength='2' value='".$dbrow->tp_hct."' /> HCT";
	echo "</td>";
	echo "<td>";
	echo "<textarea name='id[".$dbrow->id."][comment_de]' cols='30' rows='6'>".$dbrow->comment_de."</textarea>";
	echo "</td>";
	echo "<td>";
	echo "<textarea name='id[".$dbrow->id."][comment_en]' cols='30' rows='6'>".$dbrow->comment_en."</textarea>";
	echo "</td>";
	echo "<td>";
	echo "<input type='checkbox' name='id[".$dbrow->id."][delete]' value='1' /><font color='red'><b>DELETE THIS IMAGE</b></font>";
	echo "</tr>";
}

echo "</table>";
echo "<input type='submit' value='Save/Delete'>";
echo "</form><br />";
echo "<a href=\"edit_list.php\">back</a>";

?>