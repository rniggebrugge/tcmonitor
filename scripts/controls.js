$(function(){
	$("table#list tr:eq(0) td").css({background:"#039",color:"#fff", fontWeight:"bold"});
	$("td[clearfilters]").css({cursor:"pointer"}).click(function(){
		$("input[filter]").val(""); updateFilter();
	})
	$("td[sort]").css({cursor:"pointer"}).click(function(){
		var sort=$(this).attr("sort"), 
			asc=$("#list tr:eq(1)").attr("data-"+sort)>$("#list tr:eq(-1)").attr("data-"+sort);
		$("#list tr:gt(0)").sort(function(a, b){return a.dataset[sort] > b.dataset[sort] ? (asc?1:-1):(asc?-1:1)}).appendTo("#list");
	});
	$("input[filter").click(function(){return false}).change(updateFilter)

	 function updateFilter(){ 
		$("#list tr:gt(0)").each(function(){
			var visible=true, $tr=$(this);
			$("input[filter").each(function(){
				var str = $tr.attr("data-"+$(this).attr("filter")).toLowerCase();
				var lookFor = $(this).val().toLowerCase();
				var pattern = new RegExp(lookFor);
				if(lookFor!=="" && str.search(pattern)==-1) visible=false;
			});
			if(!visible) $(this).addClass("hide_row");
			else $(this).removeClass("hide_row");
		});
	}
});

function addHover(tr){
	tr.hover(function(){$(this).css({background:"#09f"})}, function(){$(this).css({background:"#fff"})})
}

function addHoverClass(){
	$(this).addClass("hover_row");
}
function removeHoverClass(){
	$(this).removeClass("hover_row");
}

function createTable(table, data, cells, actions){
	data.forEach(function(item){
		var tr=$("<tr></tr>"), td, field, button, w = 0;
		tr.hover(addHoverClass, removeHoverClass)
		for(field in item) tr.attr("data-"+field,item[field]);
		for(field in cells){
			if(cells[field]=="country"){
				td = $("<td><img src='http://www.ejnforum.eu/cp/uploaded_content/spacer.png' class='flag_sprite' style='background-position:"+flags_pos[item.country]+"'>"+ item.country__+"</td>");
			} else if (cells[field]=="EMPTY") {
				td=$("<td>&nbsp;</td>");
			}else {
				td=$("<td>"+item[cells[field]]+"</td>");
			}
			tr.append(td);
		}
		if(actions!==false){
			td=$("<td></td>");
			for(field in actions){
				button = $("<button style='width:100px; margin-right:10px'>"+field+"</button>");
				button.click(actions[field]);
				td.append(button);
				w+=120;
			}
			td.css({width:w+"px"});
			tr.append(td)
		}
		table.append(tr);
	});
}

function updateMe(){
	alert($(this).parent().html());
}
function deleteMe(){
	alert($(this).closest("tr").attr("data-title"));
}

