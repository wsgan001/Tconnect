<?php
$php_tstore = dirname(__FILE__);
$tstore = dirname($php_tstore);

class OzaTStore{
	
	public $db = "/var/www/tconnect/project/Ozalid/TStore/db/";
	
	function __construct(){
		global $tstore;
		$this->db = $tstore."/db/";
	}
	
	function getUserById($userid){
		$file = $this->db."users.json";
		$json = file_get_contents($file);
		$users = json_decode($json);
		
		foreach($users as $user){
			if($user->id == $userid){
				return $user;
			}
		}
		
		return false;
	}
	
	function getUsers(){
		$file = $this->db."users.json";
		$json = file_get_contents($file);
		$users = json_decode($json);
	
		return $users;
	}
	
	function addUser($user){
		$file = $this->db."users.json";
		$json = file_get_contents($file);
		$users = json_decode($json);
		
		$users[] = $user;
		
		$json = json_encode($users);
		
		$ok = file_put_contents($file, $json);
		
		return $ok;
		
	}
	
	function addTrace($trace){
		$file = $this->db."traces.json";
		$json = file_get_contents($file);
		$traces = json_decode($json);
	
		$traces[] = $trace;
	
		$json = json_encode($traces);
	
		$ok = file_put_contents($file, $json);
	
		return $ok;
	
	}
	
	function updateTrace($new_trace){
		$file = $this->db."traces.json";
		$json = file_get_contents($file);
		$traces = json_decode($json);
		
		$index = false;
		for($i=0;$i<count($traces);$i++){
			if($traces[$i]->id == $new_trace->id){
				$index = $i;
			}
		}
		
		if($index===false) return false;
		
		$traces[$index] = $new_trace;
		
		$json = json_encode($traces);
		
		$ok = file_put_contents($file, $json);
		
		return $ok;
		
	}
	
	function getTraces(){
		$file = $this->db."traces.json";
		$json = file_get_contents($file);
		$traces = json_decode($json);
		
		return $traces;
	}
	
	function getTraceByIds($userId, $docId){
		$file = $this->db."traces.json";
		$json = file_get_contents($file);
		$traces = json_decode($json);
		
		
		
		foreach($traces as $trace){
			$doc_id = $trace->properties->document_id;
			$user_id = $trace->properties->userid;
			if($doc_id == $docId && $user_id == $userId){
				return $trace;	
			}
		}
		
		return false;
	}
	
	function getTraceByProperties($properties){
		$file = $this->db."traces.json";
		$json = file_get_contents($file);
		$traces = json_decode($json);
		
		foreach($traces as $trace){
			$match_all = true;
			foreach($properties as $p => $value){
				$match1 = false;
				foreach($trace->properties as $p1 => $value1){
					if($p1==$p){
						if($value1==$value){
							$match1 = true;
						}else{
							$match1 = false;
						}
						break;
					}
				}
				if($match1==false) {
					$match_all = false;
					break;
				}
			}
			if($match_all){
				return $trace;
			}
		}
	
		return false;
	}
	
	function getTraceById($trace_id){
		$file = $this->db."traces.json";
		$json = file_get_contents($file);
		$traces = json_decode($json);
		
		foreach($traces as $trace){
			if($trace->id == $trace_id){
				return $trace;
			}
		}
		
		return false;
	}
	
	function getCompleteTraceById($trace_id){
		$trace = $this->getTraceById($trace_id);
		
		$obsels = $this->getObsels($trace_id);
		
		$trace->obsels = $obsels;
		
		return $trace;
	}
		
	function getObsels($traceid){
		$file = $this->db."obsels".$traceid.".json";
		$json = file_get_contents($file);
		$obsels = json_decode($json);
		
		return $obsels;
	}
	
	function addObsel($trace, $obsel){
		$trace_id = $trace->id;
		
		$file = $this->db."obsels".$trace_id.".json";
		$json = file_get_contents($file);
		$obsels = json_decode($json);
		
		$obsels[] = $obsel;
		
		$json = json_encode($obsels);
		
		$ok = file_put_contents($file, $json);
		
		return $ok;
	}
	
	function addModel($model){
		$file = $this->db."models.json";
		$json = file_get_contents($file);
		$models = json_decode($json);
		
		$models[] = $model;
		
		$json = json_encode($models);
		
		$ok = file_put_contents($file, $json);
		
		return $ok;
	}
	
	function getModels(){
		
		
	}
	
	function addDoc($doc){
		//$doc_id = $doc->id;
		
		$file = $this->db."docs.json";
		$json = file_get_contents($file);
		$docs = json_decode($json);
		
		$docs[] = $doc;
		
		$json = json_encode($docs);
		
		$ok = file_put_contents($file, $json);
		
		return $ok;
	}
	
	function updateDoc($new_doc){
		$file = $this->db."docs.json";
		$json = file_get_contents($file);
		$docs = json_decode($json);
	
		$index = false;
		for($i=0;$i<count($docs);$i++){
			if($docs[$i]->id == $new_doc->id){
				$index = $i;
			}
		}
	
		if($index===false) return false;
	
		$docs[$index] = $new_doc;
	
		$json = json_encode($docs);
	
		$ok = file_put_contents($file, $json);
	
		return $ok;
	
	}
	
	function getDocById($doc_id){
		$file = $this->db."docs.json";
		$json = file_get_contents($file);
		$docs = json_decode($json);
		
		foreach($docs as $doc){
			if($doc->id == $doc_id){
				return $doc;
			}
		}
		
		return false;
	}
	
	function getDocs(){
		$file = $this->db."docs.json";
		$json = file_get_contents($file);
		$docs = json_decode($json);

	
		return $docs;
	}
	
	function getTraceIdByIds($userids, $docids){
		
		$traces = $this->getTraces();
		$traceIds = array();
				
		foreach($traces as $trace){
			$doc_id = $trace->properties->document_id;
			$user_id = $trace->properties->userid;
			$type = $trace->properties->type;
			$traceid = $trace->id;
			
			if($docids===true&&$userids===true){
				$traceIds[] = "t_all";
				return $traceIds;
			}
			if($docids===true&&$userids!==true&&$type=="User"){// User Trace
				
				$ok = in_array($user_id, $userids);  
				if($ok)
					$traceIds[] = $trace->id;
				
			}
			else if($userids===true&&$docids!==true&&$type=="Doc"){
				$ok = in_array($doc_id, $docids);
				if($ok)
					$traceIds[] = $trace->id;				
			}
			else if($userids!==true&&$docids!==true&&$type=="UserDoc"){
				$user_ok = in_array($user_id, $userids);
				$doc_ok = in_array($doc_id, $docids);
				if($user_ok && $doc_ok)
					$traceIds[] = $trace->id;
			}
		}
		
		//echo json_encode($traceIds)."<br/><br/>";
		
		return $traceIds;
	}
	
	function getFusedTrace($traceids){
		
		$new_obsels = array();
		
		foreach($traceids as $traceid){
			
			$trace = $this->getCompleteTraceById($traceid);
			$obsels = $trace->obsels;
			$new_obsels = array_merge($new_obsels,$obsels);			
		}
		
		$new_trace = new stdClass();
		$new_trace->id= "t".time();
		$new_trace->obsels = $new_obsels;
		
		//echo json_encode($new_trace);
		
		return $new_trace;
	}
	
	function getFusedTraceByIds($userids, $docsids){
		$traceids = $this->getTraceIdByIds($userids, $docsids);
		
		//echo json_encode($traceids);
		
		$trace = $this->getFusedTrace($traceids);
		return $trace;
	}
}


