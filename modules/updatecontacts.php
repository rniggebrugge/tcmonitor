<table id="list">
<tr>
<td sort=id>id<input type=text data-filter="id"></td>
<td sort=title>name<input type=text data-filter="title"></td>
<td sort=email>email<input type=text data-filter="email"></td>
<td clearfilters>X</td></tr></table>
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

	$("td[clearfilters]").css({cursor:"pointer"}).click(function(){
		$("input[data-filter]").val(""); updateFilter();
	})
	$("td[sort]").css({cursor:"pointer"}).click(function(){
		var sort=$(this).attr("sort"), 
			asc=$("#list tr:eq(1)").attr("data-"+sort)>$("#list tr:eq(2)").attr("data-"+sort);
		$("#list tr:gt(0)").sort(function(a, b){return a.dataset[sort] > b.dataset[sort] ? (asc?1:-1):(asc?-1:1)}).appendTo("#list");
	});
	$("input[data-filter").click(function(){return false}).change(updateFilter)

	 function updateFilter(){ 
		$("#list tr:gt(0)").each(function(){
			var visible=true, $tr=$(this);
			$("input[data-filter").each(function(){
				if($(this).val()!=="" && $tr.attr("data-"+$(this).attr("data-filter")).toLowerCase().indexOf($(this).val().toLowerCase())==-1) visible=false;
			});
			if(!visible) $(this).addClass("hide_row");
			else $(this).removeClass("hide_row");
		});
	}
	
});
</script>