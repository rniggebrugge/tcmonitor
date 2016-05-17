<table id="list">
<tr>
<!-- <td sort=id >id<br><input type=text filter="id" size="3"></td> -->
<td sort=country__ >country<br><input type=text filter="country__" size="8"></td>
<td sort=number style="width:120px">number<br><input type=text filter="number" size="5"></td>
<td sort=title>title<br><input type=text filter="title" size="40"></td>
<td sort=status>status<br><input type=text filter="status" size="8"></td>
<td sort=last_update>update<br><input type=text filter="last_update" size="8"></td>
<td clearfilters>X</td></tr></table>
<style>
tr.hide_row { display:none;}
</style>
<script>
var active_type = "status_fiche";

$(function(){
	show_waiting();
	var request_url="/tcmonitor/communication/fiche_belge/items";
	$.post(request_url, function(data_instrument){
		data_instrument = $.parseJSON(data_instrument);
		if(data_instrument.error && data_instrument.error!=="") return;
		data_instrument = data_instrument.data;
		var request_url="/tcmonitor/communication/status_fiche/items/<?php echo $country!=99999?'country='.$country:'';?>";
		$.post(request_url, function(data_status){
			data_status = $.parseJSON(data_status);
			if(data_status.error && data_status.error!=="") return;
			data_status = data_status.data;
			var instruments = data_instrument.reduce(function(total, current){
				total[current.id]=[current.title, current.number];
				return total;
			}, []);
			data_status = data_status.map(function(status){
				status.title=instruments[status.fiche][0];
				status.number=instruments[status.fiche][1];
				return status
			});
			createTable($("#list"), data_status, ["country", "number","title", "status", "last_update"], {"update":updateMe, "delete":deleteMe});
			remove_waiting();
		});
	});
});


</script>