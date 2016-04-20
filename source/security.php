<?php
// logging in
if (POST) {
	if (isset($p["username"]) && $p["username"]=="r" && isset($p["password"]) && $p["password"]=="n" && (TIME-(int)$p["s"])<30){
		$_SESSION["authorization"]=1;
		$authLevel=1;
		$user_country=0;
	} else if(isset($p["country"])) {
		$country=(int)$p["country"];
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
	echo "<form method='POST' action='/monitor/'>
		Country: <select name='country'><option value=1>Belgium</option>
			<option value=2>Germany</option>
		<input type='submit' value='choose'></form>";
} 

	
	

