<?php

namespace limepie;

class view 
{
	public $tpl_ = array();
	public $var_ = array();
	public function __construct() {
	}	
	function assign($arg) {
		if (is_array($arg)) {
			$this->var_ = array_mix( $arg , $this->var_);
		} else {
			if(count(func_get_args())>1) {
				if(false == isset($this->var_[$arg])) {
					$this->var_[$arg] = array();
				}
				$this->var_[$arg] = func_get_arg(1);//array_mix (func_get_arg(1), $this->var_[$arg]);
			}
		}
	}
	public function define($arg, $path='') {
		if ($path) $this->_define($arg, $path);
		else foreach ($arg as $fid => $path) $this->_define($fid, $path);
	}
	public function _define($fid, $path) {
		if(is_array($path)) {
			$this->tpl_[$fid] = array('string', $path);// string array로 직접 넘김
		} else if ($fid[0] == '>') {
			$this->tpl_[substr($fid,1)] = array('php', array($path,'',''));//
		} else {
			$this->tpl_[$fid] = array('file', array($path,'',''));//
		}
	}
	public function display($fid, $print = false) {
		if($print) {
			$this->render($fid);
		} else {
			return $this->fetch($fid);
		}
	}
	public function fetch($fid) {
		ob_start();
		$this->render($fid);
		$fetch = ob_get_contents();
		ob_end_clean();
		return $fetch;
	}
	public function render($fid) {
		$tpl_path		= $this->tpl_path($fid);
		$compile_path	= $tpl_path;//.".php";

		if(false == is_file($tpl_path)) {
			throw new \limepie\view\Exception('템플릿 파일이 없음 : '.$tpl_path);
		}

		$this->_include_tpl($compile_path, $fid);//, $scope);
	}
	public function _include_tpl($TPL_CPL, $TPL_TPL) {//, $TPL_SCP)
		extract($this->var_);
		if (false===include $TPL_CPL) {
			throw new \limepie\view\Exception('#'.$TPL_TPL.' include error '.$TPL_CPL);
		}
	}
	public function tpl_path($fid) {
		$path = $this->tpl_[$fid][1][0];
		if($path[0] == '/') {
			return $path;
		} else {
			$a = trim($this->skin,'/');
			return rtrim($this->tpl_path,'/')."/".($a ? $a.'/' : '').$path;
		}
	}
}

function array_mix( array $array1 = array(), array $array2 = array()) {
	$merged = $array1;
	foreach ($array2 as $key => &$value) {
		if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
			$merged[$key] = array_mix($merged [$key], $value );
		} else {
			$merged[$key] = $value;
		}
	}
	return $merged;
}
