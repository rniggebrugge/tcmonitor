<?php
$db->load_full_index();
foreach($db->types as $type) {
	$list = $db->get_list($type);
	$n=count($list);
	echo "<div class='header' block='$type'><input type='button' value='new' block='$type'><b>$type </b>($n items)</div>";
	echo "<div id='block_$type' style='display:none'><table>";
	foreach($list as $item_id=>$item_title) {
		echo 	"<tr><td>$item_id</td><td>$item_title</td><td>
				<input type='button' value='edit' item_id='$item_id' asset='$type'>
				<input type='button' value='delete' style='color:red' item_id='$item_id' asset='$type'>
				</td><tr>";
	}
	echo "</table></div>";
	echo "<div id='mask_all'></div><iframe frameborder='0' hspace='0' id='edit_iframe' style='width:1030px; height:600px; 
		position:fixed; left:50%; top:50%; margin-left:-515px; margin-top:-315px; display:none'></iframe>";
}

?>
<script>
$(function(){
	$("div[block]").click(function(){
		var type = $(this).attr("block");
		$("#block_"+type).toggle();
	});
	$("input[value=new]").click(function(){
		var type = $(this).attr("block");
		edit_item(type, 0);
		return false;
	});
	$("input[value=edit]").click(function(){
		var type = $(this).attr("asset"), id = $(this).attr("item_id");
		edit_item(type, id);
		return false;
	});

	$("input[value=delete]").click(function(){
		var type = $(this).attr("asset"), id = $(this).attr("item_id");
		if(!confirm("Delete "+type+" "+id+"?"))return;
		$.post("/tcmonitor/delete/"+type+"/"+id, function(data){
			if(data && data!=="") alert(data);
		});
		$(this).closest("tr").remove();

		return false;
	});

	function edit_item(type, id){
		$("#mask_all").show();
		$("#edit_iframe").show().attr("src", "/tcmonitor/edit_form.php?type="+type+"&id="+id);
	}
});
</script>
