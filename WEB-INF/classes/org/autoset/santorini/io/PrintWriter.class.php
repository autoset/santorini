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

	// 'print'�� PHP Ű����� ��ϵǾ� �־ �޼���� �ε��� _ �߰� �Ұ�
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