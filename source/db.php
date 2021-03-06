<?php
/***************************************************************
* db->create_type(type): creates empty folder for type
* db->select(type , id, req1[] , req2[] , bool): selects assets
* 		based type, id, two requirements array and a bool (true=AND, false=OR)
*
*
*
*
*
***************************************************************/

class Database
{
	function __construct($path){
		$this->filepath = $path;
		$this->types = [];
		$this->fields_a = [];
		$this->fields_b = [];
		$full_index = [];
		$assets = scandir($path);
		foreach($assets as $asset){
			if(substr($asset,0,1)!=="." && is_dir($path.$asset)){
				$this->types[]=$asset;
				$this->fields_a[$asset] = [];
				$this->fields_b[$asset] = [];
			}
		}
		$this->schemas=[];
		$this->first_field_b = [];
		$this->read_schema();
		$this->last_results = [];
	}

	function create_type($type){
		if(in_array($type, $this->types)) return;
		$test = mkdir($this->filepath.$type, 0777);
		if ($test) {
			$test = mkdir($this->filepath.$type.'/a', 0777);
			$test = mkdir($this->filepath.$type.'/b', 0777);
			$test = mkdir($this->filepath.$type.'/changed', 0777);
			$test = mkdir($this->filepath.$type.'/deleted', 0777);
			$this->types[]=$type;
			$this->fields_a[$type] = [];
		} else {
			echo "<h1>WARNING: COULD NOT CREATE FOLDER FOR $type!</h1>";
		}
	}

	function is_type($type){
		return in_array($type, $this->types);
	}

	function item_exists($type, $id){
		$id=(int)$id;
		if(!$id || !$type || !$this->is_type($type))return false;
		return is_file($this->filepath.$type.'/a/'.$id);
	}

	function select($type, $id = 0, $req1 = [], $req2 = [], $AND = true){
		$this->last_results = [];
		$id = (int)$id;
		if(!$this->is_type($type)) return false;
		if($id && !$this->item_exists($type, $id)) return false;
		$schema = $this->schemas[$type];
		$files = scandir($this->filepath.$type.'/a/');
		$count = 0;
		foreach($files as $file) if ((int)$file>0){
			$s_id = (int)$file;
			if ($id>0 && $s_id!==$id) continue;
			$lines_a = file($this->filepath.$type.'/a/'.$s_id, FILE_IGNORE_NEW_LINES);
			$lines_b = file($this->filepath.$type.'/b/'.$s_id, FILE_IGNORE_NEW_LINES);
			$m1 = $this->calculate_match_a($lines_a, $req1, $AND);
			if ($AND && $m1==0) continue;
			$m2 =  $this->calculate_match_b($lines_b, $req2, $AND);
			if($AND && $m1 && $m2 || !$AND && ($m1 || $m2)){
				$count ++;
				unset($this->fields_a[$type][$s_id]);
				$this->fields_a[$type][$s_id]=[];
				unset($this->fields_b[$type][$s_id]);
				$this->fields_b[$type][$s_id]=[];
				for($i=1;$i<count($lines_a);$i++) {
					$temp = splitter($lines_a[$i]);
					$this->fields_a[$type][$s_id][$temp[0]]=trim($temp[1]);
					if (isset($schema["fields_a"][$temp[0]])) {
						$label = $this->read_associated_label($schema["fields_a"][$temp[0]], (int)$temp[1]);
					} else {
						$label="";
					}
					if($label)$this->fields_a[$type][$s_id][$temp[0]."__"]=$label;
				}
				$this->fields_a[$type][$s_id]["last_update"]=human_date($lines_a[0]);
				for($i=0;$i<count($lines_b);$i++) {
					$temp  = splitter($lines_b[$i]);
					$this->fields_b[$type][$s_id][$temp[0]]=trim(q_dec($temp[1]));
				}
				$this->last_results[$s_id]=array_merge($this->fields_a[$type][$s_id], $this->fields_b[$type][$s_id]);
			} 
		}
		return $count;
	}

