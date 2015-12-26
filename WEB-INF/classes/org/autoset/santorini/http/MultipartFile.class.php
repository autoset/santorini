<?php

namespace org\autoset\santorini\http;

class MultipartFile
{
	private $_file;
	
	public function __construct($file)
	{
		$this->_file = $file;
	}

	public function getOriginalFilename()
	{
		return $this->_file['name'];
	}

	public function getSize()
	{
		return $this->_file['size'];
	}

	public function getContentType()
	{
		return $this->_file['type'];
	}

	public function isEmpty()
	{
		return $this->_file['error'] != UPLOAD_ERR_OK || $this->getSize() < 1;
	}

	public function getInputStream()
	{
		// PHP������ ��θ� ��ȯ�ϴ� ������ ó��! ��Ʈ�� �׵��� �����!
		return $this->_file['tmp_name'];
	}

	public function transferTo($target)
	{
		if (is_uploaded_file($this->_file['tmp_name']))
		{
			return move_uploaded_file($this->_file['tmp_name'], $target);
		}
		else
		{
			copy($this->_file['tmp_name'], $target);
			unlink($this->_file['tmp_name']);

			return true;
		}
	}
}
