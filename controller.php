<?php

namespace limepie;

class Controller
{
	public		$framework;
	private		$route;
	protected	$segment; // number
	protected	$query;   // named

	public function __construct() 
	{
		$this->framework	= \limepie\framework::getInstance();
		$this->route		= $this->framework->route;
		$this->segment		= $this->route->getSegment();
		$this->parameter	= $this->route->getParameter();
	}
	protected function getRoute() 
	{
		return $this->route;
	}
	protected function getPrevRoute() 
	{
		return $this->route->prev;
	}
	protected function getSegment($key = false, $def = '') 
	{
		return $this->route->getSegment($key);
	}
	protected function getParameter($key = false, $end = false) 
	{
		return $this->route->getParameter($key, $end);
	}
	protected function getUri() 
	{
		return $this->route->pathinfo();
	}
	protected function getModule() 
	{
		return $this->route->getModule();
	}
	protected function getController() 
	{
		return $this->route->getController();
	}
	protected function getAction() 
	{
		return $this->route->getAction();
	}
	protected function getErrorController() 
	{
		return $this->route->getErrorController();
	}
	public function forward($d, $args = array()) 
	{
		return array(
			'type'	=> 'forward',
			'route'	=> $d,
			'args'	=> $args
		);
	}
	public function throw_exception($message = '', $method = 'error', $args = array()) 
	{
		return $this->route->setException($message, $method, $args);
	}
}
