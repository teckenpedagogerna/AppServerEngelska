<?php

if(!isset($_GET["keypass"]))
	exit();

echo hash("sha512", $_GET["keypass"]);
exit();