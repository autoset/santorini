<?php

namespace example\front\home\controller;

use Exception;
use example\common\exception\InvalidParameterException;

use org\autoset\santorini\Controller;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;
use org\autoset\santorini\http\HttpSession;
use org\autoset\santorini\util\ModelMap;
use org\autoset\santorini\ApplicationContext;

use example\front\home\service\impl\HomeServiceImpl;

use example\common\util\UserHelperUtil;
use example\common\util\StringUtil;
use example\common\util\PaginationInfo;

use example\common\vo\UserVO;
use org\autoset\santorini\vo\SessionVO;
use org\autoset\santorini\vo\VirtualFormVO;

class HomeController extends Controller
{
	/** sysPropService */
	private $_sysPropService = null;

	/** HomeService */
	private $_homeService = null;

	/**
     * <pre>
     * 컨트롤러 초기화
     * </pre>
	*/
	function init()
	{
		$this->_sysPropService = ApplicationContext::getBean("sysPropService");

		$this->_homeService = getClassNewInstance(new HomeServiceImpl);

		parent::init();
	}

	/**
     * <pre>
     * 홈
	 * </pre>
     * 
     * @param req HttpServletRequest
     * @param res HttpServletResponse
     * @return String 페이지이동
	 * @PagePolicy(requiredLogin=false)
	 * @RequestMapping(value='/home')
	*/
	public function home(HttpServletRequest $req, HttpServletResponse $res)
	{
		return "front/home";
	}

}

