<?php

namespace org\autoset\santorini\vo;

class VirtualFormVO
{
	public function __construct($arr = null)
	{
		if (is_null($arr))
			return ;

		foreach ($arr as $k => $val)
		{
			$arrTmp = explode('_',$val);
			foreach ($arrTmp as $tK=>$tV)
			{
				if ($tK == 0)
					$arrTmp[$tK] = strtoLower($tV);
				else
					$arrTmp[$tK] = strtoupper(substr($tV,0,1)).strtoLower(substr($tV,1));
			}

			$this->{'_'.implode('',$arrTmp)} = null;
		}
	}

	public function __call($name, $arguments)
	{
		if (substr($name,0,3) == 'get')
		{
			$name = lcfirst(substr($name,3));
			return isset($this->{'_'.$name}) ? $this->{'_'.$name} : null;
		}
		elseif (substr($name,0,3) == 'set')
		{
			$name = lcfirst(substr($name,3));
			$this->{'_'.$name} = $arguments[0];
		}
		elseif (substr($name,0,3) == 'has')
		{
			$name = lcfirst(substr($name,3));
			return isset($this->{'_'.$name});
		}
	}
}
