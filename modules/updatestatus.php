<table id="list">
<tr>
<td sort=id >id<br><input type=text filter="id" size="3"></td>
<td sort=title >title<br><input type=text filter="title"></td>
<td sort=reference>reference<br><input type=text filter="reference" size="8"></td>
<td clearfilters >X</td></tr></table>


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
		console.log(data)
		createTable($("#list"), data, ["id","title","reference"], {"update":updateMe, "delete":deleteMe})
		<?php
			$db->select("country", ($country==99999?0:$country));
			$db->sort_results();
			$countries = $db->get_results();
			foreach($countries as $id=>$dets) {
				echo "// $id \n";//
				echo "//".$dets["title"]."\n";
			}
		?>
	});
});
</script>


