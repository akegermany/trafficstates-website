<?php
//Where-Klause
$where="WHERE `id` > 0";

//Kriterien zur späteren Anzeige in Array sammeln
$criteria=array();

$_REQUEST["route"]="A5";

//Standard-Feld "Dropdown"
$type_dd=array();
$type_dd["type"]="dd";
$type_dd["active"]=false;
$type_dd["values"]=array();
$type_dd["selected"]=false;
$type_dd["autosubmit"]=false;
$type_dd["found"]=false;

//Standard-Feld "Choose"
$field_choose=array();
$field_choose["display"]=$lang["choose"];
$field_choose["value"]="-1";

//Standard-Feld "any"
$field_any=array();
$field_any["display"]=$lang["any"];
$field_any["value"]="0";

//////////////////////////////////////////////////////////////////////////////
//Gruppe "Route"
$criteria["route"]=array();
$criteria["route"]["display"]=$lang["route"];
$criteria["route"]["fields"]=array();
//////////////////////////////////////////////////////////////////////////////
//Auswahlfeld zur Autobahn
$criteria["route"]["fields"]["route"]=$type_dd;
$criteria["route"]["fields"]["route"]["display"]=$lang["highway"];
$criteria["route"]["fields"]["route"]["active"]=true;
$criteria["route"]["fields"]["route"]["autosubmit"]=true;
$criteria["route"]["fields"]["route"]["found"]=true;
$where_alt=$where;
if (isset($_REQUEST['route']) && $_REQUEST['route'] != "-1" && $_REQUEST['route'] != "0")
{
	$criteria["route"]["fields"]["route"]["selected"]=$_REQUEST['route'];
	$where.=" AND `route` = '".$_REQUEST["route"]."'";
}
//Werte füllen
if (false) //False=Standardbelegung, True=Werte aus DB lesen
{
	$criteria["route"]["fields"]["route"]["values"][]=$field_choose;
	$dbresult = $db->select("SELECT DISTINCT `route` as 'name' FROM `images` ORDER BY `route` ASC;");
	while ($dbrow = $dbresult->fetch_object())
	{
		$criteria["route"]["fields"]["route"]["values"][]=array("display" => $dbrow->name, "value" => $dbrow->name);
	}
	$dbresult->free();
}
$criteria["route"]["fields"]["route"]["values"][]=$field_choose;

//DB-Zugriff vermeiden
$criteria["route"]["fields"]["route"]["values"][]=array("display" => "A5", "value" => "A5");

//Wenn es nur einen Wert gibt, dann nimm den automatisch
if (count($criteria["route"]["fields"]["route"]["values"]) == 2)
{
	$criteria["route"]["fields"]["route"]["selected"]=$criteria["route"]["fields"]["route"]["values"][1]["value"];
}

