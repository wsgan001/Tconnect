<?php
$dir=dirname(__FILE__);
require_once $dir.'/N3Parser.php';
require_once $dir.'/RestfulHelper.php';

class KtbsObsel {
	public $uri = null;
	public $name = null;
	public $type = null;
	public $hasSuperObselType = null;
	public $model_uri = null;
	public $trace_uri = null;
	public $hasBegin = null;
	public $hasEnd = null;
	public $hasBeginDT = null;
	public $hasEndDT = null;
	public $comment = null;
	public $keytype = null;
	public $keycode = null;
	public $character = null;
	public $element_id = null;
	
	function __construct($model_uri,$trace_uri){
		//$name = "keyevent_".rand();
		
		//$this->name=$name;
		//$this->uri=$trace_uri.$name;
		$this->model_uri = $model_uri;
		$this->trace_uri = $trace_uri;
	}
	
	public function load($obsel){
		
		$this->hasBegin = $obsel->begin;
		$this->hasEnd = $obsel->end;
		
		$this->subject = "TODO";
		$this->name = $obsel->type.$obsel->begin;
		$this->type = $obsel->type;
		$this->user = $obsel->user;
		$this->idSession = $obsel->idSession;
		$this->idDoc = $obsel->idDoc;
		$this->idPage = $obsel->idPage;
		
		foreach ($obsel as $p => $value){
			if(!strncmp($p,"info_",strlen("info_"))){
				$this->{$p} = $value;
			}
		}
	}
	
	public function exist(){
		global $firephp;
		$url = $this->trace_uri."".$this->name.".txt";
		$data = file_get_contents($url);
		$ret = strlen(trim($data)) > 0;
		if($ret){
			$firephp->log("".$this->name." exists","I");
		} else{
			$firephp->log("GET ".$url." is failed","I");
		}
		return $ret;
	}

	public function dump(){
				
		$prefixes[] = "@prefix xsd: <http://www.w3.org/2001/XMLSchema#> .";
		$prefixes[] = "@prefix ktbs: <http://liris.cnrs.fr/silex/2009/ktbs#> .";
		$prefixes[] = "@prefix : <".$this->model_uri."> .";
				
		
		$statements[] = "<".$this->name."> a :".$this->type." .";
		$statements[] = "<".$this->name."> ktbs:hasTrace <> .";		
		$statements[] = "<".$this->name."> ktbs:hasBegin ".$this->hasBegin." .";
		$statements[] = "<".$this->name."> ktbs:hasEnd ".$this->hasEnd." .";
		$statements[] = "<".$this->name."> ktbs:hasSubject ".'"'.$this->subject.'"'." .";
		
		$statements[] = "<".$this->name."> :user ".'"'.$this->user.'"'." .";
		$statements[] = "<".$this->name."> :idSession ".'"'.$this->idSession.'"'." .";
		$statements[] = "<".$this->name."> :idDoc ".'"'.$this->idDoc.'"'." .";
		$statements[] = "<".$this->name."> :idPage ".'"'.$this->idPage.'"'." .";
		
		//$statements[] = "<".$this->name."> :description ".N3Parser::encode($this->description)." .";
		
		foreach ($this as $p => $value){
			if(!strncmp($p,"info_",strlen("info_"))){
				$statements[] = "<".$this->name."> :$p ".N3Parser::encode($this->$p)." .";
			}
		}
			
		$this->script = implode("\n", $prefixes)."\n"
						.implode("\n", $statements);
		
		$this->numAttr = count($statements);
		$this->result = RestfulHelper::post($this->trace_uri, $this->script);
		if($this->result) {
			echo $this->name." is created\n";
		} else {
			echo $this->name." is NOT created\n";
		}
	}
}
?>