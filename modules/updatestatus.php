<table id="list">
<tr>
<td sort=id width="5%">id<br><input type=text filter="id" size="3"></td>
<td sort=title width="80%">title<br><input type=text filter="title"></td>
<td sort=reference width="10%">reference<br><input type=text filter="reference" size="8"></td>
<td clearfilters width="5%">X</td></tr></table>
<style>
tr.hide_row { display:none;}
</style>
<script>
$(function(){
	var request_url="/tcmonitor/communication/legal_instrument/items";
	$.post(request_url, function(data){
		data = $.parseJSON(data);
		if(data.error && data.error!=="") return;
		data = data.data;
		createTable($("#list"), data, ["id","title","reference","EMPTY"], false)
	});
});
</script>