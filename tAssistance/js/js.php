<?php

require 'jsmin.php';

header("Content-type: text/javascript");

$debug = true;

$files = array("../js/jquery.js"
				,"../js/d3.v3.js"
				,"../js/fisheye.js"
				//,"../lib/tAssistantTraceWidget.js"
				,"../lib/tAssistant.js"	
				);

$js = "";
foreach($files as $f)
  $js .= file_get_contents($f);
  
if ($html) {
  foreach($filesHtml as $f)
  $js .= file_get_contents($f);
}

if($debug) {
  echo $js;
} else {
  echo $jsmin_php = JSMin::minify($js);
}

?>