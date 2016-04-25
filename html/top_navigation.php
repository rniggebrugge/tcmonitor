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
if($authLevel==10) {
	echo '<a href="./dbschema" style="margin-right:10px; display:block; float:right">DB Schema</a>';
	echo '<a href="./import" style="margin-right:10px; display:block; float:right">Import from file</a>';
	echo '<a href="./admintoolraw" style="margin-right:10px; display:block; float:right">Raw Edit</a>';
	echo '<a href="./admintool" style="margin-right:10px; display:block; float:right">Admin Tool</a>';
	$countries = $db->get_list("country");
	echo "<form method='POST' style='position:absolute; right:10px; top:6px; padding:2px; background:#009; border-radius:4px'>
		<select name='change_country' onchange=this.form.submit() >";
	foreach($countries as $id=>$label) echo "<option value='$id' ".($country===$id?"selected":"").">$label</option>";
	echo "</select></form>";
}


?>
<hr>
