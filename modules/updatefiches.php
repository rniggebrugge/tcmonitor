<table id="list">
<tr>
<td sort=id width="5%">id<br><input type=text data-filter="id" size="3"></td>
<td sort=title width="80%">title<br><input type=text data-filter="title"></td>
<td sort=number width="10%">number<br><input type=text data-filter="number" size="8"></td>
<td clearfilters width="5%">X</td></tr></table>
<style>
tr.hide_row { display:none;}
</style>
<script>
$(function(){
	var request_url="/tcmonitor/communication/fiche_belge/items";
	$.post(request_url, function(data){
		data = $.parseJSON(data);
		if(data.error && data.error!=="") return;
		data = data.data;
			data.forEach(function(item){
				var table=$("#list"), 
					tr = $("<tr data-id="+item.id+" data-number='"+item.number+"' data-title='"+item.title+"'></tr>"), 
					td;
				td = $("<td>"+item.id+"</td>");
				tr.append(td);
				td = $("<td>"+item.title+"</td>");
				tr.append(td);
				td = $("<td>"+item.number+"</td>");
				tr.append(td);
				td = $("<td>&nbsp;</td>");
				tr.append(td);
				addHover(tr)
				table.append(tr);

			});
	});
});
</script>