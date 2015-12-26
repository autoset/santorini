<?php

namespace example\common\util;

class CommonUtil
{
    /**
     * <pre>
     * 인코딩된 현재 페이지 주소를 리턴
     * </pre>
     * 
     * @param request
     * @return boolean
     */
    public static function getEncodedUrl($req)
	{
        return urlencode($req->getRequestURI());//.'?'.(isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''));
    }

    /**
     * <pre>
     * 파일 용량 표기 포맷터
     * </pre>
     * 
     * @param size
     * @return string
	*/
	public static function readableFileSize($size)
	{ 
		if($size <= 0)
			return "0"; 

		$units = array( "B", "KB", "MB", "GB", "TB" );

		$digitGroups = (int)(log10($size)/log10(1024)); 
		
		return sprintf("%.2f", $size/pow(1024, $digitGroups)) . $units[$digitGroups]; 
	}

    /**
     * <pre>
     * 초 단위 시간의 HH:MM:SS 표기 포맷터
     * </pre>
     * 
     * @param seconds
     * @return string
	*/
	public static function readableTimeFormat($seconds) 
	{ 
		$hours = floor($seconds / 3600); 
		$remainder = floor( $seconds - $hours * 3600); 
		$mins = floor( $remainder / 60 ); 
		$remainder = $remainder - $mins * 60; 
		$secs = $remainder; 

		return str_pad($hours, 2, "0", STR_PAD_LEFT).":".str_pad($mins, 2, "0", STR_PAD_LEFT).":".str_pad($secs, 2, "0", STR_PAD_LEFT); 
	}


} 

?>