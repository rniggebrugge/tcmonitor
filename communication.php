<?php
session_start();
// initialize db
	require("./source/db.php");
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
	$db->select($type, $id, array_slice($request, 2));
	echo '{"error":"","data":[';
	$comma="";
	foreach($db->fields_a[$type] as $item_id=>$item_fields){
		echo $comma.'{"id":'.$item_id;
		if($listtype=="list") echo ',"title":'.json_encode($item_fields[0][1]);
		else {
			foreach($item_fields as $field_a) 
				echo ', "'.$field_a[0].'":'.json_encode($field_a[1]).'';
			foreach($db->fields_b[$type][$item_id] as $field_b) 
				echo ', "'.$field_b[0].'":'.json_encode(q_dec($field_b[1]));
		}
		echo '}';
		$comma=', ';
	}
	echo ']';
	if ($listtype=="items"){
		$fb = isset($db->fields_b[$type]) && isset($db->fields_b[$type][$item_id]) 
				&& isset($db->fields_b[$type][$item_id][0])?$db->fields_b[$type][$item_id][0][0]:"";
		echo ', "first_field_b":"'.$fb.'"';
	}
	echo '}';

// function to terminate output with error message
function error_stop($err){
	echo '{"error":"'.$err.'", "data":{}}';
	die();
}
?>
