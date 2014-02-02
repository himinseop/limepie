<?php

namespace limepie;
/*
	$router = new \lime\nrouter(array(
		//'(?P<module>admin|order)(?:/(?P<parameter>.*))?' => array(), 
		'(?P<module>[^/]+)?(?:/(?P<year>[^/]+))?(?:/(?P<parameter>.*))?' => array(
			//'basedir' => 'test'
		)
	));
	$router->setError('apps_error');
*/
class router 
{
	private $pathinfo;
	private $route					= array();
	private	$segment				= array();
	private	$parameter				= array();
	private $basedir				= '';
	private $prefix					= '';
	private $access					= 'front';
	private $module					= 'welcome';
	private $controller				= 'run';
	private $action					= 'index';
	private $error					= '\limepie\error';
	private $matchRoute;
	private $systemVariables		= array('access', 'basedir','module','action','controller','prefix'); // paramter로 받을수 없는 변수
	private	$controllerDir			= '<basedir>/<module>/<access>';
	private	$controllerNamespace	= '<basedir>\<module>\<access>';
	private	$actionSuffix			= 'Action';
	private	$controllerSuffix		= 'Controller';
	public	$isError				= false;
	public	$prev;

	public function __construct($route = array())
	{
		$this->setControllerDirinfo();
		$this->route				= $route;
	}
	public function setException($message = '', $method = 'error', $args = array()) 
	{
		$this->isError				= true;
		$class						= $this->getErrorController();
		$tmpObj						= new $class;
		$_args						= array(
			'method'				=> $method,
			'message'				=> $message
		);
		$tmpObj->info				= $_args + array('trace' => $args);
		if(is_callable(array(&$tmpObj, $method))) {
			return call_user_func_array(array(&$tmpObj, $method), $args);
		} else {
			throw new \Exception('error '.$method.' method_does_not_exist');
		}
	}
	/*uri에 있을 경우 access세팅하고 parameter에서 삭제*/
	public function setAccessByFirstDir($mode, $default = false)
	{
		if(preg_match('#((?P<access>'.implode('|',$mode).')\/)#',$this->getControllerDirinfo(), $m) 
			&& isset($m['access'])) {
			$this->setControllerDirinfo(str_replace($m['access'].'/','',$this->getControllerDirinfo()));
			$this->setDefaultAccess($m['access']);
		} else if($default) {
			$this->setDefaultAccess($default);
		} else {
			$this->setDefaultAccess($mode[0]);
		}
	}
	/*domain에 있을 경우 access세팅*/
	public function setAccessByDomain($mode, $default = false)
	{
		if(preg_match('#((?P<access>'.implode(',|',$mode).')\.)#',$_SERVER['HTTP_HOST'], $m) 
			&& isset($m['access'])
		) {
			$this->setDefaultAccess($m['access']);
		} else if($default) {
			$this->setDefaultAccess($default);
		} else {
			$this->setDefaultAccess($mode[0]);
		}
	}
	public function setAccess($callback)
	{
		$callback($this);
	}
	public function setControllerDir($dir)
	{
		$this->controllerDir = $dir;
	}
	public function getControllerDir()
	{
		return $this->controllerDir;
	}
	public function setControllerNamespace($class)
	{
		$this->controllerNamespace = $class;
	}
	public function getControllerNamespace()
	{
		return $this->controllerNamespace;
	}
	public function addRoute($route = array())
	{
		$this->route = $route;	
	}
	public function setDefaultBaseDir($basedir)	
	{
		$this->basedir = $basedir;
	}
	public function getDefaultBaseDir()
	{
		return $this->basedir;
	}
	public function setDefaultPrefix($prefix)
	{
		$this->prefix = $prefix;
	}
	public function getDefaultPrefix()
	{
		return $this->prefix;
	}
	public function setDefaultAccess($access)
	{
		$this->access = $access;
	}
	public function getDefaultAccess()
	{
		return $this->access;
	}
	public function setDefaultModule($module)
	{
		$this->module = $module;
	}
	public function getDefaultModule()
	{
		return $this->module;
	}
	public function setDefaultController($controller)
	{
		$this->controller = $controller;
	}
	public function getDefaultController()
	{
		return $this->controller;
	}
	public function setDefaultAction($action)
	{
		$this->action = $action;
	}
	public function getDefaultAction()
	{
		return $this->action;
	}
	public function setControllerSuffix($controller)
	{
		$this->controllerSuffix = $controller;
	}
	public function getControllerSuffix()
	{
		return $this->controllerSuffix;
	}
	public function setActionSuffix($action)
	{
		$this->actionSuffix		= $action;	
	}
	public function getActionSuffix()
	{
		return $this->actionSuffix;	
	}
	public function setErrorController($error)
	{
		$this->error = $error;
	}
	public function getErrorController()
	{
		return $this->error;
	}
	public function getControllerDirinfo()
	{
		return $this->pathinfo;
	}
	public function setControllerDirinfo($pathinfo = false) 
	{
		$this->pathinfo	= $pathinfo ? $pathinfo : (true === isset($_SERVER['PATH_INFO']) ? trim($_SERVER['PATH_INFO'],'/') : '');
		$this->segment	= explode('/',$this->pathinfo);
	}
	public function getParameter($key=false)
	{
		if(false === $key) {
			return $this->parameter;
		}
		return true === isset($this->parameter[$key]) ? $this->parameter[$key] : null;
	}
	public function getParam($key=null)
	{
		return $this->getParameter($key);
	}
	public function getSegment($key=false, $end = false)
	{
		if(false === $key) {
			return $this->segment;
		}
		if(true === $end) {
			return implode('/',array_slice ($this->segment, $key));      
		}
		return true === isset($this->segment[$key]) ? $this->segment[$key] : null;
	}
	private function setDefaultParameter()
	{
		$ret = array();
		foreach($this->systemVariables as $key) {
			$ret[$key] = $this->{'getDefault'.$key}();
		}
		return $ret;
	}
	public function route()
	{
		if(false == is_array($this->route) || 0 == count($this->route)) {
			$this->route = array(
				'((?P<access>backend)(?:/)?)?(?P<module>[^/]+)?(?:/(?P<controller>[^/]+))?(?:/(?P<action>[^/]+))?(?:/(?P<parameter>.*))?' => array()
				// '(?P<parameter>.*)' => array()
			);
		}
		foreach($this->route as $rule => $default) {
			if(preg_match('#^'.$rule.'$#', $this->pathinfo, $m1)) {
				$parameter = $this->setDefaultParameter();
				$this->parameter = $default + $parameter; // $default 우선

				$_path	= isset($m1['parameter']) && trim($m1['parameter']) != '' ? explode('/',rtrim($m1['parameter'], '/')) : array();
				for($i=0,$max=count($_path)+1;$i<=$max-2;$i+=2) { 
					if(
						true === isset($_path[$i]) && $_path[$i] 
						&& false == in_array($_path[$i], $this->systemVariables)
					) {
						$this->parameter[$_path[$i]] = (true === isset($_path[$i+1]) ? $_path[$i+1] : '');
					}
				}
				foreach($m1 as $key => $value) {
					if(false === is_numeric($key) && $value) {
						$this->parameter[$key] = $value;
					}
				}
				$this->matchRoute = array($rule => $default);
				break;
			}	
		}
		pr($this->parameter);
		return $this->parameter;
	}
}
