$(function(){
	$("table#list tr:eq(0) td").css({background:"#039",color:"#fff", fontWeight:"bold"});
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
				var str = $tr.attr("data-"+$(this).attr("data-filter")).toLowerCase();
				var lookFor = $(this).val().toLowerCase();
				var pattern = new RegExp(lookFor);
				if(lookFor!=="" && str.search(pattern)==-1) visible=false;
			});
			if(!visible) $(this).addClass("hide_row");
			else $(this).removeClass("hide_row");
		});
	}
});
