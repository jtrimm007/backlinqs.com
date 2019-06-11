<?php
/**
 * Description: Config file
 */
define("USER", "YOUR DATABASE USER NAME");
define("DATA", "YOUR DATABASE NAME");
define("PASS", "YOUR DATABASE PASS");
define("ROOT", $_SERVER['DOCUMENT_ROOT']);
define("HOST", $_SERVER['HTTP_HOST']);
define("SERVER", $_SERVER['SERVER_NAME']);

$conString = "mysql:host=localhost;dbname=".DATA.";";

define("CONNETIONSTRING", $conString);