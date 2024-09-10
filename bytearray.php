<?php
/*
ByteArray v2.3
testwillistroj.cekuj.net/nic/ByteArray.php
(c) 2019-2020 by František Čech. All rights reserved.
*/
class ByteArray{
	public $bytes = [];
	public function __construct($maysource = false, $maymode = "utf8"){
		if($maysource != false)
			$this->from($maysource, $maymode);
	}
	public function from($src, $mode = "utf8"){
		if($mode == "utf8" and gettype($src) == "string" and strlen($src) > 0){
			$arr = [];
			foreach (str_split($src) as $chr){
				$arr[] = ord($chr);
			}
			$this->bytes = array_merge($this->bytes, $arr);
			return $this;
		}
		elseif($mode == "hex" and gettype($src) == "string" and strlen($src) > 0){
			$arr = [];
			for($i = 0; $i < strlen($src); $i += 2)
				$arr[] = hexdec($src[$i].$src[$i + 1]);
			$this->bytes = array_merge($this->bytes, $arr);
			return $this;
		}
		elseif($mode == "bin" and gettype($src) == "string" and strlen($src) > 0){
			$arr = [];
			for($i = 0; $i < strlen($src); $i += 8)
				$arr[] = bindec(substr($src, $i, 8));
			$this->bytes = array_merge($this->bytes, $arr);
			return $this;
		}
		if($mode == "latin1" and gettype($src) == "string" and strlen($src) > 0){
			return $this->from(utf8_decode($src), "utf8");
		}
		elseif($mode == "base64" and gettype($src) == "string" and strlen($src) > 0){
			return $this->from(base64_decode($src), "utf8");
		}
		elseif($mode == "bytes" and gettype($src) == "array" and count(array_filter($src, function($item){return is_int($item);})) > 0){
			$this->bytes = array_merge($this->bytes, $src);
			return $this;
		}
		elseif($mode == "words" and gettype($src) == "array" and count(array_filter($src, function($item){return is_int($item);})) > 0){
			$arr = [];
			for($wo = 0; $wo < count($src); $wo++)
				$arr = array_merge($arr, [$src[$wo] >> 24, ($src[$wo] & 0xff0000) >> 16, ($src[$wo] & 0xff00) >> 8, $src[$wo] & 0xff]);
			$this->bytes = array_merge($this->bytes, $arr);
			return $this;
		}
		else
			return false;
	}
	public function export($enc = "utf8"){
		if($enc == "utf8"){
			$exportstr = "";
			foreach($this->bytes as $byte)
				$exportstr .= chr($byte);
			return $exportstr;
		}
		elseif($enc == "hex"){
			$exportstr = "";
			foreach($this->bytes as $byte)
				$exportstr .= substr("00".dechex($byte), -2);
			return $exportstr;
		}
		elseif($enc == "bin"){
			$exportstr = "";
			foreach($this->bytes as $byte)
				$exportstr .= substr("00000000".decbin($byte), -8);
			return $exportstr;
		}
		elseif($enc == "latin1"){
			return utf8_encode($this->export("utf8"));
		}
		elseif($enc == "base64"){
			$exportstr = $this->export("utf8");
			return base64_encode($exportstr);
		}
		elseif($enc == "bytes")
			return $this->bytes;
		elseif($enc == "words"){
			$arr = [];
			for($i = 0; $i < count($this->bytes); $i += 4)
				$arr[] = $this->bytes[$i] << 24 | $this->bytes[$i + 1] << 16 | $this->bytes[$i + 2] << 8 | $this->bytes[$i + 3];
			return $arr;
		}
		else
			return false;
	}
}