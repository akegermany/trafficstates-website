<?php
require "checkpwd.php";
?><h1>Administration</h1>
<ul>
<li><a href="import.php">Import of new images</a></li>
<li><a href="edit_list.php">Edit images</a></li>
<li><a href="reindex.php">Reindex images</a> (always use after importing or editing images)</li>
<li><a href="del_set.php">Delete set of images</a></li>
<li>Install/reset db (deletes the whole database, if existing: <a href="install.php">+</a>)</li>
<!--<li><a href="/phpmyadmin/" target="_blank">DB-Access</a></li>-->
<li><a href="logout.php">Logout</a></li>
</ul>