<table id="list">
<tr>
<!-- <td sort=id >id<br><input type=text filter="id" size="3"></td> -->
<td DDsort=country__ >country<br><input type=text filter="country__"></td>
<td DDsort=number style="width:120px">number<br><input type=text filter="number" size="8"></td>
<td DDsort=title>title<br><input type=text filter="title"></td>
<td DDsort=status>status<br><input type=text filter="status"></td>
<td clearfilters>X</td></tr></table>
<style>
tr.hide_row { display:none;}
</style>
<script>

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
			createTable($("#list"), data_status, ["country", "number","title", "status"], {"update":updateMe, "delete":deleteMe});
			remove_waiting();
		});
	});
});


</script>