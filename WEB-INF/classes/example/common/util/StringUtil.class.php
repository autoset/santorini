<?php

namespace example\common\util;

use ReflectionObject;

class StringUtil
{
    /**
     * <pre>
     * 문자열이 비었는지 여부
     * </pre>
     * 
     * @param str 문자열
     * @return boolean
     */
    public static function isEmpty($str)
	{
        return $str == null || strlen($str) == 0;
    }

    /**
     * <pre>
     * 문자열이 배열 내 존재하는지
	 * </pre>
     * 
     * @param str 문자열
	 * @param arr 배열
     * @return boolean
     */
	public static function inArray($str, $arr)
	{
		return in_array($str, $arr);
	}

    /**
     * <pre>
     * 문자열이 숫자인지 판단
	 * </pre>
     * 
     * @param str 문자열
     * @return boolean
     */
	public static function isNumber($str)
	{
		return preg_match('/^[0-9]+$/', $str);
	}

    /**
     * <pre>
     * 문자열이 숫자인지 판단(소숫점 체크)
	 * </pre>
     * 
     * @param str 문자열
     * @return boolean
     */
	public static function isNumber2($str)
	{
		return preg_match('/^([0-9]+)(\.[0-9]+)*$/', $str);
	}

    /**
     * <pre>
     * md5() 해쉬값 리턴
	 * </pre>
     * 
     * @param str 문자열
     * @return string
     */
	public static function makeMd5($str)
	{
		return md5($str);
	}

    /**
     * <pre>
     * 문자열 자르기
	 * </pre>
     * 
     * @param str 문자열
     * @return string
     */
	public static function strCut($data, $index, $offset)
	{
		return mb_substr($data, $index, $offset, 'utf-8');
	}

    /**
     * <pre>
     * json 리턴
	 * </pre>
     * 
     * @param str 문자열
     * @return string
     */
	public static function jsonEncode($data)
	{
		switch ($type = gettype($data))
		{
			case 'NULL':
				return 'null';
			case 'boolean':
				return ($data ? 'true' : 'false');
			case 'integer':
			case 'double':
			case 'float':
				return $data;
			case 'string':
				return '"' . addslashes($data) . '"';
			case 'object':
				//$data = get_object_vars($data);
				$reflection = new ReflectionObject($data);
				$props = $reflection->getProperties();

				$tmpDat = array();
				foreach ($props as $prop)
				{
					$key = substr($prop->getName(),1);
					$method = $reflection->getMethod('get'.$key);
					$value = $method->invoke($data);

					$tmpDat[$key] = $value;
				}
				$data = $tmpDat;

				$tmpDat = null;
				unset($tmpDat);

			case 'array':
				$output_index_count = 0;
				$output_indexed = array();
				$output_associative = array();
				
				foreach ($data as $key => $value)
				{
					$output_indexed[] = StringUtil::jsonEncode($value);
					$output_associative[] = StringUtil::jsonEncode($key) . ':' . StringUtil::jsonEncode($value);
					if ($output_index_count !== NULL && $output_index_count++ !== $key)
					{
						$output_index_count = NULL;
					}
				}
				
				if ($output_index_count !== NULL)
				{
					return '[' . implode(',', $output_indexed) . ']';
				}
				else
				{
					return '{' . implode(',', $output_associative) . '}';
				}
			default:
				return ''; // Not supported
		}
	}
} 

?>