<?php
require("../config.php");

header('content-type: text/html; charset=utf-8');

if (!isset($_SERVER["PHP_AUTH_USER"]) || $_SERVER["PHP_AUTH_USER"]!=$config["admin_user"] || $_SERVER["PHP_AUTH_PW"]!=$config["admin_pwd"])
{ 
    header("WWW-Authenticate: Basic realm=\"Admin-Area\""); 
    header("HTTP/1.0 401 Unauthorized"); 
    die("HTTP/1.0 401 Unauthorized"); 
}
?>
