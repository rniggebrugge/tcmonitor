<?php
session_start();
$type = isset($_GET["type"])?$_GET["type"]:"";
$id = isset($_GET["id"])?(int)$_GET["id"]:0;

// details of request
	$method = $_SERVER["REQUEST_METHOD"];
    if ($method!="POST" && $method!="GET") die();
    define ("POST",$method=="POST");
	if (POST) $p=$_POST;

// connect to database
        require("./source/db.php");
        $db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

// access and country
        $authLevel = (int)$_SESSION["authorization"];
		$country = (int)$_SESSION["country"];


// output to browser
	if ($authLevel>0){ 

		if(POST && isset($p["edit_form"])){
            $id = $db->save_from_form();
            if($id) echo "<div style='background:#009; color:#fff'>Successfully saved $id.</div>";
            else echo "<div style='background:#900; color:#fff'>Problem saving!</div>";
            echo "<input type=button value=cancel end_action='close'><hr>";
        }
	
		echo '<!DOCTYPE html><html><head><meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<script type="text/javascript" src="/tcmonitor/scripts/jquery-1.12.3.min.js"></script>
			<style>html, body { background:#666; color:#fff }
			* {font-family:Arial; font-size:14px;}
			input, textarea {font-size:14px; color:#039}
			td { vertical-align:top}
			</style><title>Edit Form</title></head>
			<body>';
		$db->edit_form($type, $id); 
		?>
		 <script>$(function(){
		 	$("input[end_action]").click(function(){
		 		$("#mask_all", window.parent.document).hide();
		 		$("#edit_iframe", window.parent.document).hide();
		 	});
		 });
		 </script>
		</body></html>
		<?php
	}


?>