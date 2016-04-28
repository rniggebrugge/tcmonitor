<table id="list">
<tr>
<td sort=id width="5%">id<br><input type=text data-filter="id" size="3"></td>
<td sort=title >name<br><input type=text filter="title"></td>
<td sort=email >email<br><input type=text filter="email"></td>
<td sort=country__ >country<br><input type=text filter="country__"></td>
<td clearfilters >X</td></tr></table>
<style>
tr.hide_row { display:none;}
</style>
<script>
$(function(){
	var request_url="/tcmonitor/communication/contact_point/items/<?php echo $country!=99999?'country='.$country:'';?>";
	$.post(request_url, function(data){
		data = $.parseJSON(data);
		if(data.error && data.error!=="") return;
		data = data.data;
		createTable($("#list"), data, ["id","title","email","country"], {"update":updateMe, "delete":deleteMe})
	});
});

</script>









