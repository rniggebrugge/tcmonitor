<?php
if (POST){
	$db->save_raw($p["type"], $p["fields_a"], $p["fields_b"], $p["id"]);
}

?>
<h1>Very, very raw input!</h1>
<form method="POST">
<table style="border:1px solid #999; border-collapse:collapse">
<tr><td>Asset type:</td><td><select name="type"><option value="">choose</option><?php
	foreach($db->types as $type) echo "<option value='$type'>$type</option>";
?></select> > <select name="item" style="width:500px; overflow:hidden"><option value="0"> create new</option></select>
<input type=text value=0 size=4 name=id style="text-align:right;float:right" onclick="alert('Be very careful when changing the ID field manually!!!\n\nIt will make a copy of the item, possibly overwriting an existing one with chosen ID!')"> 
</td></tr>
<tr><td>Fields A:</td><td><textarea name="fields_a" rows="6" cols="100"></textarea></td></tr>
<tr><td>Fields B:</td><td><textarea name="fields_b" rows="15" cols="100"></textarea></td></tr>
<tr><td>&nbsp;</td><td><input type=submit value="save"></td></tr>
</table>
</form>

<script>
$(function(){
	$("select[name=type]").change(function(){
		var type=$(this).val(), request_url="/tcmonitor/communication/"+type+"/list";
		$.post(request_url, function(data){
			var select = $("select[name=item]");
			$("option[value!=0]", select).remove();
			data = $.parseJSON(data);
			if(data.error && data.error!=="") return;
			data = data.data;
			data.forEach(function(item){
				select.append($("<option value="+item.id+">"+item.title+"</option>"));
			});

		})
	});
	$("select[name=item]").change(function(){
		var type=$("select[name=type]").val();
		var id=parseInt($(this).val());
		$("input[name=id]").val(id)
		$("textarea[name=fields_a]").val("");
		$("textarea[name=fields_b]").val("");
		if(id){
			var request_url="/tcmonitor/communication/"+type+"/items/id="+id;
			$.post(request_url, function(data){
				alert(data)
				data = $.parseJSON(data);
				if(data.error && data.error!=="") return;
				var first_field_b = data.first_field_b, add_to = 0, text = ["",""];
				var object = data.data[0];
				for(var label in object) if(label!=="id"){
					if (label===data.first_field_b)add_to=1;
					text[add_to]+=label+"="+object[label]+"\n";
				}
				$("textarea[name=fields_a]").val(text[0]);
				$("textarea[name=fields_b]").val(text[1]);
			});
		}
	});
});
</script>