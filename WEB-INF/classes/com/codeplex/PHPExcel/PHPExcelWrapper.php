<?php

namespace com\codeplex\PHPExcel;

class PHPExcelWrapper
{
	static public function getPHPExcel()
	{
		include_once(DIR_CLASSES.'/com/codeplex/PHPExcel/PHPExcel.php');

		return new \PHPExcel;
	}

	static public function createWriter($oExcelDoc, $format = "Excel5")
	{
		include_once(DIR_CLASSES.'/com/codeplex/PHPExcel/PHPExcel.php');

		return \PHPExcel_IOFactory::createWriter($oExcelDoc, $format);
	}

	static public function load($path)
	{
		include_once(DIR_CLASSES.'/com/codeplex/PHPExcel/PHPExcel.php');

		return \PHPExcel_IOFactory::load($path);
	}

	
}
