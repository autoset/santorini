<?php


namespace org\autoset\santorini\view;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;

use org\autoset\santorini\util\ModelMap;

use org\autoset\santorini\JSONString;

use org\autoset\santorini\vo\VirtualFormVO;

class JSONView
{
	public function display(HttpServletRequest &$req, HttpServletResponse &$res, ModelMap $model = null)
	{
		$arr = $model->getAttributes();

		// cross domain
		$res->setHeader('Access-Control','allow <*>');
		$res->setHeader('Access-Control-Allow-Origin','*');
		$res->setHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
		$res->setHeader('Access-Control-Allow-Credentials', 'true');

		$this->sendResponseHeader($req, $res);

		$res->getWriter()->print_(json_encode($arr, JSON_UNESCAPED_UNICODE));
		//$res->getWriter()->print_($this->jsonEncode($arr));

		return true;
	}

	public function sendResponseHeader($req, $res)
	{
		// IE 8 이하를 위해 content-type을 text/plain 으로 내려보냄으로써 다운로드 창을 안띄움
		if (strpos($req->getHeader('USER-AGENT'), 'MSIE') !== false || strpos($req->getHeader('USER-AGENT'), 'Trident') !== false)
		{
			$res->setContentType('text/plain; charset=utf-8');
			$res->setHeader("Content-Disposition", "inline;filename=result.txt");
		}
		else
		{
			$res->setContentType('application/json; charset=utf-8');
		}
	}

	public function jsonEncode(&$arr)
	{		
		//convmap since 0x80 char codes so it takes all multibyte codes (above ASCII 127). So such characters are being "hidden" from normal json_encoding
		array_walk_recursive($arr, function (&$item, $key)
		{
			if (is_string($item))
				$item = mb_encode_numericentity($item, array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
			elseif (is_object($item))
			{
				$reflection = new \ReflectionObject($item);
				$props = $reflection->getProperties();

				$tmp = array();
				foreach ($props as $prop)
				{
					$name = substr($prop->getName(),1);
					$value = '';

					try
					{
						$method = $reflection->getMethod('get'.ucfirst($name));				
						$value = $method->invoke($item);
					}
					catch (\Exception $ex)
					{
						if ($reflection->name == 'org\autoset\santorini\vo\VirtualFormVO')
						{
							$value = $item->__call('get'.ucfirst($name), null);
						}
					}

					if ($value instanceof JSONString)
						$value = $value->toString();
					elseif (is_string($value))
						$value = mb_encode_numericentity($value, array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
					elseif (is_array($value))
						$value = json_decode($this->jsonEncode($value));
					elseif (is_object($value))
						$value = $this->jsonEncode($value);

					$tmp[$name] = $value;
				}

				$item = $tmp;
			}
		});


		return mb_decode_numericentity(json_encode($arr), array (0x80, 0xffff, 0, 0xffff), 'UTF-8');
	}


}

