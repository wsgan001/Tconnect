<?php 

if($_SERVER['PATH_INFO']=="/OzaTraceSearch" && $_GET["search"]){
	// read data input
	//require_once "/var/www/tconnect/project/Ozalid/TStore/OzaTStoreClient.php";
	//require_once "/var/www/tconnect/tAssistance/php/OzaTraceMenu.php";
	//require_once "/var/www/tconnect/tAssistance/php/OzaTraceSearch.php";

	

	exit;
}

elseif($_SERVER['PATH_INFO']=="/words"){
	//require_once "$ozalid_tstore/OzaTStoreClient.php";
	//require_once "$tassist_dir/php/OzaTraceList.php";

	//echo "aaa";
	$maker = new OzaWordFreMaker();

	exit;
}