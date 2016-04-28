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
	var request_url="/tcmonitor/communication/contact_point/items/<?php echo $country!=99999?'country='.$country:'';?>";
	$.post(request_url, function(data){
		data = $.parseJSON(data);
		if(data.error && data.error!=="") return;
		data = data.data;
			data.forEach(function(item){
				var table=$("#list"), 
					tr = $("<tr data-id="+item.id+" data-email='"+item.email+
						"' data-title='"+item.title+"' data-country='"+item.country__+"'></tr>"), 
					td;
				td = $("<td>"+item.id+"</td>");
				tr.append(td);
				td = $("<td>"+item.title+"</td>");
				tr.append(td);
				td = $("<td>"+item.email+"</td>");
				tr.append(td);
				td = $("<td><img src='http://www.ejnforum.eu/cp/uploaded_content/spacer.png' class='flag_sprite' style='background-position:"+flags_pos[item.country]+"'>"+ item.country__+"</td>");
				tr.append(td);
				td = $("<td>&nbsp;</td>");
				tr.append(td);
				addHover(tr)
				table.append(tr);
			});
	});
});
</script>
<?php

?>