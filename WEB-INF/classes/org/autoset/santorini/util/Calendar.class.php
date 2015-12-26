<?php

namespace org\autoset\santorini\util;

class Calendar
{
	const YEAR = 'Y';
	const MONTH = 'm';
	const DAY_OF_MONTH = 'd';
	const HOUR = 'h';
	const AM_PM = 'AM_PM';
	const AM = 'AM';
	const PM = 'PM';
	const HOUR_OF_DAY = 'H';
	const MINUTE = 'i';
	const SECOND = 's';
	const MILLISECOND = 'u'; // microsecond

	static public function getInstance()
	{
		//date_default_timezone_set('Asia/Seoul');
		return new Calendar;
	}

	public function get($const)
	{
		if ($const == self::MONTH)
			return date($const) - 1;
		elseif ($const == self::AM_PM)
			return date('A') == 'AM' ? self::AM : self::PM;
		else
			return date($const);
	}

} 

?>