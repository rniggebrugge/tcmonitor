<?php
if ((int)$authLevel<10) die();
$path =$_SERVER["DOCUMENT_ROOT"]."/tcmonitor/import_files/";

$target="";
if(isset($p["target"])){
	$target=$p["target"];
	$lines = file($path.$target, FILE_IGNORE_NEW_LINES);
	$type = trim($lines[0]);
	$fields_a = explode("|",trim($lines[1]));
	$fields_b = explode("|",trim($lines[2]));
	$current_line = 4;
	$last_line=count($lines);
	while($current_line<$last_line){
		$read_a=[];
		foreach($fields_a as $key) if($key){
			$read_a[$key]=$lines[$current_line];
			$current_line++;
		} 
		$read_b=[];
		foreach($fields_b as $key) if($key){
			$read_b[$key]=$lines[$current_line];
			$current_line++;
		}
		$db->save_asset($type,0, $read_a, $read_b); 
	}
	echo '<hr>';
}

?>
<form method=post>
<?php
$files = scandir($path);
foreach($files  as $file){
	if(substr($file,0,1)!=="." && !is_dir($path.$file)){
		echo $file.' '.($target===$file?"<b style=color:red>!!! just imported from this file !!!</b>":"").' >> <input type=submit target="'.$file.'" value="import from this file"><br><br>';
	}
}
?>
<input type=text name=target value="">
</form>
<script>
$(function(){
	$("input[target]").click(function(){
		var form = $("#import_form");
		$("input[name=target]").val($(this).attr("target"));
		form.submit();

	})
});
</script>