	function read_associated_label($type, $id){
		if(!$type||!$id||!$this->is_type($type)||!$this->item_exists($type, $id))return false;
		$lines = file($this->filepath.$type.'/a/'.$id, FILE_IGNORE_NEW_LINES);
		return q_dec(splitter($lines[1])[1]);
	}

	function get_results(){
		return $this->last_results;
	}

	function sort_results($field = "title", $asc = true, $strCmp = true){
		if ($field=="id"){
			if ($asc) ksort($this->last_results);
			else krsort($this->last_results);
		} else {
		uasort($this->last_results, function($a, $b) use ($field, $asc, $strCmp){
				$v1 = isset($a[$field])?$a[$field]:($strCmp?"":0);
				$v2 = isset($b[$field])?$b[$field]:($strCmp?"":0);
				return ($strCmp?strcmp($v1,$v2):$v1-$v2)*($asc?1:-1);
			});
		}
	}


	function get_item($type, $id){
		$id = (int)$id;
		if(!$id || $id === 0) return false;
		if(!in_array($type, $this->types)) return false;
		if(!isset($this->fields_a[$type][$id])) {
			$test = $this->select($type, $id);
			if (!$test || $test === 0) return [];
			return $this->last_results[$id];
		} else {
			return array_merge($this->fields_a[$type][$id], $this->fields_b[$type][$id]);
		}
	}

	function calculate_match_a($lines, $requirements, $AND){
		$m=0; 
		if (count($requirements)===0)return 1;
		if(isset($requirements["title"])){
			$parts = splitter($lines[1]);
			if(strpos($parts[1], $requirements["title"])!==false) $m++;
			else if ($AND)return 0;
			unset($requirements["title"]);
		}
		for($i=2;$i<count($lines); $i++){
			$parts = splitter($lines[$i]);
			if(isset($requirements[$parts[0]])){
				if($parts[1]==$requirements[$parts[0]]) $m++; 
				else if($AND)return 0;
			}
		}
		return $m;
	}

	function calculate_match_b($lines, $requirements, $AND){
		$m=0;
		if (count($requirements)===0)return 1;
		for($i=0;$i<count($lines); $i++){
			$parts = splitter($lines[$i]);
			if(isset($requirements[$parts[0]])){
				if(strpos($parts[1],$requirements[$parts[0]])!==false) $m++;
				else if($AND)return 0;
			}
		}
		return $m;
	}

	function delete_item($type, $id){
		if (!$this->is_type($type)) return "requested type unknown";
		if (!$this->item_exists($type, $id)) return "could not delete object";
		$lines_a = file($this->filepath.$type.'/a/'.$id);
		$lines_b = file($this->filepath.$type.'/b/'.$id);
		$test = unlink($this->filepath.$type."/a/".$id);
		if(!$test) return "could not remove file /a/".$id;
		file_put_contents($this->filepath.$type.'/deleted/'.$id, implode("", $lines_a)."".implode("\n",$lines_b));
		$test = unlink($this->filepath.$type."/b/".$id);
		return "";
	}


	function save_asset($asset = "", $id = 0, $fields_a = [], $fields_b = []){
		$id=(int)$id;
		if(!$asset || $asset === "") return 0;
		if($id>0 && !isset($this->fields_a[$asset][$id])) return 0;
		if((!$id || $id === 0) && count($fields_a) === 0 ) return 0;
		if($id && !is_file($this->filepath.$asset.'/a/'.$id)) return 0;
		if($id && count($fields_a) === 0 ) $fields_a = $this->fields_a[$asset][$id];
		if($id && count($fields_b) === 0 ) $fields_b = $this->fields_b[$asset][$id];
		if(!in_array($asset, $this->types)) $this->create_type($asset);
		if(!$id) $id = $this->next_id($asset);
		file_put_contents($this->filepath.$asset.'/a/'.$id,date("Y-m-d-H-i-s")."\n");
		foreach($fields_a as $key=>$value ){
			file_put_contents(
				$this->filepath.$asset.'/a/'.$id, 
				$key.'='.$value."\n", FILE_APPEND | LOCK_EX);
		}
		file_put_contents($this->filepath.$asset.'/b/'.$id,"");
		if (count($fields_b)> 0){
			foreach($fields_b as $key=>$value ){
				file_put_contents(
					$this->filepath.$asset.'/b/'.$id, 
					$key.'='.q_enc($value)."\n", FILE_APPEND | LOCK_EX);
			}
		}
		return $id;
	}

