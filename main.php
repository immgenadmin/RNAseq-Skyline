<?php
	$url = $_SERVER['REQUEST_URI'];
	if(isset($_GET['gene'])){
		$tmp = explode("=", $url);
		$gene = $tmp[1]; 
	} else {
		$gene = "None";
	}
?>
