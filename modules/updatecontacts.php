<table id="list"><tr><th sort=id>id</th><th sort=title>name</th><th sort=email>email</th><th>&nbsp;</th></tr></table>

<script>
$(function(){
	var request_url="/tcmonitor/communication/contact_point/items/country=<?php echo $country;?>";
	$.post(request_url, function(data){
		data = $.parseJSON(data);
		if(data.error && data.error!=="") return;
		data = data.data;
			data.forEach(function(item){
				var table=$("#list"), 
					tr = $("<tr data-id="+item.id+" data-email='"+item.email+"' data-title='"+item.title+"'></tr>"), 
					td;
				td = $("<td>"+item.id+"</td>");
				tr.append(td);
				td = $("<td>"+item.title+"</td>");
				tr.append(td);
				td = $("<td>"+item.email+"</td>");
				tr.append(td);
				td = $("<td>"+item.country+"</td>");
				tr.append(td);
				table.append(tr);

			});
	});


	$("th[sort]").click(function(){
		var sort=$(this).attr("sort"); 
		$("#list tr:gt(0)").sort(function(a, b){return a.dataset[sort] > b.dataset[sort] ? 1: -1}).appendTo("#list");
	})
});
</script>