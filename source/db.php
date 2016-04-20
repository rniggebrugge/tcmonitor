<?php
class Database
{
	function __construct($path){
		$this->filepath = $path;
		$this->types = [];
		$assets = scandir($path);
		foreach($assets as $asset){
			if(substr($asset,0,1)!=="." && is_dir($path.$asset)){
				$this->types[]=$asset;
			}
		}
		$this->active_id = 0;
		$this->active_asset = "";
		$this->fields_a = [];
		$this->fields_b = [];
		$this->search_results = [];
	}

	function create_type($type){
		if(in_array($type, $this->types)) return;
		$test = mkdir($this->filepath.$type, 0777);
		if ($test) $this->types[]=$type;
	}

	function select($type, $id = 0, $req1 = [], $req2 = []){
		$id = (int)$id;
		if(!in_array($type, $this->types)) return [];
		if($id && !is_file($this->filepath.$type.'/'.$id.'_a.txt')) return [];
		$files = scandir($this->filepath.$type.'/');
		$this->search_results = [];
		foreach($files as $file) if (preg_match('/('.($id?$id:'\d+').')_a/', $file, $matches)){
			$s_id = $matches[1];
			$lines = file($this->filepath.$type.'/'.$s_id.'_a.txt', FILE_IGNORE_NEW_LINES);
			$this->search_results[$s_id] = [$lines[0], $lines[1]]; 
		}
		$this->active_asset = $type;
		$this->active_id = 0;
	}


	function save_asset($asset, $id = 0, $fields_a = [], $fields_b = []){
		if ((count($fields_a)+count($fields_b)) ===0) return false;
		if(!in_array($asset, $this->types)) $this->create_type($asset);
		if($id && !is_file($this->filepath.$asset.'/'.$id.'_a.txt')) return false;
		if(!$id) $id = count(scandir($this->filepath.$asset))/2;
		file_put_contents($this->filepath.$asset.'/'.$id.'_a.txt',"Last update: ".date("Y-m-d-H-i-s")."\n");
		foreach($fields_a as $field ){
			file_put_contents(
				$this->filepath.$asset.'/'.$id.'_a.txt', 
				$field[0].'='.$field[1]."\n", FILE_APPEND | LOCK_EX);
		}
		file_put_contents($this->filepath.$asset.'/'.$id.'_b.txt',"");
		foreach($fields_b as $field ){
			file_put_contents(
				$this->filepath.$asset.'/'.$id.'_b.txt', 
				$field[0].'='.$field[1]."\n", FILE_APPEND | LOCK_EX);
		}
		return true;
	}

	function load_asset($asset, $id){
		$id=(int)$id;
		if(!$id) return false;
		if(!in_array($asset, $this->types)) return false;
		if($id && !is_file($this->filepath.$asset.'/'.$id.'_a.txt')) return false;
		if(!is_file($this->filepath.$asset.'/'.$id.'_a.txt')) return false;
		if(!is_file($this->filepath.$asset.'/'.$id.'_b.txt')) return false;
		$this->fields_a = [];
		foreach(file($this->filepath.$asset.'/'.$id.'_a.txt', FILE_IGNORE_NEW_LINES) as $i=>$line)if($i){
			$isPos = strpos($line, "=");
			$this->fields_a[]=[substr($line, 0, $isPos), substr($line, $isPos+1)];
		}
		$this->fields_b = [];
		foreach(file($this->filepath.$asset.'/'.$id.'_b.txt', FILE_IGNORE_NEW_LINES) as $i=>$line){
			$isPos = strpos($line, "=");
			$this->fields_a[]=[substr($line, 0, $isPos), substr($line, $isPos+1)];
		}
		$this->active_id = $id;
		$this->active_asset = $asset;
	}
/// TEST FUNCTIONS ---\/--------------
	function show_active_asset(){
		echo "<div style='background:#000; border:3px solid #009; padding:10px; margin:10px; color:#fff'>";
		echo $this->active_asset." - ".$this->active_id."<br>";
		foreach($this->fields_a as $field){
			echo "<b>".$field[0]." : </b>".$field[1]."<br>";
		}
		foreach($this->fields_b as $field){
			echo "<b>".$field[0]." : </b>".$field[1]."<br>";
		}
		echo "</div>";
	}

	function show_search_results(){
		echo "<div style='background:#000; border:3px solid #009; padding:10px; margin:10px; color:#fff'>";
		echo $this->active_asset."<br>";
		echo "<table style=color:white><tr><td>Id</td><td>Item</td><td>Last update</td></tr>";
		foreach($this->search_results as $id=>$item){
			echo "<tr><td>$id</td><td>".$item[1]."</td><td>".$item[0]."</td></tr>";
		}
		echo "</table>";
		echo "</div>";		
	}


}