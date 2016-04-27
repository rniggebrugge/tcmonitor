<?php
session_start();
// access and country
$authLevel = (int)$_SESSION["authorization"];
if($authLevel<10) die("Insufficient permissions");

// initialize db
	require("../source/db.php");
	$db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

// process request uri to build query, and check for validity
	$request = array_filter(explode("/",explode("/delete/",$_SERVER["REQUEST_URI"])[1]));
	$type=htmlentities($request[0]);
	$id=(int)$request[1];

	echo $db->delete_item($type, $id);
?>