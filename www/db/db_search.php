<?php
require "Database.php";
$db=new Database();

require INNER_WEBROOT."db/criteria.php";

echo "<div id='searchbody'>";
echo "<form action='?' method='post' name='tme'>";
if (!isset($_REQUEST["helpshown"]))
{
	$_REQUEST["helpshown"]=0;
}
if ($_REQUEST["site"]=="searchhelp")
{
	$_REQUEST["helpshown"]=1;
}
echo "<input type='hidden' name='helpshown' value='".$_REQUEST["helpshown"]."'>";
echo "<input type='hidden' name='showhelp' value='".(1-$_REQUEST["helpshown"])."'>";
echo "<input type='hidden' name='site' value='".$_REQUEST["site"]."'>";
echo "<input type='hidden' name='lang' value='".$_REQUEST["lang"]."'>";
echo "<input type='hidden' name='limit' value='0'>";
echo "<input type='hidden' name='ppp' value='".$lang["ppp_std"]."'>";

//HTML für Suche aufbauen und ausgeben
foreach ($criteria as $groupname => $group)
{
	echo "<div class='searchgroup'>";
	echo "<div class='searchgrouptitle'>";
	echo $group["display"];
	echo "</div>";
	echo "<div class='searchgroupbody'>";
	foreach($group["fields"] as $fieldname => $field)
	{
		echo "<div class='searchcrit'>";
		echo "<div class='searchcrittitle'>";
		echo $field["display"];
		echo "</div>";
		echo "<div class='searchcritbody'>";
		if ($field["type"] == "dd") //Dropdown?
		{
			if (!$field["found"] && isset($_REQUEST[$fieldname]))
			{
				unset($_REQUEST[$fieldname]);
				$field["selected"]=false;
			}
			elseif ($field["selected"])
			{
				$_REQUEST[$fieldname]=$field["selected"];			
			}
			echo "<select".($field["active"]?"":" class='dis'")." name='".$fieldname."' size='1'".($field["active"]?"":" disabled='disabled'").($field["autosubmit"]?" onChange='document.tme.submit()'":"").">";

			foreach($field["values"] as $value)
			{
				echo "<option value='".$value["value"]."'".(($value["value"]==$field["selected"] && $field["selected"]!==false)?" selected='selected'":"").">";
				echo $value["display"];
				echo "</option>";
			}
			echo "</select>";
		}
		elseif ($field["type"] == "cb") //Checkboxes?
		{
			$max=count($field["values"])-1;
			foreach($field["values"] as $akt => $value)
			{
				echo "<input style='margin:0 10 0 10;width:13px;height:13px;overflow:hidden;' type='checkbox' name='".$fieldname."[".$value["value"]."]' value='1' ".($value["checked"]?" checked='checked'":"").($field["active"]?"":" disabled='disabled'")."/>".$value["display"];
				if ($akt < $max)
				{
					echo "<br />\n";
				}
			}
		}
		elseif ($field["type"] == "txt") //Textfelder?
		{
			echo "<input".($field["active"]?"":" class='dis'")." type='text' onChange='document.tme.site.value=\"search\";' id='".$fieldname."'  name='".$fieldname."' size='30' value='".$field["selected"]."' ".($field["active"]?"":" disabled='disabled'")."/>";
		}
		else
		{
//			var_dump($criteria);
//			die("Unknown field type ".$fieldname);
		}
		echo "</div>";
		echo "</div>";
	}
	echo "</div>";
	echo "</div>";
}

echo "<input style='color:#".($criteria["comment"]["fields"]["comment"]["active"]?"FFFFFF; cursor:pointer":"999999")."; background-color: #495F83; margin: 10 5 10 10;  border:0px;' type='button' value='".$lang["submit"]."' onMouseOver='this.style.backgroundColor=\"#313f57\"' onMouseOut='this.style.backgroundColor=\"#495F83\"' onClick='document.tme.site.value=\"search\";document.tme.submit()'".($criteria["comment"]["fields"]["comment"]["active"]?"":" disabled='disabled'").">";
echo "</form>";
echo "</div>";
echo "</div>";
?>