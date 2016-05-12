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
	echo "<form method='POST' style='position:absolute; right:10px; top:6px; padding:2px; background:#009; border-radius:4px'>
		<select name='change_country' onchange=this.form.submit() >";
	$db->select("country",0,["eu"=>1]);
	$db->sort_results();
	$countries = $db->get_results();
	echo "<optgroup label='EJN'>";
	foreach($countries as $id=>$dets) echo "<option value='$id' ".($id==$country?"selected":"").">".$dets["title"]."</option>";
	echo "</optgroup>";
	if($authLevel>1){
		$db->select("country",0,["candidate"=>1]);
		$db->sort_results();
		$countries = $db->get_results();
		echo "<optgroup label='Candidate Countries'>";
		foreach($countries as $id=>$dets) echo "<option value='$id' ".($id==$country?"selected":"").">".$dets["title"]."</option>";
		echo "</optgroup>";
		$db->select("country",0,["associated"=>1]);
		$db->sort_results();
		$countries = $db->get_results();
		echo "<optgroup label='Associated Countries'>";
		foreach($countries as $id=>$dets) echo "<option value='$id' ".($id==$country?"selected":"").">".$dets["title"]."</option>";
		echo "</optgroup>";
		echo "<optgroup label='Show all'>";
		echo "<option value=99999 ".($country==99999?"selected":"").">ALL</option>";
		echo "</optgroup>";
	}
	echo "</select></form>";
}


?>
