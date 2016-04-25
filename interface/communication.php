<?php
session_start();
error_reporting(0);
// initialize db
	require("../source/db.php");
	$db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

// make sure only authorized visitors can possibly see these lists
	define("AUTHLEVEL", (int)$_SESSION["authorization"]);
	if (AUTHLEVEL<1) error_stop("permission denied");

// process request uri to build query, and check for validity
	$request = array_filter(explode("/",explode("/communication/",$_SERVER["REQUEST_URI"])[1]));
	if(count($request)<2) error_stop("too few arguments");
	$type=$request[0]; 
	$listtype=$request[1]; if($listtype!="list" && $listtype!="items") error_stop("requested list type not supported");
	if (!$db->is_type($type)) error_stop("requested type unknown");

// building query. In case the argument "id=xxx" is in request, return only that item
	$id=0;
	for ($i=2;$i<count($request); $i++){
		if (preg_match('/^id=(\d+)$/', $request[$i], $matches)){
			$id = (int)$matches[1];
			unset($request[$i]);
			break;
		}
	}
	$req_array=[];
	foreach(array_slice($request, 2) as $line){
		$parts = splitter($line);
		$req_array[$parts[0]]=$parts[1];
	}
	$db->select($type, $id, $req_array);
	$db->sort_results();
	echo '{"error":"","data":[';
	$comma=""; 
	foreach($db->last_results as $item_id=>$item_fields){
		echo $comma.'{"id":'.$item_id;
		if($listtype=="list") echo ',"title":'.json_encode($item_fields["title"]);
		else {
			foreach($item_fields as $key=>$value) 
				echo ', "'.$key.'":'.json_encode($value).'';
		}
		echo '}';
		$comma=', ';
	}
	echo ']';
	if ($listtype=="items"){
		echo ', "first_field_b":"'.$db->first_field_b[$type].'"';
	}
	echo '}';

// function to terminate output with error message
function error_stop($err){
	echo '{"error":"'.$err.'", "data":{}}';
	die();
}
?>
