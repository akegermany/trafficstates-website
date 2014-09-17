<?php
//TP-Block
$tps=array();
foreach ($lang["tp"] as $tp => $temp)
{
	if (isset($_REQUEST["traffic_pattern"][$tp]))
	{
		$tps[]="`tp_".$tp."` > 0";
	}
}
if ($tps != array() && count($tps) != count($lang["tp"]))
{
	$tps=" AND (".implode(" OR ", $tps).")";
}
else
{
	$tps="";
}

$sql = "WHERE";
$sql.=isset($_REQUEST["route"])?" `route` = '".$_REQUEST["route"]."'":" 1 = 1";
$sql.=(isset($_REQUEST["direction"]) && $_REQUEST["direction"] != "0" && $_REQUEST["direction"] != "-1")?" AND `direction` = '".$_REQUEST["direction"]."'":"";
if (isset($_REQUEST["location"]) && $_REQUEST["location"] != "0" && $_REQUEST["location"] != "-1")
{
	$loc=explode("-", $_REQUEST["location"]);
	$sql.=" AND `location_from` = '".$loc[0]."' AND `location_to` = '".$loc[1]."'";
}
$sql.=isset($_REQUEST["date_from"])?" AND `date` >= '".$_REQUEST["date_from"]."'":"";
$sql.=isset($_REQUEST["date_to"])?" AND `date` <= '".$_REQUEST["date_to"]."'":"";
$sql.=(isset($_REQUEST["date_wd"]) && $_REQUEST["date_wd"]!=-1)?" AND `date_wd` = '".$_REQUEST["date_wd"]."'":"";
if (isset($_REQUEST["date_time"]) && $_REQUEST["date_time"] > 0)
{
	$hour=explode("-", $_REQUEST["date_time"]);
	$sql.=" AND `hour_start` = '".$hour[0]."' AND `hour_end` = '".$hour[1]."'";
}
if ($_REQUEST["comment"] != "")
{
	$comments=explode(" ", $_REQUEST["comment"]);
	foreach($comments as $comment)
	{
		if (!trim($comment) == "")
		{
			$sql.=" AND (`comment_de` LIKE '%".$comment."%' OR ".
			" `comment_en` LIKE '%".$comment."%' OR ".
			" `comment_int` LIKE '%".$comment."%')";
		}
	}
}
$sql.=$tps;

$sql.=" ORDER BY `route` ASC, `direction` ASC, `date` ASC, `hour_start` ASC";

$sql_all="SELECT * FROM `images` ".$sql." LIMIT ".intval($_REQUEST["limit"]).", ".intval($_REQUEST["ppp"]).";";
$sql_count="SELECT count(*) as `count` FROM `images` ".$sql.";";

$images= $db->select($sql_all);
$count= $db->selectOne($sql_count);

$counter=0;
echo "<div id='content'>";
echo "<div id='contentheader'>";
echo "<h2>".$lang["header"]."</h2>";
echo "<form action='' method='post' name='tme_4'>";
echo $lang["found_0"]." ".$count->count." ".$lang["found_1"].", ".$lang["display_0"]." ".($_REQUEST["limit"]/intval($_REQUEST["ppp"])+1)." ".$lang["display_1"]." ".ceil($count->count/(intval($_REQUEST["ppp"])));
echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$lang["ppp"].":&nbsp;&nbsp;";
foreach ($_REQUEST as $key => $value)
{
	if ($key=="limit")
	{
		echo "<input type='hidden' name='".$key."' value='0'>";
	}
	elseif (is_array($value))
	{
		foreach ($value as $key2 => $value2)
		{
			echo "<input type='hidden' name='".$key."[".$key2."]' value='".$value2."'>";
		}
	}
	elseif ($key=="ppp")
	{
		echo "<span id='pppdrop'>";
		echo "<select name='ppp' size='1' onChange='document.tme_4.submit()'>";
		foreach($lang["ppp_values"] as $value_all)
		{
			echo "<option value='".$value_all."'".(($value_all==$value)?" selected='selected'":"").">";
			echo $value_all;
			echo "</option>";
		}

		echo "</select></span>";
	}
	else
	{
		echo "<input type='hidden' name='".$key."' value='".$value."'>";
	}
}
echo "</form>";