	function save_raw($asset, $text_a, $text_b, $id = 0){
		$id=(int)$id;
		if(!$asset || $asset === "") return 0;
		$text_a = $this->encode_line_breaks($text_a);
		$text_b = $this->encode_line_breaks($text_b);
		if(!in_array($asset, $this->types)) $this->create_type($asset);
		if(!$id) $id = $this->next_id($asset);
		file_put_contents($this->filepath.$asset.'/a/'.$id,date("Y-m-d-H-i-s")."\n");
		file_put_contents($this->filepath.$asset.'/a/'.$id, $text_a , FILE_APPEND | LOCK_EX);
		file_put_contents($this->filepath.$asset.'/b/'.$id, $text_b);
		$this->select($asset, $id);
	}

	function next_id($type){
		$max = 99999;
		$path = $this->filepath.$type.'/a/';
		$files = scandir($path , SCANDIR_SORT_DESCENDING);
		if (isset($files[0]) && (int)$files[0]>0){
			$found_id = (int)$files[0];
			if($found_id>$max)$max=$found_id;
		}
		$max++;
		return $max;
	}

	function find_latest($type, $limit = 0, $number = 1){
		$found = [];
		$path = $this->filepath.$type.'/a/';
		$files = array_diff(scandir($path, SCANDIR_SORT_DESCENDING), array('..','.'));
		while(count($found)<$number && $item = array_shift($files)){ 
			$id=(int)$item;
			if($limit === 0 || $id<$limit) { $found[$id]=$this->get_item($type, $id); }
		}		
		return $found;
	}

	function load_full_index($types = []){
		if (!$types || count($types)===0) $types=$this->types;
		foreach($types as $type) if(in_array($type, $this->types)){
			if(isset($this->full_index[$type]))continue;
			$this->full_index[$type]=[];
			$files = array_diff(scandir($this->filepath.$type.'/a/'), array('..','.'));
			foreach($files as $file) {
				$temp = [];
				$s_id = (int)$file;
				$temp[]=$s_id;
				$lines = file($this->filepath.$type.'/a/'.$s_id, FILE_IGNORE_NEW_LINES);
				$temp[]=$lines[0];
				$parts = splitter($lines[1]);
				$temp[]=$parts[1];
				$this->full_index[$type][]=$temp;
			}	
		}
	}

	function full_index_exists($type){
		return isset($this->full_index[$type]);
	}

	function get_list($type){
		if(!in_array($type,$this->types)) return false;
		if(!$this->full_index_exists($type))$this->load_full_index([$type]);
		$result=[];
		$items = $this->full_index[$type];
		usort($items, "cmp2");
		foreach($items as $item) $result[$item[0]]=$item[2];
		return $result;
	}

	function read_schema(){
		$lines = file($this->filepath.'__schema', FILE_IGNORE_NEW_LINES);
		$type="";
		$fields="A";
		$temp = [];
		$first_field_b = false;
		foreach($lines as $line) {
			$line = trim($line);
			if(substr($line,0,3)=="***"){
				if($type!=="")$this->schemas[$type]=$temp; 
				$type=substr($line,3);
				if(!in_array($type, $this->types)) $this->create_type($type);
				$fields="A";
				$temp = [];
				$temp["fields_a"]=[];
				$temp["fields_b"]=[]; 
			}
			else if(substr($line,0,3)=="---") {$fields="B"; $first_field_b = true; }
			else if(!$line || $line === "" || substr($line,0,1) == "#") continue;
			else {
				$parts = explode("|", $line);
				if(count($parts)<2 || !$parts[1])$parts[1]="";
				if($fields=="A")$temp["fields_a"][$parts[0]]=$parts[1];
				else {	
					$temp["fields_b"][$parts[0]]=$parts[1];
					if ($first_field_b) $this->first_field_b[$type] = $parts[0];
					$first_field_b = false;
				}
			}
		}
		$this->schemas[$type]=$temp;
	}

