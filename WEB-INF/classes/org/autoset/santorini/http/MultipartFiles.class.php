<?php

namespace org\autoset\santorini\http;

class MultipartFiles
{
	static public function getData(&$fileData)
	{
		$arrDat = array();

		if (is_array($fileData['name']))
		{
			foreach ($fileData['name'] as $idx => &$fileName)
			{
				$arrTmp = array(
					'name'		=> $fileName,
					'type'		=> $fileData['type'][$idx],
					'tmp_name'	=> $fileData['tmp_name'][$idx],
					'error'		=> $fileData['error'][$idx],
					'size'		=> $fileData['size'][$idx]
				);

				$arrDat[] = new MultipartFile($arrTmp);
			}
		}
		else
		{
			$arrDat[] = new MultipartFile($fileData);
		}

		return $arrDat;
	}
}
