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
	$countries = $db->get_list("country");
	echo "<form method='POST' action='/monitor/'>
		Country: <select name='select_country'>";
	foreach($countries as $id=>$label) echo "<option value='$id'>$label</option>";
	echo "</select> > <input type='submit' value='choose'></form>";
} 

	
	