//////////////////////////////////////////////////////////////////////////////
//Auswahlfeld zur Richtung
$criteria["route"]["fields"]["direction"]=$type_dd;
$criteria["route"]["fields"]["direction"]["display"]=$lang["direction"];
$criteria["route"]["fields"]["direction"]["values"][]=$field_choose;
$criteria["route"]["fields"]["direction"]["autosubmit"]=true;
$where_alt=$where;
if ($criteria["route"]["fields"]["route"]["found"] && isset($_REQUEST["direction"]) && $_REQUEST["direction"] != "-1" && $_REQUEST["direction"] != "0")
{
	$criteria["route"]["fields"]["direction"]["selected"]=$_REQUEST["direction"];
	$where.=" AND `direction` = '".$_REQUEST["direction"]."'";
}
if($criteria["route"]["fields"]["route"]["found"])
{
	$criteria["route"]["fields"]["direction"]["active"]=true;
	$dbresult = $db->select("SELECT DISTINCT `direction` as `name` FROM `images` ".$where_alt." ORDER BY `direction` ASC;");
	while ($dbrow = $dbresult->fetch_object())
	{
		$criteria["route"]["fields"]["direction"]["values"][]=array("display" => $dbrow->name, "value" => $dbrow->name);
		$criteria["route"]["fields"]["direction"]["found"]=($dbrow->name == $criteria["route"]["fields"]["direction"]["selected"])?true:$criteria["route"]["fields"]["direction"]["found"];
	}
	$dbresult->free();

	//Wenn es nur einen Wert gibt, dann nimm den automatisch
	if (count($criteria["route"]["fields"]["direction"]["values"]) == 2)
	{
		$criteria["route"]["fields"]["direction"]["found"]=true;
		$criteria["route"]["fields"]["direction"]["selected"]=$criteria["route"]["fields"]["direction"]["values"][1]["value"];
		$where=$where_alt." AND `direction` = '".$criteria["route"]["fields"]["direction"]["selected"]."'";
	}
}
if ($criteria["route"]["fields"]["direction"]["selected"] && !$criteria["route"]["fields"]["direction"]["found"])
{
	$where=$where_alt;
}
//////////////////////////////////////////////////////////////////////////////
//Auswahlfeld zur Location
$criteria["route"]["fields"]["location"]=$type_dd;
$criteria["route"]["fields"]["location"]["display"]=$lang["location"];
$criteria["route"]["fields"]["location"]["values"][]=$field_choose;
$criteria["route"]["fields"]["location"]["autosubmit"]=true;
$where_alt=$where;
if ($criteria["route"]["fields"]["direction"]["found"] && isset($_REQUEST["location"]) && $_REQUEST["location"] != "-1" && $_REQUEST["location"] != "0")
{
	$criteria["route"]["fields"]["location"]["selected"]=$_REQUEST["location"];
	$loc=explode("-", $_REQUEST["location"]);
	$where.=" AND `location_from` = '".$loc[0]."' AND `location_to` = '".$loc[1]."'";
}
if($criteria["route"]["fields"]["direction"]["found"])
{
	$criteria["route"]["fields"]["location"]["active"]=true;
	$dbresult = $db->select("SELECT DISTINCT `location_from`, `location_to` FROM `images` ".$where_alt."ORDER BY `location_from` ASC, `location_to` DESC;");
	while ($dbrow = $dbresult->fetch_object())
	{
		$criteria["route"]["fields"]["location"]["values"][]=array("display" => $dbrow->location_from."-".$dbrow->location_to, "value" => $dbrow->location_from."-".$dbrow->location_to);
		$criteria["route"]["fields"]["location"]["found"]=($dbrow->location_from."-".$dbrow->location_to == $criteria["route"]["fields"]["location"]["selected"])?true:$criteria["route"]["fields"]["location"]["found"];
	}
	$dbresult->free();
	//Wenn es nur einen Wert gibt, dann nimm den automatisch
	if (count($criteria["route"]["fields"]["location"]["values"]) == 2)
	{
		$criteria["route"]["fields"]["location"]["found"]=true;
		$criteria["route"]["fields"]["location"]["selected"]=$criteria["route"]["fields"]["location"]["values"][1]["value"];
		$loc=explode("-", $criteria["route"]["fields"]["location"]["selected"]);
		$where=$where_alt." AND `location_from` = '".$loc[0]."' AND `location_to` = '".$loc[1]."'";
	}
}
if ($criteria["route"]["fields"]["location"]["selected"] && !$criteria["route"]["fields"]["location"]["found"])
{
	$where=$where_alt;
}
//////////////////////////////////////////////////////////////////////////////
//Gruppe "Zeit"
$criteria["time"]=array();
$criteria["time"]["display"]=$lang["time"];
$criteria["time"]["fields"]=array();
//////////////////////////////////////////////////////////////////////////////
//Auswahlfeld zum Datum (Start)
$criteria["time"]["fields"]["date_from"]=$type_dd;
$criteria["time"]["fields"]["date_from"]["display"]=$lang["date_from"];
if ($criteria["route"]["fields"]["location"]["found"] && isset($_REQUEST["date_from"]) && $_REQUEST["date_from"] != "-1" && $_REQUEST["date_from"] != "0")
{
	$criteria["time"]["fields"]["date_from"]["selected"]=$_REQUEST["date_from"];
}
if($criteria["route"]["fields"]["location"]["found"])
{
	$criteria["time"]["fields"]["date_from"]["active"]=true;
	$dbresult = $db->select("SELECT DISTINCT `date` as `name` FROM `images` ".$where." ORDER BY `date` ASC;");
	while ($dbrow = $dbresult->fetch_object())
	{
		$criteria["time"]["fields"]["date_from"]["values"][]=array("display" => date($lang["date"], $dbrow->name), "value" => $dbrow->name);
		$criteria["time"]["fields"]["date_from"]["found"]=($dbrow->name == $criteria["time"]["fields"]["date_from"]["selected"])?true:$criteria["time"]["fields"]["date_from"]["found"];
	}
	$dbresult->free();
}
else
{
	$criteria["time"]["fields"]["date_from"]["values"][]=$field_choose;
}
//////////////////////////////////////////////////////////////////////////////
//Auswahlfeld zum Datum (Ende)
$criteria["time"]["fields"]["date_to"]=$type_dd;
$criteria["time"]["fields"]["date_to"]["display"]=$lang["date_to"];
if ($criteria["route"]["fields"]["location"]["found"] && isset($_REQUEST["date_to"]) && $_REQUEST["date_to"] != "-1" && $_REQUEST["date_to"] != "0")
{
	$criteria["time"]["fields"]["date_to"]["selected"]=$_REQUEST["date_to"];
}
if($criteria["route"]["fields"]["location"]["found"])
{
	$criteria["time"]["fields"]["date_to"]["active"]=true;
	$dbresult = $db->select("SELECT DISTINCT `date` as `name` FROM `images` ".$where." ORDER BY `date` DESC;");
	while ($dbrow = $dbresult->fetch_object())
	{
		$criteria["time"]["fields"]["date_to"]["values"][]=array("display" => date($lang["date"], $dbrow->name), "value" => $dbrow->name);
		$criteria["time"]["fields"]["date_to"]["found"]=($dbrow->name == $criteria["time"]["fields"]["date_to"]["selected"])?true:$criteria["time"]["fields"]["date_to"]["found"];
	}
	$dbresult->free();
}
else
{
	$criteria["time"]["fields"]["date_to"]["values"][]=$field_choose;
}
//////////////////////////////////////////////////////////////////////////////
//Auswahlfeld zum Datum (Wochentag)
$criteria["time"]["fields"]["date_wd"]=$type_dd;
$criteria["time"]["fields"]["date_wd"]["display"]=$lang["date_wd"];
if ($criteria["route"]["fields"]["location"]["found"] && isset($_REQUEST["date_wd"]))
{
	$criteria["time"]["fields"]["date_wd"]["selected"]=$_REQUEST["date_wd"];
}
if($criteria["route"]["fields"]["location"]["found"])
{
	$criteria["time"]["fields"]["date_wd"]["active"]=true;
	$criteria["time"]["fields"]["date_wd"]["values"][]=array("display" => $lang["any"], "value" => '-1');
	foreach($lang["days"] as $id => $day)
	{
		$criteria["time"]["fields"]["date_wd"]["values"][]=array("display" => $day, "value" => $id);
		$criteria["time"]["fields"]["date_wd"]["found"]=($id == $criteria["time"]["fields"]["date_wd"]["selected"])?true:$criteria["time"]["fields"]["date_wd"]["found"];
	}
}
else
{
	$criteria["time"]["fields"]["date_wd"]["values"][]=$field_choose;
}
//////////////////////////////////////////////////////////////////////////////
//Auswahlfeld zum Tagesabschnitt
$criteria["time"]["fields"]["date_time"]=$type_dd;
$criteria["time"]["fields"]["date_time"]["display"]=$lang["date_time"];
if ($criteria["route"]["fields"]["location"]["found"] && isset($_REQUEST["date_time"]) && $_REQUEST["date_time"] != "-1" && $_REQUEST["date_time"] != "0")
{
	$criteria["time"]["fields"]["date_time"]["selected"]=$_REQUEST["date_time"];
}
if($criteria["route"]["fields"]["location"]["found"])
{
	$criteria["time"]["fields"]["date_time"]["active"]=true;
	$criteria["time"]["fields"]["date_time"]["values"][]=$field_any;
	$dbresult = $db->select("SELECT DISTINCT `hour_start`, `hour_end` FROM `images` ".$where." ORDER BY `hour_start` ASC, `hour_end` DESC;");
	while ($dbrow = $dbresult->fetch_object())
	{
		$criteria["time"]["fields"]["date_time"]["values"][]=array("display" => sprintf("%02.0f", $dbrow->hour_start).":00-".sprintf("%02.0f", $dbrow->hour_end).":00", "value" => $dbrow->hour_start."-".$dbrow->hour_end);
		$criteria["time"]["fields"]["date_time"]["found"]=($dbrow->hour_start."-".$dbrow->hour_end == $criteria["time"]["fields"]["date_time"]["selected"])?true:$criteria["time"]["fields"]["date_time"]["found"];
	}
	$dbresult->free();
}
else
{
	$criteria["time"]["fields"]["date_time"]["values"][]=$field_choose;
}
//////////////////////////////////////////////////////////////////////////////
//Gruppe "traffic_pattern"
$criteria["traffic_pattern"]=array();
$criteria["traffic_pattern"]["display"]=$lang["traffic_pattern"];
$criteria["traffic_pattern"]["fields"]=array();
//////////////////////////////////////////////////////////////////////////////
//Checkboxen ""
$criteria["traffic_pattern"]["fields"]["traffic_pattern"]=array();
$criteria["traffic_pattern"]["fields"]["traffic_pattern"]["display"]="";
$criteria["traffic_pattern"]["fields"]["traffic_pattern"]["type"]="cb";
$criteria["traffic_pattern"]["fields"]["traffic_pattern"]["active"]=false;
$criteria["traffic_pattern"]["fields"]["traffic_pattern"]["values"]=array();
//if($criteria["route"]["fields"]["location"]["selected"])
{
	$criteria["traffic_pattern"]["fields"]["traffic_pattern"]["active"]=true;
}
foreach($lang["tp"] as $id => $tp)
{
	$checked=false;
	if(isset($_REQUEST["traffic_pattern"][$id]))
	{
		$checked=true;
	}
	elseif (!isset($_REQUEST["traffic_pattern"]) && $id=="all")
	{
		$checked=true;
	}
	$criteria["traffic_pattern"]["fields"]["traffic_pattern"]["values"][]=array("display" => $tp, "value" => $id, 
	"checked" => $checked);
}
//////////////////////////////////////////////////////////////////////////////
//Gruppe "Kommentar"
$criteria["comment"]=array();
$criteria["comment"]["display"]=$lang["comment"];
$criteria["comment"]["fields"]=array();
//////////////////////////////////////////////////////////////////////////////
//Textfeld ""
$criteria["comment"]["fields"]["comment"]=array();
$criteria["comment"]["fields"]["comment"]["display"]="";
$criteria["comment"]["fields"]["comment"]["type"]="txt";
$criteria["comment"]["fields"]["comment"]["active"]=false;
$criteria["comment"]["fields"]["comment"]["values"]=array();
$criteria["comment"]["fields"]["comment"]["selected"]="";
if (isset($_REQUEST["comment"]) && $_REQUEST["comment"] != "-1" && $_REQUEST["comment"] != "0")
{
	$criteria["comment"]["fields"]["comment"]["selected"]=$_REQUEST["comment"];
}

//if($criteria["route"]["fields"]["location"]["selected"])
{
	$criteria["comment"]["fields"]["comment"]["active"]=true;
}



//if (!$criteria["route"]["fields"]["location"]["selected"] && $criteria["route"]["fields"]["direction"]["selected"])
if ($_REQUEST["showhelp"]==1 && $_REQUEST["site"] != "search")
{
	$_REQUEST["site"]="searchhelp";
}

?>