<?php

class monotable {
  
  function __construct() {
    $this->headers=array();
    $this->offsets=array();
  }

  function feed($data) {
    $ret=array();
    foreach(explode($data,"\n") as $line) {
      $line=$this->parseLine($line);
      if(!is_null($line)) $ret[]=$line;
    }
    return $ret;
  }

  function read($filename) {
    $ret=array();
    foreach(file($filename) as $line) {
      $line=$this->parseLine($line);
      if(!is_null($line)) $ret[]=$line;
    }
    return $ret;
  }

  function parseLine($line) {
    $ret=array();

    if(!$this->headers)
      return $this->parseHeader($line);
    $line=rtrim($line);

    
    $len=count($this->offsets);
    for($i=0;$i<$len;$i++) {
      list($a,$b)=$this->getBoundaries($i);
      $ret[$this->headers[$i]]=rtrim(substr($line,$a,$b));
    }
    return $ret;
  }

  function getBoundaries($field) {
    $a=$this->offsets[$field];
    @$b=is_null($z=$this->offsets[$field+1])?-1:$z-$a;
    return array($a,$b);
  }

  function parseHeader($line) {
    $line=rtrim($line);
    preg_match_all('#\S+#',$line,$M,PREG_OFFSET_CAPTURE);
    foreach($M[0] as $m) {
      $this->headers[]=$m[0];
      $this->offsets[]=$m[1];
    }
    return null;
  }
}

/*?
$monotable=new monotable();
var_export($monotable->read("flat_table.txt"));
 */
