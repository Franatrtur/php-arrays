<?php
function rang($leng){
	$ret = [];
	for($i = 0; $i < $leng; $i++)
		$ret[$i] = $i;
	return $ret;
}
class eArray{
	public $v = [];
	public $length = 0;
	function __construct(...$args){
		$this->v = [];
		if(gettype($args[0]) == "integer" and count($args) == 1){
			foreach(rang($args[0]) as $i)
				$this->v[$i] = null;
		}
		elseif(gettype($args[0]) == "array")
			$this->v = $args[0];
		else
			$this->v = $args;
		$this->length = count($this->v);
	}
	public function delete($idx){
		unset($this->v[$idx]);
		$this->length = count($this->v);
		return $this->length;
	}
	public function i(...$args){
		if(count($args) == 1)
			return $this->v[$args[0]];
		$did = $this->v[$args[0]];
		$this->v[$args[0]] = $args[1];
		$this->length = count($this->v);
		return $did;
	}
	public function push($itm){
		$this->v[] = $itm;
		$this->length = count($this->v);
		return $this->length;
	}
	public function map($callb, $args = false){
		$ret = new eArray();
		foreach(rang(count($this->v)) as $ix)
			$ret->push($callb($this->i($ix), $ix, $this, $args));
		return $ret;
	}
	public function join($glue){
		return implode($glue, $this->v);
	}
	public function reduce($callb, $init, $args = false){
		$ret = $init;
		foreach(rang($this->length) as $ix)
			$ret = $callb($ret, $this->i($ix), $ix, $this, $args);
		return $ret;
	}
	public function reverse(){
		$ret = new eArray();
		foreach(rang($this->length) as $i)
			$ret->push($this->i($this->length - $i - 1));
		return $ret;
	}
	public function concat(...$arrs){
		$conc = $this->slice(0);
		foreach($arrs as $arr){
			foreach($arr->v as $itm)
				$conc->push($itm);
		}
		return $conc;
	}
	public function fill($with){
		$ret = new eArray();
		foreach($this->v as $itm)
			$ret->push($with);
		return $ret;
	}
	public function filter($callb, $args = false){
		$ret = new eArray();
		foreach(rang(count($this->v)) as $ix){
			if($callb($this->i($ix), $ix, $this, $args))
				$ret->push($this->i($ix));
		}
		return $ret;
	}
	public function includes($val){
		return $this->indexOf($val) >= 0;
	}
	public function indexOf($val){
		foreach($this->v as $ix => $itm){
			if($itm === $val)
				return $ix;
		}
		return -1;
	}
	public function pop(){
		$ret = $this->i($this->length - 1);
		unset($this->v[$this->length - 1]);
        $this->length = count($this->v);
		return $ret;
	}
	public function every($callb, $args = false){
		return $this->filter($callb, $args)->length == $this->length;
	}
	public function some($callb, $args = false){
		return $this->filter($callb, $args)->length > 0;
	}
	public function slice($arg1, $arg2 = null){
		if($this->length == 0)
			return $this;
		$arg1 %= $this->length;
		$arg1 %= $this->length;
		if($arg1 < 0)
			$arg1 = $arg1 % $this->length + $this->length;
		if(gettype($arg2) == "integer" and $arg2 < 0)
			$arg2 = $arg2 % $this->length + $this->length;
		if($arg1 > $arg2 && gettype($arg2) == "integer")
			return new eArray();
		if($arg2 === null){
			return $this->filter(function($n, $i, $nie, $ee){
				return $i >= $ee;
			}, $arg1);
		}
		else{
			return $this->filter(function($n, $i, $nie, $ee){
				return $i >= $ee[0] and $i < $ee[1];
			}, [$arg1, $arg2]);
		}
	}
	public function shift(){
		$rev = $this->reverse();
		$rev->pop();
		$this->v = $rev->reverse()->v;
		$this->length--;
		return $this->length;
	}
	public function unshift(...$itms){
		$rev = $this->reverse();
		$this->v = $rev->concat(new eArray($itms))->reverse()->v;
		$this->length++;
		return $this->length;
	}
}