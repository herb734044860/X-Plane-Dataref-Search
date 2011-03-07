<?php
global $datarefs;
$datarefs = array();
global $types;
$types = array('');
global $writables;
$writables = array('');
global $units;
$units = array('');
global $version;

global $time;

function parse()
{
	global $version;
	global $time;
	$t_start = microtime(true);
	$txt = file_get_contents('DataRefs.txt');
	$lines = explode("\n",$txt);
	$v = explode(" ",$lines[0]);
	$version = 'Datarefs for X-Plane '.$v[1].', compiled '.$v[2].' '.$v[3].' '.$v[4].' '.$v[5].' '.$v[6];
	for($i=1; $i<count($lines);$i++) {
		parseLine($lines[$i]);
	}
	$time = round((microtime(true)-$t_start)*1000)/1000;
}
function parseLine($line)
{
	global $datarefs;
	global $types;
	global $writables;
	global $units;
	if($line!='') {
		$parts = preg_split("/\s+/",$line,5);
		for($i=0;$i<5;$i++) {
			if (!isset($parts[$i])) {
				$parts[] = '';
			} else $parts[$i] = trim($parts[$i]);
		}
		
		$dataref = array('name'=>$parts[0],'type'=>$parts[1],'writable'=>$parts[2],'units'=>$parts[3],'description'=>utf8_decode($parts[4]));

		if (checkMatch($dataref)) {
			if (!in_array($dataref['type'],$types,false)) $types[] = $dataref['type'];
			if (!in_array($dataref['writable'],$writables,false)) $writables[] = $dataref['writable'];
			if (!in_array($dataref['units'],$units,false)) $units[] = $dataref['units'];
			$datarefs[] = $dataref;
		}
		
	}
}
function fillSelect($arr,$current = '')
{
	$o = '';
	foreach($arr as &$v) {
		if (!empty($current) && $current==$v) $selected = ' selected="selected"'; else $selected = '';
		$o.='<option value="'.$v.'"'.$selected.'>'.$v.'</option>';
	}
	return $o;
}
function fillTable()
{
	global $datarefs;
	$o = '';
	foreach($datarefs as $key=>&$d) {
		if ($key%2==1) $class = ' class="even"'; else $class = '';
		$o.='<tr'.($class).'><td class="copy"><input id="copy-'.$key.'" name="copy" type="radio" value="'.$d['name'].'"/></td><td class="name"><label for="copy-'.$key.'"><a href="javascript:void(0);">'.$d['name'].'</a></label></td><td class="type">'.$d['type'].'</td><td class="writable">'.$d['writable'].'</td><td class="units">'.$d['units'].'</td><td class="description">'.$d['description'].'</td></tr>';
	}
	return $o;
}
function checkMatch(&$dataref)
{
	$name = trim($_GET['name']);
	$type = $_GET['type'];
	$writable = $_GET['writable'];
	$units = $_GET['units'];
	$description = trim($_GET['description']);
	$match = true;

	if (!empty($name) && strpos($dataref['name'],$name)===false) $match = false;
	if (!empty($type) && $dataref['type']!=$type) $match = false;
	if (!empty($writable) && $dataref['writable']!=$writable) $match = false;
	if (!empty($units) && $dataref['units']!=$units) $match = false;
	if (!empty($description) && strpos($dataref['description'],$description)===false) $match = false;
	return $match;
}
?>