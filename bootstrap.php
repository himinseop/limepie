<?php

/* configure start */
define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);

spl_autoload_register(function ($class) {
	$file = (preg_replace('#\\\|_(?!.+\\\)#','/', $class) . '.php');

	if (stream_resolve_include_path($file)) {
		require $file;
	} else {
		echo 'file not found : '.$file.'<br />';
	}
});

define('__ROOT__', realpath(dirname(__file__).'/..'));

set_include_path(
	__ROOT__
	.PS.'.'
);


if (!defined('PHP_EOL')) define('PHP_EOL', strtoupper(substr(PHP_OS,0,3) == 'WIN') ? "\r\n" : "\n");

define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) 
					&& !empty($_SERVER['HTTP_X_REQUESTED_WITH']) 
					&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

define('IS_POST', isset($_SERVER["REQUEST_METHOD"]) 
					&& !empty($_SERVER["REQUEST_METHOD"]) 
					&& strtolower($_SERVER["REQUEST_METHOD"]) == 'post');

define('REQUEST_METHOD', (strtolower($_SERVER["REQUEST_METHOD"] ? $_SERVER["REQUEST_METHOD"] : 'get')));


define('HTTP_PROTOCAL',	(strtolower(getenv('HTTPS')) == 'on' ? 'https' : 'http') . '://');
define('HTTP_HOST',		getenv('HTTP_HOST'));
define('HTTP_PORT',		 (($p = getenv('SERVER_PORT')) != 80 AND $p != 443 ? ":$p" : ''));
define('DOMAIN',		HTTP_PROTOCAL
						.HTTP_HOST
						.HTTP_PORT
		);
define('REQUEST_URI',	getenv('REQUEST_URI'));
define('URI',			DOMAIN.REQUEST_URI);

/* namespace 제거 */
function _t($msgid, $arr = null) {
	return \limepie\_($msgid, $arr);
}
function __t($module, $msgid, $arr = null) {
	return \limepie\__($module, $msgid, $arr);
}
/*필수파일 인클루드*/

require_once("limepie/function.php");
require_once("limepie/language.php");