$disable=false;
echo "<span id='picturenavi'>";
echo "<span id='previousbutton'>";
echo "<form action='' method='post' name='tme_2'>";
foreach ($_REQUEST as $key => $value)
{
	if ($key=="limit")
	{
		if ($value==0)
		{
			$disable = true;
		}
		echo "<input type='hidden' name='".$key."' value='".($value-(intval($_REQUEST["ppp"])))."'>";
	}
	elseif (is_array($value))
	{
		foreach ($value as $key2 => $value2)
		{
			echo "<input type='hidden' name='".$key."[".$key2."]' value='".$value2."'>";
		}
	}
	else
	{
		echo "<input type='hidden' name='".$key."' value='".$value."'>";
	}
}
echo "<input id='".($disable?"prevdisabled":"prevenabled")."' type='submit' value='".$lang["left"]."'".($disable?" disabled='disabled'":"").">";
echo "</form>";
echo "</span>";
$disable=false;
echo "<span id='nextbutton'>";
echo "<form action='' method='post' name='tme_3'>";
foreach ($_REQUEST as $key => $value)
{
  	if ($key=="limit")
	{
		if ($value+(intval($_REQUEST["ppp"])) >= $count->count)
		{
			$disable = true;
		}
		echo "<input type='hidden' name='".$key."' value='".($value+(intval($_REQUEST["ppp"])))."'>";
	}
	elseif (is_array($value))
	{
		foreach ($value as $key2 => $value2)
		{
			echo "<input type='hidden' name='".$key."[".$key2."]' value='".$value2."'>";
		}
	}
	else
	{
		echo "<input type='hidden' name='".$key."' value='".$value."'>";
	}
}

echo "<input id='".($disable?"nextdisabled":"nextenabled")."' type='submit' value='".$lang["right"]."'".($disable?" disabled='disabled'":"").">";
echo "</form>";
echo "</span>";
echo "</span>";
echo "</div>";
echo "<br/>";
echo "<br/>";
echo "<div id='contentbody'>";

//JS, um Popups zu generieren
echo "<script type='text/javascript'>
function popup(id, html)
{
	document.getElementById(id).style.opacity = '0.6';
	var IE = navigator.userAgent.indexOf(\"MSIE\") >= 0;
	if(IE) {
		//document.getElementById(id).style.border = '1px solid #495F83';
		document.getElementById(id).style.filter = 'alpha(opacity=40)';
		}
	var fenster;
	fenster=window.open('', 'TME', 'width=800, height=620, scrollbars=1');
	fenster.document.write('<html><head><title>TME</title></head><body>' + html + '</body></html>');
	fenster.document.close();
}
</script>";
echo "<div id='gallerie'>";
while ($image = $images->fetch_object())
{
	$counter++;

	echo "<div id='thumb'><a href=\"javascript:popup('bild".$counter."', '<img src=images/screen/".$image->filename."><br/><hr/><code>".
	$lang["route"].": ".$lang["highway"]." ".$image->route.", ".$lang["direction"]." ".$image->direction.", ".$lang["location"]." ".$image->location_from."-".$image->location_to."<br />".
	$lang["date_date"].": ".date($lang["date"], $image->date)." (".$lang["days"][$image->date_wd]."), ".$lang["date_time"]." ".sprintf("%02.0f", $image->hour_start).":00-".sprintf("%02.0f", $image->hour_end).":00<br />".
	$lang["traffic_pattern"].": ";
	$tpa=array();
	foreach($lang["tp"] as $id => $tp)
	{
		$name="tp_".$id;
		if ($image->$name > 0 && $id != "all")
		{
			$count_str="";
			if ($image->$name > 1)
			{
				$count_str=" (".$image->$name."x)";
			}
			$tpa[]=$tp.$count_str;
		}
	}
	echo implode(", ", $tpa)."<br />";
	echo $lang["comment_result"].": ";
	if ($_REQUEST["lang"] == "de")
	{
		echo $image->comment_de."<br/>";
		echo $lang["comment_result"]." en: ".$image->comment_en;
	}
	else
	{
		echo $image->comment_en."<br/>";
		echo $lang["comment_result"]." de: ".$image->comment_de;
	}
	echo "<br/>".$lang["file"].": ".$image->filename."";
	echo "</code>')\">";
	if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') === false))
	{
		echo "<div class='picshad5'><div class='picshad4'><div class='picshad3'><div class='picshad2'><div class='picshad1'>";
	}
	echo "<img id=\"bild".$counter."\" src=\"images/thumbs/tn_".$image->filename.".jpg\" border=\"0\">";
	if (isset($_SERVER['HTTP_USER_AGENT']) && (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') === false))
	{
		echo "</div></div></div></div></div>";
	}
	echo "</a><div class='pictext'><br/>";
	echo $image->route.$image->direction.", ".date($lang["date"], $image->date)." (".$lang["days"][$image->date_wd].")";
	echo "</div></div>";
}
if ($count->count == 0)
{
	echo "<h3>".$lang["no_result"]."</h3>";
}

echo "</div>";
echo "</div>";
echo "</div>";
?>