	function edit_form($type, $id=0){  
		if(!isset($this->schemas[$type]))return;
		if(!in_array($type, $this->types)) $this->create_type($asset);
		$s = $this->schemas[$type];
		$item = $this->get_item($type, $id);
		if(count($item)===0){
			$id = 0;
			$title = "new $type";
		} else {
			$keys_a = array_keys($s["fields_a"]);
			$keys_b = array_keys($s["fields_b"]);
			$f = $keys_a[0];
			$title = $item[$f];
		}
		echo "<form method='POST'><table>";
		echo "<input type='hidden' value='$id' name='id'>";
		echo "<input type='hidden' name='type' value='$type'>";
		echo "<input type='hidden' name='edit_form' value='save'>";
		echo "<tr><td colspan=2 class=header><b>$title </b>($type  <u style=''>$id</u>)</td></tr>";
		echo "<tr><td colspan=2><hr></td></tr>";
		foreach($s["fields_a"] as $field=>$field_type){
			$keys_a = remove_element_by_value($keys_a, $field);
			echo "<tr><td>".$field."</td><td>";
			echo $this->add_edit_field($field, $field_type, isset($item[$field])?$item[$field]:"");
			echo "</td></tr>";
		}
		echo "<tr><td colspan=2><hr></td></tr>";
		foreach($s["fields_b"] as $field=>$field_type){
			$keys_b = remove_element_by_value($keys_b, $field);
			echo "<tr><td>".$field."</td><td>";
			echo $this->add_edit_field($field, $field_type, isset($item[$field])?$item[$field]:"");
			echo "</td></tr>";
		}
		if (count($keys_a) || count($keys_b)){
			echo '<tr><td colspan=2><hr></td></tr>';
			foreach($keys_a as $k){
				echo '<tr><td>'.$k.'</td><td>';
				echo $this->add_edit_field($k, '',  isset($item[$field])?$item[$field]:"");
				echo '</td></tr>';
			}
			foreach($keys_b as $k){
				echo '<tr><td>'.$k.'</td><td>';
				echo $this->add_edit_field($k, '',  isset($item[$field])?$item[$field]:"");
				echo '</td></tr>';
			}
		}
		echo "<tr><td>&nbsp;</td><td><input type=submit value=save end_action='close'> | ";
		echo "<input type=button value=cancel end_action='close'></td></tr>";
		echo "</table></form>";
	}

	function save_from_form(){
		if(!POST)return false;
		$p=$_POST;
		if(!isset($p["id"]) || !isset($p["type"]) || !$p["type"])return false;
		$type=$p["type"];
		if(!isset($this->schemas[$type]))return false;
		$id=(int)$p["id"];
		$current = false;
		$changes = [];
		if(!$id) $id = $this->next_id($type);
		else {
			$current = $this->get_item($type, $id);
		}
		if(!in_array($type, $this->types)) $this->create_type($asset);

		$s = $this->schemas[$type];

		$text_a = date("Y-m-d-H-i-s")."\n";
		$text_b = "";
		foreach($s["fields_a"] as $field=>$field_type){
			$text_a .= $field."=";
			if($field_type && $field_type=="checkbox") $value=isset($p[$field])?1:0;
			else $value=$p[$field];
			$text_a .= $value."\n";
			if(!isset($current[$field]) && $value) $changes[]="+ added field $field with value '".htmlentities($value)."'";
			else if(isset($current[$field]) && $current[$field]!=$value) $changes[]="* changed field $field with from '".htmlentities($current[$field])."' to '".htmlentities($value)."'";
			unset($current[$field]);
		}
		foreach($s["fields_b"] as $field=>$field_type){
			$text_b .= $field."=";
			if($field_type && $field_type=="checkbox") $value=isset($p[$field])?1:0;
			else $value=$p[$field];
			if(!isset($current[$field]) && $value) $changes[]="+ added field $field with value '".htmlentities($value)."'";
			else if(isset($current[$field]) && $current[$field]!=$value) $changes[]="* changed field $field with from '".htmlentities($current[$field])."' to '".htmlentities($value)."'";
			unset($current[$field]);
			$text_b .= q_enc($value)."\n";
		}

		foreach($current as $key=>$value) if(substr($key,-2)!=="__"){
			$changes[] = "- removed field: $key = $value";
		}
		file_put_contents($this->filepath.$type.'/a/'.$id, $text_a);
		file_put_contents($this->filepath.$type.'/b/'.$id, $text_b);
		
		if (count($changes)){
			file_put_contents($this->filepath.$type.'/changes/'.$id,"::::".date("Y-m-d-H-i-s")."\n", FILE_APPEND | LOCK_EX);
			foreach($changes as $line) {
				file_put_contents($this->filepath.$type.'/changes/'.$id,$line."\n", FILE_APPEND | LOCK_EX);
			}
		}
		return $id;
	}

