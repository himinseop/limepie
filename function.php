<?php

$timer = array();
function php_timer($file = '', $line = 0){
	global $timer;
	static $arr_timer;
	static $prev;
	if(!isset($arr_timer)){
		$arr_timer = explode(" ", microtime());
	}else{
		$b = $prev;
		$arr_timer2 = explode(" ", microtime());
		$result = ($arr_timer2[1] - $arr_timer[1]) + ($arr_timer2[0] - $arr_timer[0]);
		$result = sprintf("%.4f",$result);
		$a = $result - $prev;
		$prev = $result;
		return $timer[] = 'cur => '.str_pad($result,6,'0', STR_PAD_RIGHT).', prv => '.(($b ? str_pad($b,6,'0', STR_PAD_RIGHT) : '0.0000')).', diff => '.str_pad(custom_ceil((float)$a,4),6,'0', STR_PAD_RIGHT).'&nbsp; &nbsp; &nbsp; &nbsp;'.($file ? str_pad($file.' ',10,'&nbsp', STR_PAD_RIGHT) : '').', '.($line ? $line.' line ' : '');
	}
	return false;
} php_timer(__file__,__line__);


function custom_ceil($val,$pressision=2){
	$p = pow(10,$pressision);
	$val = $val*$p;
	$val = ceil($val);
	return $val /$p;
}

function microtime_float(){
	list($usec, $sec) = explode(" ", microtime());
	return /*((float)$usec.(float)$sec).'_'.*/uniqid(rand());
}

function mbstrlen($str) {
	//preg_match_all('/[\xEA-\xED][\x80-\xFF]{2}|./u', $str, $match);
	preg_match_all('/./u', $str, $match);
	$m    = $match[0];
	$slen = count($m);
	$count = 0;

	for ($i=0; $i < $slen; $i++) {
		//if(isset($m[$i]) == false) break;
		$count += (strlen($m[$i]) > 1)?2:1;
	}
	return $count;
}

if(!function_exists('print_p')) {
	function print_p($expression, $return = false) {
		return pr($expression, $return);
	}

	function pr($expression, $return = false) {
		$ret = '<pre>';
		$ret .= print_r($expression, true);
		$ret .= '</pre>';

		if(true === $return) {
			return $ret;
		} else {
			echo $ret;
		}
	}
}

function apr() {
	$args = func_get_args();
	echo "<table border=1>";
	echo "<tr>";
	foreach($args as $key => $value) {
		$value3 = '<pre>'.print_r($value, true).'</pre>';
		echo "<td valign=top align='left' width=100>".($value3)."</td>";
	}
	echo "</tr>";
	echo "</table>";
}

function lpr($line = null) {
	$args = func_get_args();
	echo "<table border=1>";
	echo "<tr>";
	foreach($args as $key => $value) {
		$value2 = explode("\n", $value);
		$value3 = '<table>';
		$i = 0;
		foreach($value2 as $k => $v) {
			$i++;
			$value3 .= '<tr><td style="background:#efefef;"><span style="font-family: monospace;">'.$i.'.</span> </td><td><pre><span style="font-family: monospace;">'.$v.'</span></pre></td></tr>';
		}
		$value3 .= '</table>';
		echo "<td valign=top align='left' width=100>".($value3)."</td>";
	}
	echo "</tr>";
	echo "</table>";
}

