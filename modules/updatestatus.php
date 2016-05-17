<table id="list">
<tr>
<!-- <td sort=id >id<br><input type=text filter="id" size="3"></td> -->
<td sort=country__ >country<br><input type=text filter="country__"></td>
<td sort=reference>reference<br><input type=text filter="reference" size="8"></td>
<td sort=title >title<br><input type=text filter="title"></td>
<td clearfilters >X</td></tr></table>


<style>
tr.hide_row { display:none;}
</style>
<script>
$(function(){
	show_waiting();
	var request_url="/tcmonitor/communication/legal_instrument/items";
	$.post(request_url, function(data_instrument){
		data_instrument = $.parseJSON(data_instrument);
		if(data_instrument.error && data_instrument.error!=="") return;
		data_instrument = data_instrument.data;
		var request_url="/tcmonitor/communication/status_implementation/items/<?php echo $country!=99999?'country='.$country:'';?>";
		$.post(request_url, function(data_status){
			data_status = $.parseJSON(data_status);
			if(data_status.error && data_status.error!=="") return;
			data_status = data_status.data;
			var instruments = data_instrument.reduce(function(total, current){
				total[current.id]=[current.title, current.reference];
				return total;
			}, []);
			data_status = data_status.map(function(status){
				// status
				status.title=instruments[status.legal_instrument][0];
				status.reference=instruments[status.legal_instrument][1];
				return status
			});
			createTable($("#list"), data_status, ["country", "reference","title"], {"update":updateMe, "delete":deleteMe});
			remove_waiting();
		});
	});
});
</script>


