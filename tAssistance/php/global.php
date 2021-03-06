<?php
session_start();// it works only on global

$db_server = "localhost";
$db_user = "tuser";
$db_pwd = "tuser";
$db_name = "tassistance";
$debug = false;
$base_uri = "https://dsi-liris-silex.univ-lyon1.fr/ozalid/ktbs/ANONYMOUS/";
$ktbs_uri = "https://dsi-liris-silex.univ-lyon1.fr/ozalid/ktbs/";
//$base_uri = "http://localhost:8001/ANONYMOUS/";

$tconnect_dir = dirname(dirname(dirname(__FILE__)));
$tassist_php_dir = dirname(__FILE__);
$tassist_dir = dirname(dirname(__FILE__));
$ozalid_dir = $tconnect_dir."/project/Ozalid";
$ozalid_tstore = $ozalid_dir."/TStore";
$ozalid_correct = $ozalid_dir."/CorrectionServer";

require_once $tassist_php_dir."/OzaDoc.php";
require_once $tassist_php_dir."/TAssistantClient.php";
require_once $tassist_php_dir."/OzaObselList.php";
require_once $tassist_php_dir."/OzaObselSearch.php";
require_once $tassist_php_dir."/OzaTraceList.php";
require_once $tassist_php_dir."/OzaTraceSearch.php";
require_once $tassist_php_dir."/TAssistant.php";
require_once $tassist_php_dir."/TAssistantClient.php";
require_once $tassist_php_dir."/TextObsel.php";
require_once $tassist_php_dir."/TraceList.php";
require_once $tassist_php_dir."/TraceSearch.php";
require_once $tassist_php_dir."/creator/OzaWordFreMaker.php";
require_once $tassist_php_dir."/store/User.php";
require_once $tassist_php_dir."/common/_Object.php";
require_once $tassist_php_dir."/common/URI.php";
require_once $tassist_php_dir."/common/FileUploader.php";

require_once $ozalid_tstore."/OzaTStoreClient.php";
require_once $ozalid_tstore."/php/OzaTStore.php";

require_once $ozalid_correct."/OzaEditorClient.php";

$tassist_html_dir = $tassist_dir."/html";


// style
function style_indexOf($styles,$style_id){
	//echo "data=".$data;
	for($i=0; $i<count($styles);$i++){
		$style=$styles[$i];
		if($style->id==$style_id){
			return $i;
		}
	}
	return -1;
}

function style_get_all(){
	$filename = "json/style.json";
	$data = file_get_contents($filename);
	$styles = json_decode($data);
	return $styles;
}

function style_put_all($styles){
	$filename = "json/style.json";
	$styles_in_json = json_encode($styles);
	$ok = file_put_contents($filename, $styles_in_json);
	return $ok;
}

function style_post($style){
	$styles = style_get_all();
	$pos=style_indexOf($styles, $style->id);
	//echo "data=".$data;
	if($pos==-1){
		// add new style
		$styles[]= $style;
		style_put_all($styles);
		return $ok;
	}
	else{
		return "exist";
	}
}

function style_put($style){
	$styles = style_get_all();
	$pos = style_indexOf($style->name);
	if($pos==-1){// if not exist, create new
		$styles[]= $style;
		$ok = style_put_all($styles);
		return $ok;
	}
	else{// if exist, replace
		$styles[$pos] = $style;		
		$ok = style_put_all($styles);
		return $ok;
	}
}

function style_delete($style_id){
	$styles = style_get_all();
	$pos=style_indexOf($styles,$style_id);
	array_splice($styles, $pos, 1);
	$ok=style_put_all($styles);
	return $ok;
}

function get_style_by_user($user){
	global $db_server,$db_user,$db_pwd,$db_name;

	$con = mysqli_connect($db_server,$db_user,$db_pwd,$db_name);
	// Check connection
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	$sql = "select * from ta_style where user = '".user. "' ";

	$ret = array();

	if ($result = mysqli_query($con,$sql))
	{
		while ($row = mysqli_fetch_object($result)) {
			$ret[] = $row;
		}
	}
	else{
		die('Error: ' . mysqli_error($con));
	}
		
	mysqli_close($con);

	return $ret;
}
// user
function get_user($session_id){
	global $db_server,$db_user,$db_pwd,$db_name;

	$con = mysqli_connect($db_server,$db_user,$db_pwd,$db_name);
	// Check connection
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: ".mysqli_connect_error();
		exit;
	}

	$select_sql = "SELECT * FROM ta_session WHERE local_session = '".$session_id."' order by modified_date desc";

	if ($result = mysqli_query($con,$select_sql))
	{
		$row = mysqli_fetch_object($result);
		return $row->user;
	}
}

// session & key
function get_new_key($app_id){
	global $db_server,$db_user,$db_pwd,$db_name;

	$con = mysqli_connect($db_server,$db_user,$db_pwd,$db_name);
	// Check connection
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: ".mysqli_connect_error();
		exit;
	}

	$select_sql = "SELECT * FROM ta_key WHERE remote_app = '".$app_id."' ";

	if ($result = mysqli_query($con,$select_sql))
	{
		$row_num = mysqli_num_rows($result);
		if($row_num==0){

			function generateRandomString($length = 10) {
				$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$randomString = '';
				for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, strlen($characters) - 1)];
				}
				return $randomString;
			}

			$priv_key = generateRandomString();
			$insert_sql = "INSERT INTO ta_key (remote_app, priv_key, modified_date) VALUES ('".$app_id."', '".$priv_key."', NOW()) ";
				
			if (!mysqli_query($con,$insert_sql))
			{
				echo 'Error: ' . mysqli_error($con);
				exit;
			}
			$key = new stdClass();
			$key->value =  $priv_key;
			$ret = json_encode($key);
			echo $ret;
		}
		else{
			$row = mysqli_fetch_object($result);
			$priv_key = $row->priv_key;
			$key = new stdClass();
			$key->value =  $priv_key;
			$ret = json_encode($key);
			echo $ret;
		}

		exit;
	}
}

