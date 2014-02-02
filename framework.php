<?php

namespace limepie;


class framework
{
	private static $instance = null;
	public $route;
	public function __construct() {}
	public function __destruct() {}
	public static function getInstance()
	{       
		if (null === self::$instance) {      
			self::$instance = new \limepie\framework;      
		}   
		return self::$instance;  
	}
	public function setRouter($route) 
	{
		$route->route();
		$this->route = $route;
	}
	private function action($args = null) 
	{
		$access			= $this->route->getParameter('access');
		$module			= $this->route->getParameter('module');
		$controller		= $this->route->getParameter('controller');
		$action			= $this->route->getParameter('action');
		$basedir		= $this->route->getParameter('basedir');
		$prefix			= $this->route->getParameter('prefix');
		$errorClassName	= $this->route->getErrorController();

		$namespaceName	= strtr($this->route->getControllerNamespace(), array(
			'<basedir>'			=> ($basedir ? str_replace('/','\\',$basedir) : '')
			, '<access>'		=> ($access ? $access : '')
			, '<prefix>'		=> ($prefix ? $prefix:'')
			, '<module>'		=> ($module ? $module : '')
			, '<controller>'	=> ($controller ? $controller : '')
			, '<action>'		=> ($action ? $action : '')
		));
		
		$className		= ($controller).$this->route->getControllerSuffix();
		$actionName		= $action.$this->route->getActionSuffix();
	   
		$baseFolderName	= (strtr($this->route->getControllerDir(), array(
			'<basedir>'			=> ($basedir ? str_replace('\\','/',$basedir) : '')
			, '<access>'		=> ($access ? $access : '')
			, '<prefix>'		=> ($prefix ? $prefix:'')
			, '<module>'		=> ($module ? $module : '')
			, '<controller>'	=> ($controller ? $controller : '')
			, '<action>'		=> ($action ? $action : '')
		)));
		$fileName		= __ROOT__.'/'.($baseFolderName.'/'.$className.'.php');
		$folderName		= dirname($fileName);
		define('__CONTROLLER_DIR__', $folderName);

		$_args = array(
			'access'	=> $access,
			'folder'	=> $folderName,
			'file'		=> $fileName,
			'namespace'	=> $namespaceName,
			'class'		=> $className,
			'method'	=> $actionName
		);
		$callClassName	= $namespaceName.'\\'.$className;
		if(($class_exist = class_exists($callClassName, false)) /* 로드됨 */ || ($file_exist = is_file($fileName)) /* 파일이 있음 */) {
			$insObj = null;
			if(isset($file_exist) && $file_exist) {

				require_once(($fileName));
				if (!($class_exist = class_exists($callClassName, false))) {
					return $this->route->setException(\limepie\_('클레스 없음'), 'class_does_not_exist', $_args);
				}
			}
			$insObj = new $callClassName;
			if(method_exists($insObj, '__init')) {
				$tmp = $insObj->__init();	
				if(is_null($tmp) === false) {
					return $tmp;
				}
			}
			if(($method = (true === method_exists($insObj, REQUEST_METHOD.'_'.$actionName)) && is_callable(array(&$insObj, REQUEST_METHOD.'_'.$actionName)) ) 
				|| (true === method_exists($insObj, $actionName) && is_callable(array(&$insObj, $actionName)) )
			) { /* request type을 메소드 명에 붙여 하나의 url로 두가지 역할 할수 있음 */
				if(isset($method) === true && $method === true) {
					$actionName = REQUEST_METHOD.'_'.$actionName;
				}
				return call_user_func_array(array(&$insObj, $actionName), $args ? (is_array($args) ? $args : array($args)) : $this->route->getSegment());
			} else {
				return $this->route->setException(\limepie\_('메소드 없음'), 'method_does_not_exist', $_args);
			}
		} else if(!is_dir($folderName)) {
			return $this->route->setException(\limepie\_('폴더 없음'), 'folder_does_not_exist', $_args);
		} else {
			return $this->route->setException(\limepie\_('파일 없음'), 'file_does_not_exist', $_args);
		}
	}
	public function forward($config) 
	{
		return $ret = $this->_forward($config['route'], (isset($config['args']) ? $config['args'] : array()));
	}
	private function _forward($array = array()) 
	{
		$prev_route			= $this->route;
		$router				= new \limepie\Router($array);
		$router->setError($this->route->defaultError);

		$front				= framework::getInstance();
		$front->setRouter($router);

		$new_route			= $front->route;
		$new_route->prev	= $prev_route;

		if($prev_route->matchRoute == $new_route->matchRoute) {
			return $this->route->setException(\limepie\_('같은 곳으로 포워딩 되고 있습니다.'), 'forward_loop', array());
		}
		return $front->dispatch();
	}
	public function dispatch($args = null) 
	{
		$ret = null;
		if($config = $this->action($args)) {
			if(is_array($config) === false && is_null($config) === false) {
			}

			if(is_array($config) === true && isset($config['type']) === true) {
				$url = is(@$config['action'], @$config['path'], @$config['url']);
				switch($config['type']) {
					case 'template' :
						break;
					case 'forward' : // forward to another action
						$ret = $this->_forward($config['route'], (isset($config['args']) ? $config['args'] : array()));
						break;
					case 'redirect' : // redirect to other location
						redirect($url, @$config['msg']);
						break;
					case 'submit' :
						submit($url, $config);
						break;
					case 'mail' :
						break;
					default :
						echo 'default';
				}
			}  else {
				$ret = $config;
			}
		}  else {
		}
		return $ret;
	}
}
