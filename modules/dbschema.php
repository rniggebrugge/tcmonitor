<?php
$lines = file($db->filepath.'__schema', FILE_IGNORE_NEW_LINES);
?>
<form method="POST">
<table><tr><td>
<textarea rows=30 cols=60 name=schema><?php
foreach($lines as $line) echo "$line\n";
?></textarea></td><td style="background:#39f">Options:<br><br>
***[typename] (example: ***status)<br><br>
[fieldname]|[fieldtype] (example: description|textarea, default is simple textfield)<br><br>
--- : start of "b-fields"<br><br>
#[comment] : lines starting with # are considered remarks<br><br>
Empty lines can be included and will be ignored
</td></tr></table>
<br><input type="submit" value="save">
</form>