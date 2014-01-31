<?php

namespace limepie;

class Controller
{
	public		$framework;
	private		$route;
	protected	$segment; // number
	protected	$query;   // named

	public function __construct() {
		$this->framework	= \limepie\framework::getInstance();
		$this->route		= $this->framework->route;
		$this->segment		= $this->route->getSegment();
		$this->parameter	= $this->route->getParameter();
	}
	protected function getRoute() {
		return $this->route;
	}
	protected function getPrevRoute() {
		return $this->route->prev;
	}
	protected function getSegment($key = false, $def = '') {
		return $this->route->getSegment($key);
	}
	protected function getParameter($key = false, $end = false) {
		return $this->route->getParameter($key, $end);
	}
	protected function getUri() {
		return $this->route->pathinfo();
	}
	protected function getModule() {
		return $this->route->getModule();
	}
	protected function getController() {
		return $this->route->getController();
	}
	protected function getAction() {
		return $this->route->getAction();
	}
	protected function getErrorController() {
		return $this->route->getErrorController();
	}
	protected function getSegAsArray($num=3) {
		$_path	= array_slice ($this->raw, $num);
		$max	= count($_path) + 1; 
		$_vars	= array();
		for ($i=0; $i<=$max-2; $i+=2) { 
			$_vars[$_path[$i]] = (isset($_path[$i+1]) ? $_path[$i+1] : '');
		} 
		return $_vars;
	}
	public function forward($d, $args = array()) {
		return array(
			'type'	=> 'forward',
			'route'	=> $d,
			'args'	=> $args
		);
	}
	public function throw_exception($message = '', $method = 'error', $args = array()) {
		return $this->route->setException($message, $method, $args);
	}
}
