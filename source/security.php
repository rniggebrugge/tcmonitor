<?php
// logging in
if (POST) {
	if (isset($p["username"]) && isset($p["password"]) && (TIME-(int)$p["s"])<30) {
		if($p["username"]=="tc" && $p["password"]=="n" ){
			$_SESSION["authorization"]=1;
			$authLevel=1;
			$user_country=0;
		} else if($p["username"]=="remco" && $p["password"]=="n" ){
			$_SESSION["authorization"]=10;
			$authLevel=10;
			$user_country=0;
		} 
	}	else if(isset($p["select_country"])) {
			$country=(int)$p["select_country"];
			$_SESSION["country"]=$country;
	}
}


if ( (int)$_SESSION["authorization"]<1 ){
	$authLevel = 0;
	$_SESSION["authorization"]=0;
	$_SESSION["country"]=0;
	echo "<form method='POST' action='/monitor/'>
		<input type='hidden' value='".TIME."' name='s'>
		<table><tr><td>Username:</td><td><input type=text size=20 name=username></td></tr>
		<tr><td>Password:</td><td><input type=password size=20 name=password></td></tr>
		<tr><td>&nbsp;</td><td><input type='submit' value='log in'></td></tr>
		</table></form>";
} else if ($country<1){
	$country = 0;
	$db->select("country",0,["eu"=>1]);
	$db->sort_results();
	$countries = $db->get_results();
	echo "<form method='POST' action='/monitor/'>
		Country: <select name='select_country'>";
	echo "<optgroup label='EJN'>";
	foreach($countries as $id=>$dets) echo "<option value='$id'>".$dets["title"]."</option>";
	echo "</optgroup>";
	if($authLevel>1){
		$db->select("country",0,["candidate"=>1]);
		$db->sort_results();
		$countries = $db->get_results();
		echo "<optgroup label='Candidate Countries'>";
		foreach($countries as $id=>$dets) echo "<option value='$id'>".$dets["title"]."</option>";
		echo "</optgroup>";
		$db->select("country",0,["associated"=>1]);
		$db->sort_results();
		$countries = $db->get_results();
		echo "<optgroup label='Associated Countries'>";
		foreach($countries as $id=>$dets) echo "<option value='$id'>".$dets["title"]."</option>";
		echo "</optgroup>";
	}
	echo "</select> > <input type='submit' value='choose'></form>";
} 

	
	

