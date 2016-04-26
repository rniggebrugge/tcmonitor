<table id="list">
<tr>
<td sort=id width="5%">id<br><input type=text data-filter="id" size="3"></td>
<td sort=title width="40%">name<br><input type=text data-filter="title"></td>
<td sort=email width="40%">email<br><input type=text data-filter="email"></td>
<td sort=country width="10%">country<br><input type=text data-filter="country"></td>
<td clearfilters width="5%">X</td></tr></table>
<style>
tr.hide_row { display:none;}
</style>
<script>
$(function(){
	var request_url="/tcmonitor/communication/contact_point/items/country=<?php echo $country;?>";
	$.post(request_url, function(data){
		data = $.parseJSON(data);
		if(data.error && data.error!=="") return;
		data = data.data;
			data.forEach(function(item){
				var table=$("#list"), 
					tr = $("<tr data-id="+item.id+" data-email='"+item.email+"' data-title='"+item.title+"' data-country='"+item.country+"'></tr>"), 
					td;
				td = $("<td>"+item.id+"</td>");
				tr.append(td);
				td = $("<td>"+item.title+"</td>");
				tr.append(td);
				td = $("<td>"+item.email+"</td>");
				tr.append(td);
				td = $("<td>"+item.country+"</td>");
				tr.append(td);
				td = $("<td>&nbsp;</td>");
				tr.append(td);
				table.append(tr);

			});
	});
});
</script>
<?php


print_r($db->schemas["contact_point"]);
?>