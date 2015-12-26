<?php

namespace org\autoset\santorini\io;

class PrintWriter
{
	public function __construct()
	{ 
	} 

	public function close()
	{
		exit;
	}

	public function flush()
	{
		@ob_flush();
		@flush();
		@ob_end_flush();
	}

	// 'print'가 PHP 키워드로 등록되어 있어서 메서드명에 부득이 _ 추가 불가
	public function print_($s)
	{
		echo $s;
	}

	public function println($s)
	{
		echo $s.PHP_EOL;
	}

	public function write($s)
	{
		echo $s;
	}

} 

?>