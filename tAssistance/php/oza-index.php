<?php

if($_GET["page"]=="Trace" && $_GET["trace_uri"]){

	//$traceid = $_GET["traceid"];
	$trace_uri = $_GET["trace_uri"];

	$page = file_get_contents("/var/www/tconnect/tAssistance/html/Traces.html");
	$page = str_replace("\$user_id", "undefined", $page);
	$page = str_replace("\$trace_uri", $trace_uri, $page);

	echo $page;
	exit;
}
elseif($_GET["widget"]== "OzaTraceSearch" && !isset($_GET["search"])){
	require_once "/var/www/tconnect/project/Ozalid/TStore/OzaTStoreClient.php";
	require_once "/var/www/tconnect/tAssistance/php/OzaTraceMenu.php";
	require_once "/var/www/tconnect/tAssistance/php/OzaTraceSearch.php";
	
	// data
	$tstore = new OzaTStoreClient();
	$traces = $tstore->getTraces();
	
	// presentation
	$search = new OzaTraceSearch($traces);
	$html = $search->toHtml();

	echo $html;
	exit;
}
elseif($_GET["widget"]== "OzaTraceSearch" && isset($_GET["search"])){
	require_once "/var/www/tconnect/project/Ozalid/TStore/OzaTStoreClient.php";	
	require_once "/var/www/tconnect/tAssistance/php/OzaTraceSearch.php";

	$tstore = new OzaTStoreClient();
	
	//$users = $tstore->getUsers();
	
	$traces = $tstore->getTraces();
	
	$search = $_GET["search"];
	
	$traceSearch = new OzaTraceSearch($traces);
	$html = $traceSearch->search($search);
	
	echo $html;
	exit;
}