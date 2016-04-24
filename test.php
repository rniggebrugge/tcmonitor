<?php
// connect to database
require("./source/db.php");
$db = new Database($_SERVER["DOCUMENT_ROOT"]."/tcmonitor/database/");

$n = $db->select("country");
$db->sort_results("id",true);
if ($n>0) $results = $db->get_results();
else die();

// foreach($results as $i=>$r) $results[$i]=array_splice($r, 0,4);
print_r($results);
// echo "<hr>";

// $db->sort_results("general_situation");
// $results = $db->get_results();
// foreach($results as $i=>$r) $results[$i]=array_splice($r, 0,4);
// print_r($results);






?>