	function add_edit_field($name, $specs, $value=""){
		if(!$specs)$specs="";
		if(!$name || $name === "") return "x";
		if (!$specs || $specs ==="") return "<input type=text size=95 name='$name' value='$value'>";
		if ($specs =="date") return "<input type=date size=15 name='$name' value='$value'>";
		if ($specs=="textarea") return "<textarea name='$name' rows=8 cols=100>$value</textarea>";
		if ($specs=="checkbox") return "<input type=checkbox name='$name' id='cb_$name' ".($value==1?"checked":"")."><label for='cb_$name'>$name</label>";
		else { 
			if(!$this->full_index_exists($specs)) $this->load_full_index([$specs]);
			$txt="<select name='$name'>";
			$items = $this->full_index[$specs];
			usort($items, "cmp2");
			foreach($items as $item){
				$txt.="<option value='".$item[0]."' ".($item[0]==$value?"selected":"").">".$item[2]."</option>";
			}
			$txt.="</select>";
			return $txt;
		}
	}

function encode_line_breaks($text){
	$pattern = '/^[^=]*$/m';
	while(preg_match($pattern, $text, $matches)){
		foreach($matches as $i=>$m){ 
			$text = str_replace("\r\n$m", urlencode("\n").$m, $text);
			$text = str_replace("\n$m", urlencode("\n").$m, $text);
		}
	}
	return $text;
}

/// TEST FUNCTIONS ---\/--------------
	function show_active_assets(){
		foreach($this->fields_a as $asset_type=>$assets)if(count($assets)>0){
			foreach($assets as $id=>$fields_a){
				echo "<div style='background:#000; border:3px solid #009; padding:10px; margin:10px; color:#fff'>";
				echo $asset_type." / ".$id."<br>";
				foreach($fields_a as $key=>$value){
					echo "<b>".$key." : </b>".$value."<br>";
				}
				foreach($this->fields_b[$asset_type][$id] as $key=>$value){
					echo "<b>".$key." : </b>".$value."<br>";
				}
				echo "</div>";
			}
		}
	}

	function show_all(){
		$this->load_full_index();
		echo "<table>";
		foreach($this->types as $type){
			echo "<tr><td colspan=3><h2>$type</h2></td></tr>";
			$items = $this->full_index[$type];
			usort($items, "cmp2");
			foreach($items as $item){
				echo "<tr><td>".$item[0]."</td><td>".$item[1]."</td><td>".$item[2]."</td></tr>";
			}
		}
		echo "</table>";
	}
}

// Helper function that could also be used by other classes
function cmp2($a, $b){
	if($a[2] === $b[2]) return 0;
	return $a[2]<$b[2]?-1:1;
}
function splitter($line, $separator="="){
	$isPos = strpos($line, $separator);
	return [substr($line, 0, $isPos), substr($line, $isPos+1)];
}
function q_enc($t){
	return urlencode($t);
}
function q_dec($t){
	return stripslashes(urldecode($t));
}
function remove_element_by_value($array, $value){
	return array_diff($array, [$value]);
}
function human_date($date){
	$t = explode("-", $date);
	return $t[0]."/".$t[1]."/".$t[2]." at ".$t[3].":".$t[4].":".$t[5];
}