function readable_size($size) {
	$unit=array('B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).''.$unit[$i];
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
function alert($strMsg) {
	echo '<script type="text/javascript">alert("'.$strMsg.'");</script>';
}

function move($strUrl) {
	echo "<meta http-equiv='refresh' content='0; url=".$strUrl."'>";
}
function jsmove($strUrl) {
	echo '<script type="text/javascript">window.location.href="'.$strUrl.'";</script>';	
}

function redirect($strUrl, $strMsg='') {
	if(false === empty($strMsg)) {
		alert($strMsg);
	}
	if(false === empty($strUrl)) {
		move($strUrl);
		exit();
	}
}

function jsredirect($strUrl, $strMsg='') {
	if(false === empty($strMsg)) {
		alert($strMsg);
	}
	if(false === empty($strUrl)) {
		jsmove($strUrl);
		exit();
	}
}

function submit($url, $config) {
	$config['method'] = is(@$config['method'], 'post');
	if(isset($config['args']) == false && count($config['args']) == 0) {
		if('get' == $config['method']) {
			$config['args'] = $_GET;
		} else {
			$config['args'] = $_POST;
		}
	}
	if(isset($config['msg']) && $config['msg']) {	
		alert($config['msg']);
	}	
	$ret = "<script type='text/javascript'>function gosubmit(){var o = document.getElementById('jsform'); o.setAttribute('method','".$config['method']."'); o.setAttribute('action','".$url."'); o.submit(); } </script><form id='jsform'>";
	if(isset($config['args']) && count($config['args'])>0) {
		foreach($config['args'] as $key => $value) {
			$ret .= "<textarea name='".$key."' style='display:none'>".$value."</textarea>";
		}
	}
	$ret .= "</form><script type='text/javascript'>gosubmit();</script>";
	echo $ret;
}

function is() {
	$args = func_get_args();
	foreach($args as $key => $value) {
		if(strlen($value) > 0) {
			return $value;
		}
	}
}

/* like var_export, delete blank */
function phpvar_export($var){
    if (is_array($var)) {
        $code = 'array(';
        foreach ($var as $key => $value) {
            $code .= "'".str_replace("'","&#039;",$key)."'=>".phpvar_export(str_replace("'","&#039;",$value)).',';
        }
        $code = rtrim($code, ','); //마지막 , 문자 삭제
        $code .= ')';
        return $code;
    } else {
        if (is_string($var)) {
            return "'".str_replace("'","&#039;",$var)."'";
        } elseif (is_bool($var)) {
            return ($var ? 'true' : 'false');
        } else {
            return 'null';
        }
    }
}

function make_dir($path, $permission = 0777) {
	$dir = '';

	if (\lime\is_dir($path)) return $path;
	$dirs=explode(DS, $path);

	$is_create_dir = false;
	foreach($dirs as $i => $value) {
		$dir.= $value.DS;
		if ($is_create_dir == true || (!\lime\is_dir($dir) && $is_create_dir = true)) {
			if(mkdir($dir, $permission)) {
			} else {
				//pr($dir);
				// error
			}
			chmod($dir, $permission);
		} else {
			// exists
		}
	}
	return $dir;
}

function del_dir($path, $php_safe_mode = false) {
	if (!$php_safe_mode) {
		substr(__file__,0,1)==='/'
			? @shell_exec('rm -rf "'.$path.'/"*')
			: @shell_exec('del "'.str_replace('/','\\',$path).'\\*" /s/q');
		return;
	}
	if (!$d = @dir($path)) return;
	while ($f = @$d->read()) {
		switch ($f) {
		case '.': case '..': break;
		default : @\lime\is_dir($f=$path.'/'.$f) ? $this->del_dir($f, 1) : @unlink($f);
		}
	}
}

function dir_scan($dir){
	$var = array();
	if (\lime\is_dir($dir)) {
		if ($dh = opendir($dir)) {
			while (($file = readdir($dh)) !== false) {
				if(filetype($dir . $file) == "dir" && !preg_match('/^\./',$file)){
					$var[] = $file;
				}
			}
			closedir($dh);
		}
	}
	return $var;
}

function _ajax_message($type, $msg, $data = array()) {
	if(is_array($msg)) {
		$data = $msg;
	} else {
		$data = array_merge(array(
			'msg' => $msg
		), $data);
	}
	return array(
		'status' => $type,
		'result' => $data
	);
}

function get_countdown($rem, $pad = false) {
	$day = floor($rem / 86400);
	$hr  = floor(($rem % 86400) / 3600);
	$min = floor(($rem % 3600) / 60);
	$sec = ($rem % 60);

	if($pad == false) {
		return array(
			'day' => $day
			, 'hour' => $hr
			, 'min' => $min
			, 'sec' => $sec
		);	
	}
	return array(
		'day' => $day
		, 'hour' => str_pad($hr, 2, '0', STR_PAD_LEFT)
		, 'min' => str_pad($min, 2, '0', STR_PAD_LEFT)
		, 'sec' => str_pad($sec, 2, '0', STR_PAD_LEFT)
	);
}

function todate($date) {
	return str_replace(array(
		'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'	
	), array(
		'일요일', '월요일', '화요일', '수요일', '목요일', '금요일', '토요일'
	), date('Y년 m월 d일 l', strtotime($date)));
}
function die_error($msg, $data = array()) {
	die_json_enc(_ajax_message('error', $msg, $data));
}

function die_success($msg, $data = array()) {
	die_json_enc(_ajax_message('success', $msg, $data));
}

function die_data($msg, $data = array()) {
	die_json_enc(_ajax_message('data', $msg, $data));
}

function die_valid($msg, $data = array()) {
	die_json_enc(_ajax_message('valid', $msg, $data));
}

function die_json_enc($arr) {
	echo json_enc($arr);
	exit();
}

function json_enc($data) {
	$s = $e = '';
	if(isset($_GET['callback'])) {
		$s = $_GET['callback'].'(';
		$e = ')';
	}
	return $s.json_encode($data).$e;
}

function nf($number, $decimals = 0, $dec_point = '.' , $thousands_sep = ',' ) {
	return number_format($number, $decimals, $dec_point, $thousands_sep);
}

function get_web_page( $url )
{
    $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_HEADER         => false,    // don't return headers
        CURLOPT_FOLLOWLOCATION => true,     // follow redirects
        CURLOPT_ENCODING       => "",       // handle all encodings
        CURLOPT_USERAGENT      => "php", // who am i
        CURLOPT_AUTOREFERER    => true,     // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120,      // timeout on connect
        CURLOPT_TIMEOUT        => 120,      // timeout on response
        CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
    );

    $ch      = curl_init( $url );
    curl_setopt_array( $ch, $options );
    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    return $header['content'] = $content;
    return $header;
}