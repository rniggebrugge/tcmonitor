<table id="list">
<tr>
<td sort=country__ >country<br><input type=text filter="country__"></td>
<td sort=title >name<br><input type=text filter="title"></td>
<td sort=email >email<br><input type=text filter="email"></td>
<td clearfilters >X</td></tr></table>
<style>
tr.hide_row { display:none;}
</style>
<script>
var active_type = "contact_point";

$(function(){
	show_waiting();
	var request_url="/tcmonitor/communication/contact_point/items/<?php echo $country!=99999?'country='.$country:'';?>";
	$.post(request_url, function(data){
		data = $.parseJSON(data);
		if(data.error && data.error!=="") return;
		data = data.data;
		createTable($("#list"), data, ["country","title","email"], {"update":updateMe, "delete":deleteMe});
		remove_waiting();
	});
});

</script>









