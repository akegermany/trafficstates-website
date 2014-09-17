<?php
    header("WWW-Authenticate: Basic realm=\"Admin-Area\""); 
    header("HTTP/1.0 401 Unauthorized"); 
    die("HTTP/1.0 401 Unauthorized"); 
?>