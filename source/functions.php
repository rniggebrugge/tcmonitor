<?php

function get_text_block($label, $db){
	$db->select("website_text", 0, ["title"=>"homepage"]);
	return array_values($db->last_results)[0]["text"]; 
}
?>