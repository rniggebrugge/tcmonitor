<?php
if (POST){
	$db->save_raw($p["type"], $p["fields_a"], $p["fields_b"]);
}

?>
<h1>Very, very raw input!</h1>
<form method="POST">
<table style="border:1px solid #999; border-collapse:collapse">
<tr><td>Asset type:</td><td><select name="type"><?php
	foreach($db->types as $type) echo "<option value='$type'>$type</option>";
?></select>
</td></tr>
<tr><td>Fields A:</td><td><textarea name="fields_a" rows="6" cols="100"></textarea></td></tr>
<tr><td>Fields B:</td><td><textarea name="fields_b" rows="15" cols="100"></textarea></td></tr>
<tr><td>&nbsp;</td><td><input type=submit value="create"></td></tr>
</table>
</form>