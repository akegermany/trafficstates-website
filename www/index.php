<?php
//Programmroot
define("INNER_WEBROOT", dirname(__FILE__)."/");

//Anfrage analysieren
if (!isset($_REQUEST))
{
	$_REQUEST = array();
}

//Welche Seite soll angezeigt werden?
if (!isset($_REQUEST['site']))
{
	$_REQUEST['site'] = "start";
}

//Welche SPrache soll angezeigt werden?
if (!isset($_REQUEST['lang']))
{
	//Standardsprache aus Browser ermitteln
	require "language.php"; //Funktion zum Auslesen der im Browser gesetzten Sprache
	$allowed_langs = array ('de', 'en');
	$_REQUEST['lang'] = lang_getfrombrowser ($allowed_langs, 'en', null, false);
}

//Parameter auf Sonderzeichen überprüfen
if (preg_match("|^(a-z)|", $_REQUEST['lang'].$_REQUEST['site']))
{
	die("Error");
}

header('content-type: text/html; charset=utf-8');

//HTML-Seite generieren
//Kopfbereich (oben)
require "./html/_head_".$_REQUEST['lang'].".html";

//Suche (links?)
require "./html/db_".$_REQUEST['lang'].".php";
require "./db/db_search.php";

//Anzuzeigende Datei: Namen zusammensetzen
$file="./html/".$_REQUEST['site']."_".$_REQUEST['lang'].".html";
$errorfile="./html/error_".$_REQUEST['lang'].".html";
$errorfile_en="./html/error_en.html";

//exisitiert die Datei auch?
if (!is_readable($file))
{
	$file=$errorfile;
}

//exisitiert die Datei auch?
if (!is_readable($file))
{
	$file=$errorfile_en;
}

//Eigentlicher Inhalt
require $file;

//Fußzeile
require "./html/_foot_".$_REQUEST['lang'].".html";
?>