function update_session($session){
	global $db_server,$db_user,$db_pwd, $db_name;

	$user = $session["user_id"];
	$remote_app= $session["app_id"];
	$remote_session = $session["session_id"];

	$con = mysqli_connect($db_server,$db_user,$db_pwd,$db_name);
	// Check connection
	if (mysqli_connect_errno())
	{
		echo "Failed to connect to MySQL: ".mysqli_connect_error();
		exit;
	}

	$select_sql = "SELECT 1 FROM ta_session WHERE remote_app = '".$remote_app."' and remote_session = '".$remote_session."'";

	if ($result = mysqli_query($con,$select_sql))
	{
		$row_num = mysqli_num_rows($result);
		if($row_num==0){
			$insert_sql = "INSERT INTO ta_session (remote_app, remote_session, user) VALUES ('".$remote_app."', '".$remote_session."', '".$user."')";

			if (!mysqli_query($con,$insert_sql))
			{
				echo 'Error: ' . mysqli_error($con);
				exit;
			}
		}
		else{
			$update_sql="UPDATE ta_session SET user = '".$user."', modified_date=NOW() WHERE remote_app = '".$remote_app."' and remote_session = '".$remote_session."'";

			if (!mysqli_query($con,$update_sql))
			{
				echo 'Error: ' . mysqli_error($con);
				exit;
			}
		}
	}
	else{
		die('Error: ' . mysqli_error($con));
	}

	mysqli_close($con);
}
// rule
function rule_get_all(){
	$filename = "json/rule.json";
	$data = file_get_contents($filename);
	$docs = json_decode($data);
	return $docs;
}

function rule_get_full(){
 	$rules = rule_get_all();
 	$styles = style_get_all();
 	$selectors = selector_get_all();
	
 	$rules_full = array();
	for($i=0;$i<count($rules);$i++){
		$rule = $rules[$i];
		$item = new stdClass();
		$item->id = $rule->id;
		
		$iStyle = style_indexOf($styles, $rule->style);
		$item->style = $styles[$iStyle]->script;
		
		$iSelector = selector_indexOf($selectors, $rule->selector);
		$item->selector = $selectors[$iSelector]->script;
		
		$item->priority = $rule->priority;
		$rules_full[]= $item;
	}
	return $rules_full;
}

function rule_indexOf($rules, $rule_id){
	//echo "data=".$data;
	for($i=0; $i<count($rules);$i++){
		$rule=$rules[$i];
		if($rule->id==$rule_id){
			return $i;
		}
	}
	return -1;
}

function rule_put_all($rules){
	$filename = "json/rule.json";
	$rules_in_json = json_encode($rules);
	$ok = file_put_contents($filename, $rules_in_json);
	return $ok;
}

function rule_post($rule){
	$rules = rule_get_all();
	$pos=rule_indexOf($rules,$rule->id);
	
	if($pos==-1){
		// add new style
		$rules[]= $rule;
		rule_put_all($rules);
		return $ok;
	}
	else{
		return "exist";
	}
}

function rule_put($rule){
	$rules = rule_get_all();
	$pos=rule_indexOf($rules,$rule->id);
	
	if($pos==-1){// if not exist, create new
		$rules[]= $rule;
		$ok = rule_put_all($rules);
		return $ok;
	}
	else{// if exist, replace
		$rules[$pos] = $rule;
		$ok = rule_put_all($rules);
		return $ok;
	}
}

function rule_delete($rule_id){
	$rules = rule_get_all();
	$pos=rule_indexOf($rules,$rule_id);
	array_splice($rules, $pos, 1);
	$ok=rule_put_all($rules);
	return $ok;
}

// selector
function selector_get_all(){
	$filename = "json/selector.json";
	$data = file_get_contents($filename);
	$docs = json_decode($data);
	return $docs;
}

function selector_indexOf($selectors, $selector_id){
	//echo "data=".$data;
	for($i=0; $i<count($selectors);$i++){
		$selector=$selectors[$i];
		if($selector->id==$selector_id){
			return $i;
		}
	}
	return -1;
}

function selector_put_all($selectors){
	$filename = "json/selector.json";
	$selectors_in_json = json_encode($selectors);
	$ok = file_put_contents($filename, $selectors_in_json);
	return $ok;
}

function selector_post($selector){
	$selectors = selector_get_all();
	$pos=selector_indexOf($selectors,$selector->id);

	if($pos==-1){
		// add new selector
		$selectors[]= $selector;
		selector_put_all($selectors);
		return $ok;
	}
	else{
		return "exist";
	}
}

function selector_put($selector){
	$selectors = selector_get_all();
	$pos=selector_indexOf($selectors,$selector->id);

	if($pos==-1){// if not exist, create new
		// add new selector
		$selectors[]= $selector;
		selector_put_all($selectors);
		return $ok;
	}
	else{// if exist, replace
		$selectors[$pos] = $selector;
		$ok = selector_put_all($selectors);
		return $ok;
	}
}

function selector_delete($selector_id){
	$selectors = selector_get_all();
	$pos=selector_indexOf($selectors,$selector_id);
	array_splice($selectors, $pos, 1);
	$ok=selector_put_all($selectors);
	return $ok;
}


 



