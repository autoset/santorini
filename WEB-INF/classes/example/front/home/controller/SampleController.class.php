<?php

namespace example\front\home\controller;

use org\autoset\santorini\Controller;

use org\autoset\santorini\util\ModelMap;
use org\autoset\santorini\ModelAndView;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;

class SampleController extends Controller
{

	/**
	 * @RequestMapping(value='/sample/index')
	 */
	public function index(HttpServletRequest $req, HttpServletResponse $res)
	{
		// �Ķ���� ȹ��
		$email = $this->getRequestParam('email');

		// ��� email �� ����
		$this->model = new ModelMap();
		$this->model->addAttribute('email', $email);

		return "sample/index";
	}

	/**
	 * @RequestMapping(value='/sample/index.json')
	 */
	public function indexJson(HttpServletRequest $req, HttpServletResponse $res)
	{
		// �Ķ���� ȹ��
		$email = $this->getRequestParam('email');

		// ��� email �� ����
		$model = new ModelMap();
		$model->addAttribute('email', $email);

		return new ModelAndView('JSONView', $model);
	}
}