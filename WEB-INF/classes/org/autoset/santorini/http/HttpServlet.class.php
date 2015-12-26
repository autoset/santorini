<?php

namespace org\autoset\santorini\http;


class HttpServlet
{
	static public $__parent__ = null;

	public function __construct()
	{
		$this->init();

		$req	= new HttpServletRequest;
		$resp	= new HttpServletResponse;

		$this->service($req, $resp);

		$this->destroy();
	}

	/*
     * <pre>
     * 초기화
     * </pre>
     * 
     * @return void
	*/
	public function init()
	{
	}

	/*
     * <pre>
     * 언로드 시점
     * </pre>
     * 
     * @return void
	*/
	public function destroy()
	{
	}

	/*
     * <pre>
     * 클라이언트 요청 종류에 따라 클래스에 정의되어 있는 적당한 doXXX() 호출
     * </pre>
     * 
     * @param request HttpServletRequest
     * @param response HttpServletResponse
     * @return void
	*/
	public function service(HttpServletRequest &$req, HttpServletResponse &$resp)
	{
		switch ($req->getMethod())
		{
			case 'DELETE':
				$this->doDelete($req, $resp);
				break;
			case 'GET':
				$this->doGet($req, $resp);
				break;
			case 'OPTIONS':
				$this->doOptions($req, $resp);
				break;
			case 'POST':
				$this->doPost($req, $resp);
				break;
			case 'PUT':
				$this->doPut($req, $resp);
				break;
			case 'TRACE':
				$this->doTrace($req, $resp);
				break;
		}
	}

	/*
     * <pre>
     * HTTP DELETE 요청 처리
     * </pre>
     * 
     * @param request HttpServletRequest
     * @param response HttpServletResponse
     * @return void
	*/
	public function doDelete(HttpServletRequest $req, HttpServletResponse $resp)
	{

	}

	/*
     * <pre>
     * HTTP GET 요청 처리
     * </pre>
     * 
     * @param request HttpServletRequest
     * @param response HttpServletResponse
     * @return void
	*/
	public function doGet(HttpServletRequest $req, HttpServletResponse $resp)
	{

	}

	/*
     * <pre>
     * HTTP OPTIONS 요청 처리
     * </pre>
     * 
     * @param request HttpServletRequest
     * @param response HttpServletResponse
     * @return void
	*/
	public function doOptions(HttpServletRequest $req, HttpServletResponse $resp)
	{

	}

	/*
     * <pre>
     * HTTP POST 요청 처리
     * </pre>
     * 
     * @param request HttpServletRequest
     * @param response HttpServletResponse
     * @return void
	*/
	public function doPost(HttpServletRequest $req, HttpServletResponse $resp)
	{

	}

	/*
     * <pre>
     * HTTP PUT 요청 처리
     * </pre>
     * 
     * @param request HttpServletRequest
     * @param response HttpServletResponse
     * @return void
	*/
	public function doPut(HttpServletRequest $req, HttpServletResponse $resp)
	{

	}

	/*
     * <pre>
     * HTTP Trace 요청 처리
     * </pre>
     * 
     * @param request HttpServletRequest
     * @param response HttpServletResponse
     * @return void
	*/
	public function doTrace(HttpServletRequest $req, HttpServletResponse $resp)
	{

	}

	public function &getContext()
	{
		return ApplicationContext::getInstance();
	}
}


?>