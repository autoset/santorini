<?php

define('DIR_ROOT',	dirname(__FILE__));
define('DIR_CLASSES',	'WEB-INF/classes');
define('DIR_LIB',	'WEB-INF/lib');
define('DB_TYPE',	'mysql');

$arrWebConfig = array(
			'contextConfigLocation'	=> 'WEB-INF/config/common-servlet.xml',
			'url-pattern'		=> array('*'),
			'alwaysReload'		=> !file_exists('WEB-INF/config/common-servlet.xml.contextComponentSacn.obj')
		);

// 시간대
date_default_timezone_set('Asia/Seoul');

// 예외처리 핸들러
set_exception_handler('exception_handler');

// 실행 시간 제약 설정
set_time_limit(0);

ini_set("memory_limit", "1024M");


// PCRE 셋팅 (템플릿 버퍼)
ini_set("pcre.recursion_limit", 1024*1024*1024 );
ini_set("pcre.backtrack_limit", 1024*1024*1024 );

function exception_handler($ex)
{
	new org\autoset\santorini\DispatcherException($GLOBALS['arrWebConfig'], $ex);
}

function &getClassNewInstance(&$obj)
{	
	$oAop = new org\autoset\santorini\handler\AOPHandler($obj);
	return $oAop;
}

function santoriniAutoLoad($className)
{
	$arrClasses = array(
				DIR_CLASSES.'/'.str_replace('\\','/', $className).'.class.php',
				DIR_CLASSES.'/'.str_replace('\\','/', $className).'.php'
			);

	foreach ($arrClasses as $classPath)
	{
		if (file_exists($classPath))
		{
			include_once($classPath);
			return ;
		}
	}

	if ($GLOBALS['__enabledAutoloadException'])
	{
		$ex = new Exception($classPath.' 위치에 '.$className.' 클래스가 존재하지 않습니다.');
		new \org\autoset\santorini\DispatcherException($GLOBALS['arrWebConfig'], $ex);
	}
}

function enabledAutoloadException($enabled = true)
{
	$GLOBALS['__enabledAutoloadException'] = $enabled;
}

function __main()
{
	enabledAutoloadException(true);

	$dispatcher = new org\autoset\santorini\DispatcherServlet($GLOBALS['arrWebConfig']);
}

function convFieldName($s)
{
	$arr = explode('_',strtolower($s));
	$arr = array_map('ucfirst', $arr);
	return implode('',$arr);
}

function dump($arr,$bExit=true)
{
	if (@sizeof($arr))
	{
		echo "<!-- dump -->\n\n";
		echo "<style>.fixedsys{font-family:fixedsys}</style><div class='fixedsys' style='color:blue'>".$_SERVER['PHP_SELF']."?".$_SERVER['QUERY_STRING']."</div>";
		echo genDumpVars($arr);

		if ($bExit) exit;
	}
}

function genDumpVars($arr,$nDepth = 0) 
{
	$bgColor = array("white","#efefef","#FFE8CF","#FFF5CF","#F0FFCF","#CFFFE9","#CFF5FF","#849FEE","#B684EE","#EE84A4","#EEAE84","#EEEA84","#BDEE84","#84EEB3");
	$strDebug = "<table border='0' cellpadding='2' cellspacing='0' cellpaddding='0'>";
	if (is_string($arr))
		$strDebug .= "<tr><td align='left' class='fixedsys'>string (".strlen($arr).") <xmp class='fixedsys' style='display:inline'>'".$arr."'</xmp></td></tr>";
	else if (is_int($arr))
		$strDebug .= "<tr><td align='left' class='fixedsys'>int (".intval($arr).") </td></tr>";
	else if (is_float($arr))
		$strDebug .= "<tr><td align='left' class='fixedsys'>float (".floatval($arr).") </td></tr>";
	else if (is_bool($arr))
		$strDebug .= "<tr><td align='left' class='fixedsys'>bool (".($arr?"TRUE":"FALSE").")</td></tr>";
	elseif (is_null($arr))
		$strDebug .= "<tr><td align='left' class='fixedsys'><font class='fixedsys' color='red'>NULL</font></td></tr>";
	else if (is_array($arr) || is_object($arr))
	{
		$strDebug .= "<tr><td><table border='0' cellpadding='2' cellspacing='0'>";
		
		while (list($k, $v) = each($arr))
		{
			$strDebug .= "<tr><td class='fixedsys' align='left' bgcolor='".$bgColor[$nDepth]."'>".$k."</td><td bgcolor='".$bgColor[$nDepth]."'> => </td><td bgcolor='".$bgColor[$nDepth]."'>";
			$strDebug .= genDumpVars($v,($nDepth+1));
			$strDebug .= "</td></tr>";
		}
		$strDebug .= "</table></td></tr>";
	}
	else
	{
		$strDebug .= "<tr><td align='left' class='fixedsys'><font class='fixedsys' color='green'>WHAT IS THIS?</font></td></tr>";
	}
	$strDebug .= "</table>";
	return $strDebug;
}

spl_autoload_register('santoriniAutoLoad');

// 실행 시간 정보 - 타이머 시작
/*
$STARTTIME = (double)time() + (double)microtime();
$STARTMEMORY = memory_get_usage();
*/
__main();
/*
$EXECTIME = (double)time() + (double)microtime();
$EXECMEMORY = memory_get_usage();
$time_server = $EXECTIME - $STARTTIME;
$memory_usage = $EXECMEMORY - $STARTMEMORY;

//echo '<!-- 서버 처리 : '.$time_server.'초 / '.($memory_usage/1024).'K바이트 -->';
*/

