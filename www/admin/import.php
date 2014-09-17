<?php
require "checkpwd.php";

require "../Database.php";
$db=new Database();

$dir="../images/import/";
$dir_screen="../images/screen/";
$dir_thumb="../images/thumbs/";

//Import-Verzeichnis einlesen
$filenames=scandir($dir);
echo "<code>Scanning ".$dir." for new files...";
flush();

$images= $db->select("SELECT * FROM `images`;");
$imported=array();
while ($image = $images->fetch_object())
{
	$imported[$image->filename]=true;
}

$counter=0;
foreach($filenames as $filename)
{
	if ($filename=="." || $filename == ".." || substr($filename, -23) == ".description_states.xml")
	{
		continue;
	}
	
	//Wurde schon mal importiert
	if (is_file($dir_screen.$filename))
	{
		if (isset($imported[$filename]))
		{
			continue;
		}
	}
	
	if ($counter >= 400)
	{
		echo "<br/>Maximum of imports per request reached. <a href=\"javascript:window.location.reload()\">Click here to continue.</a>";
		break;
	}

	$counter++;

	echo "<br/>".sprintf("%04u",$counter)." Importing ".$filename.": ";
	flush();

	if (!is_file($dir.$filename) || !is_readable($dir.$filename))
	{
		echo "cannot read this file";
		continue;
	}
	
	//DB-Werte ermitteln
	$route=substr($filename, 0, 2);
	$direction=substr($filename, 2,1);
	$date_y=intval(substr($filename, 8,4));
	$date_m=intval(substr($filename, 13,2));
	$date_d=intval(substr($filename, 16,2));

	$location_from=intval(substr($filename, 19,4));
	$location_to=intval(substr($filename, 24,4));

	$hour_start=intval(substr($filename, 29,2));
	$hour_end=intval(substr($filename, 32,2));
	$routine=substr($filename, 35,4);
	$perspective=substr($filename, 40,3);
	
	//Standardwerte
	$tp_mlc=1;
	$tp_plc=1;
	$tp_tsg=1;
	$tp_oct=1;
	$tp_hct=1;
	$comment_de="";
	$comment_en="";
	
	$time=mktime(12, 0, 0, $date_m, $date_d, $date_y);
	
	if (!$time)
	{
		echo "unable to extract date";
		$counter--;
		continue;
	}
	
	$date_wd = date("N", $time)-1;
	

	$filetype=strtolower(substr($filename, 44,3));
	
	if ($filetype != "png")
	{
		echo "unknown filetype";
		$counter--;
		continue;
	}
	
	//Bild einlesen
	$gd2img=@imagecreatefrompng($dir.$filename);

	if (!$gd2img)
	{
		echo "Error reading file as PNG";
		$counter--;
		continue;
	}
	
	$ox=imagesx($gd2img);
	$oy=imagesy($gd2img);
	$or=$ox/$oy;

	//Abmaße Thumbs
	$ny=180;
	$nx=180;

	$nr=$nx/$ny;

	//Org. Seitenverhälntnis beibehalten
	if ($or > $nr)
	{
		$ny=floor($nx/$or);
	}
	else
	{
		$nx=floor($or*$ny);
	}

	$gd2new=imageCreateTrueColor($nx, $ny);

	imagecopyresampled ($gd2new, $gd2img, 0, 0, 0, 0, $nx, $ny, $ox, $oy);
	
	imagegammacorrect ($gd2new, 1 ,2);
	
	imagejpeg($gd2new, $dir_thumb."tn_".$filename.".jpg", 90);
	
	imagedestroy($gd2new);
	
	echo "Thumnail created, ";
	
	if (!@copy($dir.$filename, $dir_screen.$filename))
	{
		echo "failed copying file";
		$counter--;
		continue;
	}

	echo "File copied, ";	
	
	//XML einlesen
	if (!is_file(substr($dir.$filename, 0, -13).".description_states.xml") || !is_readable(substr($dir.$filename, 0, -13).".description_states.xml"))
	{
		echo "no XML, ";
	}
	else
	{
		$xml = simplexml_load_file(substr($dir.$filename, 0, -13).".description_states.xml");
		
		if (!$xml)
		{
			echo "failed to parse XML!";
			$counter--;
			continue;
		}

		//Standardwerte
		$tp_mlc=0;
		$tp_plc=0;
		$tp_tsg=0;
		$tp_oct=0;
		$tp_hct=0;
		
		foreach ($xml->state as $ts)
		{
			if (isset($ts['name']) && "" != $ts['name'])
			{
				$name="tp_".strtolower($ts['name']);
				$$name++;
			}
		}
		
		foreach ($xml->comment as $comm)
		{
			if (isset($comm['de']) && "" != $comm['de'])
			{
				$comment_de.=$comm['de']."; ";
			}

			if (isset($comm['en']) && "" != $comm['en'])
			{
				$comment_en.=$comm['en']."; ";
			}
		}
		
		//cut trailing "; "
		if ("; " == substr($comment_de, -2))
		{
			$comment_de = substr($comment_de, 0, -2);
		}
		
		if ("; " == substr($comment_en, -2))
		{
			$comment_en = substr($comment_en, 0, -2);
		}
		
		echo "XML OK, ";
	}
	
	//DB-Werte schreiben
	$db->execute(
	"INSERT INTO `images` ".
	"(`filename`, `route`, `direction`, `location_from`, `location_to`, `date`, `date_wd`, `hour_start`, `hour_end`, `routine`, `perspective`, `tp_mlc`, `tp_plc`, `tp_tsg`, `tp_oct`, `tp_hct`, `comment_de`, `comment_en`, `comment_int`) ".
	"VALUES ".
	"('$filename', '$route', '$direction', '$location_from', '$location_to', $time, $date_wd, $hour_start, $hour_end, '$routine', '$perspective', $tp_mlc, $tp_plc, $tp_tsg, $tp_oct, $tp_hct, '$comment_de', '$comment_en', '');"
	);
	echo "DB-Values written, ";


	echo "OK";
}
echo "</code><br/>";
echo $counter." files imported<br/>";
echo "<a href=\".\">back</a>";

?>