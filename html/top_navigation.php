<?php
$places = array(
	"./" => "Home",
	"./status" => "Status Table",
	"./fiches" => "Fiches Belges",
	"./contacts" => "Contact Points",
	"./infoabout" => "Information Nat. System",
	"./messages" => "Messages",
);

foreach ($places as $url=>$label){
	echo "<a href='$url' ".($url==SHORTURI?"class='topactive'":"").">$label</a> | ";
}
echo '<a href="./logout" style="font-weight:bold; color:#f00; display:block; float:right">Log out</a>';
if($authLevel==10) echo '<a href="./admintoolraw" style="margin-right:10px; display:block; float:right">Raw Edit</a>';
if($authLevel==10) echo '<a href="./admintool" style="margin-right:10px; display:block; float:right">Admin Tool</a>';
?>
<hr>
