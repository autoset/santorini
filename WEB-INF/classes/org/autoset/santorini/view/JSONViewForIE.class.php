<?php

namespace org\autoset\santorini\view;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;

use org\autoset\santorini\util\ModelMap;

use org\autoset\santorini\JSONString;


class JSONViewForIE extends JSONView
{
	// 이 클래스는 JSONView 에서 알아서 처리하기 전에 작성된 코드를 위해 남겨둡니다.

	public function sendResponseHeader($req, $res)
	{
		$res->setContentType('text/plain; charset=utf-8');
		$res->setHeader("Content-Disposition", "inline;filename=result.txt");
	}

}
