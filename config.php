<?php
/**
 * Description: Config file
 */
define("USER", "backlinq_jtrimm");
define("DATA", "backlinq_web");
define("PASS", "B+A>rhdxEpGwI");
define("ROOT", $_SERVER['DOCUMENT_ROOT']);
define("HOST", $_SERVER['HTTP_HOST']);
define("SERVER", $_SERVER['SERVER_NAME']);

$conString = "mysql:host=localhost;dbname=".DATA.";";

define("CONNETIONSTRING", $conString);