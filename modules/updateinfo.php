<table id="list">
<tr>
<!-- <td sort=id >id<br><input type=text filter="id" size="3"></td> -->
<td sort=country__ >country<br><input type=text filter="country__" size="8"></td>
<td sort=remark>remark<br><input type=text filter="remark" size="40"></td>
<td sort=confirmed_date>Confirmation<br><input type=text filter="confirmed_date" size="8"></td>
<td clearfilters>X</td></tr></table>
<style>
tr.hide_row { display:none;}
</style>
<script>
var active_type = "info_about";

$(function(){
	show_waiting();
	var request_url="/tcmonitor/communication/info_about/items/<?php 
		echo $country!=99999?'country='.$country:'';
		?>";
	$.post(request_url, function(data){
		data = $.parseJSON(data);
		if(data.error && data.error!=="") return;
		data = data.data;
		data = data.map(function(item){
			item.remark = item.remark?item.remark:"-";
			item.confirmed_date = item.confirmed_date?item.confirmed_date:"-"; 
			return item;
		});
		createTable($("#list"), data, ["country", "remark","confirmed_date"], {"update":updateMe, "delete":deleteMe});
		remove_waiting();
	});
});


</script>