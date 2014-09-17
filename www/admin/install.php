<?php
require "checkpwd.php";

require "../Database.php";
$db=new Database();

$dir_screen="../images/screen/";
$dir_thumbs="../images/thumbs/";

$db->execute("DROP TABLE IF EXISTS `images`;");
$db->execute("CREATE TABLE IF NOT EXISTS `images` (
  `id` int(11) NOT NULL auto_increment,
  `filename` varchar(64) collate utf8_unicode_ci NOT NULL,
  `route` varchar(16) collate utf8_unicode_ci NOT NULL,
  `direction` varchar(16) collate utf8_unicode_ci NOT NULL,
  `location_from` int(11) NOT NULL,
  `location_to` int(11) NOT NULL,
  `date` bigint(11) NOT NULL,
  `date_wd` tinyint(4) NOT NULL,
  `hour_start` tinyint(4) NOT NULL,
  `hour_end` tinyint(4) NOT NULL,
  `routine` varchar(16) collate utf8_unicode_ci NOT NULL,
  `perspective` varchar(16) collate utf8_unicode_ci NOT NULL,
  `tp_mlc` smallint(6) NOT NULL default '1',
  `tp_tsg` smallint(6) NOT NULL default '1',
  `tp_hct` smallint(6) NOT NULL default '1',
  `tp_plc` smallint(6) NOT NULL default '1',
  `tp_oct` smallint(6) NOT NULL default '1',
  `tp_all` smallint(6) NOT NULL default '1',
  `comment_de` varchar(5000) collate utf8_unicode_ci NOT NULL,
  `comment_en` varchar(5000) collate utf8_unicode_ci NOT NULL,
  `comment_int` varchar(5000) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;");

echo "DB resetted<br/>";

//Importierte Bilder löschen
if ($dh = opendir($dir_screen))
{
	while (($sf = readdir($dh)) !== false)
	{
		if ($sf == '.' || $sf == '..')
		{
			continue;
		}
		unlink($dir_screen.$sf);
		echo "deleting ".$dir_screen.$sf."<br/>";
	}
}

//Importierte Bilder löschen
if ($dh = opendir($dir_thumbs))
{
	while (($sf = readdir($dh)) !== false)
	{
		if ($sf == '.' || $sf == '..')
		{
			continue;
		}
		unlink($dir_thumbs.$sf);
		echo "deleting ".$dir_thumbs.$sf."<br/>";
	}
}


echo "<a href=\".\">back</a>";



?>