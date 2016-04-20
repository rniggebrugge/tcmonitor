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
?>
<a href="./logout" style="font-weight:bold; color:#f00; display:block; float:right">Log out</a>
<hr>
