<?php

namespace example\front\home\service\impl;

use example\front\home\service\HomeService;

use example\front\home\dao\HomeDAO;

use org\autoset\santorini\vo\VirtualFormVO;

class HomeServiceImpl implements HomeService
{
	/** HomeDAO */
	private $_homeDAO = null;

	public function __construct()
	{
		$this->_homeDAO = new HomeDAO;
	}


}
