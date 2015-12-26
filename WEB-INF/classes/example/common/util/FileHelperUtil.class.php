<?php

namespace example\common\util;

class FileHelperUtil
{
    /**
     * <pre>
     * 파일의 확장자를 뺀 이름만 리턴
     * </pre>
     * 
     * @param name 파일명 (예: hello.png)
     * @return 확장자를 뺀 이름 명 (예: hello)
     */
    public static function GetFileName($name)
	{
		$nLastIndexOf = strrpos($name, ".");
		$strFileExt = "";

		if($nLastIndexOf > -1)
			$strFileExt = substr($name, 0, $nLastIndexOf);

		$nLastIndexOf = strrpos($name, "/");

		if($nLastIndexOf > -1)
			$strFileExt = substr($strFileExt, $nLastIndexOf + 1);

		return $strFileExt;
    }

    /**
     * <pre>
     * 파일의 용량 리턴
     * </pre>
     * 
     * @param name 경로
     * @return 
     */
    public static function GetFileSize($path)
	{
		return filesize($path);
    }

    /**
     * <pre>
     * 파일의 확장자
     * </pre>
     * 
     * @param name 파일명 (예: hello.png)
     * @return 확장자 명 (예: .png)
     */
    public static function GetFileExtension($name)
	{
		$nLastIndexOf = strrpos($name, ".");
		$strFileExt = "";

		if($nLastIndexOf > -1)
			$strFileExt = substr($name, $nLastIndexOf);

		return $strFileExt;
    }

    /**
     * <pre>
     * 디렉토리 생성
     * </pre>
     * 
     * @param path 생성할 디렉토리의 경로
     * @return 없음
     */
    public static function MakeDirectory($path)
	{
        if(!file_exists($path))
			@mkdir($path,0777,true);
    }
    

    /**
     * <pre>
     * 파일 삭제
     * </pre>
     * 
     * @param strPath 파일 경로
     * @return true = 성공, false = 실패
     */
    public static function DeleteFile($strPath)
	{
        if(file_exists($strPath))
		{
			if (is_dir($strPath))
			{
				$fileList = FileHelperUtil::GetFileList($strPath);

				foreach ($fileList as $filePath)
				{
					FileHelperUtil::DeleteFile($filePath);
				}

				@rmdir($strPath);
			}
			else
			{
				@unlink($strPath);
	            return !file_exists($strPath);
			}
		}
        else
		{
            return false;
		}
    }

	/**
     * <pre>
     * 파일 복사
     * </pre>
     * 
     * @param strSrc 원본 경로(파일)
     * @param strTar 대상 경로(파일)
     */
	 public static function Copy($strSrc, $strTar)
	{
		if (file_exists($strSrc))
			@copy($strSrc, $strTar);
		else
			return false;
	}

	/**
     * <pre>
     * 파일 이동
     * </pre>
     * 
     * @param strSrc 원본 경로(파일)
     * @param strTar 대상 경로(파일)
     */
	 public static function Move($strSrc, $strTar)
	{
		if (file_exists($strSrc))
			@move_uploaded_file($strSrc, $strTar);
		else
			return false;
	}

    /**
     * <pre>
     * 파일 존재여부
     * </pre>
     * 
     * @param path 파일명
     * @return boolean
     */
    public static function FileExists($path)
	{
		return file_exists($path);
    }

    /**
     * <pre>
     * 파일 목록
     * </pre>
     * 
     * @param path 경로
     * @return array
     */
    public static function GetFileList($path)
	{
		$arr = array();

		if (is_dir($path))
		{
			if ($dh = opendir($path))
			{
				while (($file = readdir($dh)) !== false)
				{
					if ($file == '.' || $file == '..')
						continue;

					$arr[] = $path.'/'.$file;
				}
				closedir($dh);
			}
		}

		return $arr;
    }

    /**
     * <pre>
     * 파일 쓰기
     * </pre>
     * 
     * @param path 파일명
	 * @param str 파일 데이터
     * @return 
     */
    public static function PutFile($path, $dat)
	{
		file_put_contents($path, $dat);
    }

}

?>