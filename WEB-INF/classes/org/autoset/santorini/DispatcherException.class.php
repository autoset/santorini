<?php


namespace org\autoset\santorini;

use SimpleXMLElement;
use Exception;

use org\autoset\santorini\view\UrlBasedViewResolver;
use org\autoset\santorini\handler\SimpleMappingExceptionResolver;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;

use org\autoset\santorini\util\ModelMap;

class DispatcherException
{ 
	private $_ex;

	private $_beanMap = array(); // BeanFactory
	private $_viewResolverMap = array();
	private $_exceptionMap = array();

	private $_request;
	private $_response;

	public function __construct($param, &$ex)
	{
		if (array_key_exists('contextConfigLocation', $param))
			$this->loadContextConfig($param['contextConfigLocation']);

		$this->_ex = $ex;

		$this->request = new HttpServletRequest;
		$this->response = new HttpServletResponse;

		$findIt = false;

		$model = new ModelMap;
		$model->addAttribute('exception', $ex);

		if (sizeof($this->_exceptionMap) > 0)
		{
			foreach ($this->_exceptionMap as $exObj)
			{
				$exMap = $exObj->getExceptionMappings();

				foreach ($exMap as $exClassName=>$viewTemplate)
				{
					eval("\$tarEx = new \\".$this->getPhpNamespaceByDotNotation($exClassName).';');

					if ($ex instanceof $tarEx)
					{
						foreach ($this->_viewResolverMap as $order=>$viewResolver)
						{
							if ($viewResolver->display($this->request, $this->response, $viewTemplate, $model))
							{
								$findIt = true;
								break 3;
							}
						}
					}
				}
			}
		}

		if (!$findIt)
		{
			if (sizeof($this->_exceptionMap) > 0)
			{
				foreach ($this->_exceptionMap as $exObj)
				{
					$viewTemplate = $exObj->getDefaultErrorView();

					foreach ($this->_viewResolverMap as $order=>$viewResolver)
					{
						if ($viewResolver->display($this->request, $this->response, $viewTemplate, $model))
						{
							$findIt = true;
							break 2;
						}
					}
				}
			}

			if (!$findIt)
				$this->displayError($ex);
		}
	}

	private function displayError($ex)
	{
		echo '<h3>오류 발생</h3>';
		echo $ex->getMessage();
		echo '<hr />';

		foreach ($ex->getTrace() as $trace)
		{
			echo 'file: '.$trace['file'].'<br>';
			echo 'line: '.$trace['line'].'<br>';
		
			if (isset($trace['function']))	
				echo 'function: '.$trace['function'].'<br>';

			if (isset($trace['class']))
				echo 'class: '.$trace['class'].'<br>';
		
			if (isset($trace['type']))
				echo 'type: '.$trace['type'].'<br>';
			echo '<hr>';
		}
	}

	private function loadContextConfig($path)
	{
		if (!file_exists($path))
		{
			$this->displayError(new Exception('컨텍스트 설정 파일이 지정한 위치('.$path.')에 존재하지 않습니다.'));
			return ;
		}

		$xml = new SimpleXMLElement($path, NULL, TRUE);


		$arrBeans = array();
		$arrBean = $xml->bean;

		foreach ($arrBean as $bean)
		{
			$this->praseBeanNode($bean);
		}

	}

	private function praseBeanNode($bean)
	{
		$beanClass = $this->getPhpNamespaceByDotNotation((string)$bean['class']);
		$beanId = isset($bean['id']) ? (string)$bean['id'] : (string)$bean['class'];

		if (!class_exists($beanClass))
		{
			$this->displayError(new Exception('Class `'.$beanClass.'` 로드에 실패했습니다.'));
			return ;
		}

		$this->_beanMap[$beanId] = new $beanClass;

		if (isset($bean->property))
		{
			foreach ($bean->property as $prop)
			{
				if (isset($prop['ref']))
				{
					if (array_key_exists((string)$prop['ref'], $this->_beanMap))
						$this->_beanMap[$beanId]->{(string)$prop['name']} = &$this->_beanMap[(string)$prop['ref']];
					else
					{
						$this->displayError(new Exception($beanId.'에서 참조된 bean ID가 정확하지 않습니다.'));
						return ;
					}
				}
				else
				{
					// NOTE :
					// 이 부분이 아직 쌈박하지 않아요. 
					// 완벽하게 spring과 호환하려는 것은 아니거든요.. 놀리기 있기? 없기?
					if (isset($prop->list) && isset($prop->list->value))
					{
						$arrTmp = array();

						foreach ($prop->list->value as $propValue)
							$arrTmp[] = (string)$propValue;

						$this->_beanMap[$beanId]->{(string)$prop['name']} = $arrTmp;

						$arrTmp = null;
						unset($arrTmp);
					}
					elseif (isset($prop->props) && isset($prop->props->prop))
					{
						$arrTmp = array();

						foreach ($prop->props->prop as $propValue)
							$arrTmp[(string)$propValue['key']] = (string)$propValue;

						$this->_beanMap[$beanId]->{(string)$prop['name']} = $arrTmp;

						$arrTmp = null;
						unset($arrTmp);
					}
					elseif (isset($prop->map))
					{
						$this->_beanMap[$beanId]->{(string)$prop['name']} = $this->getMapArray($prop->map);
					}
					elseif (isset($prop->set))
					{
						$arrTmp = array();

						foreach ($prop->set as $setNode)
						{
							if (is_object($setNode))
							{
								foreach ($setNode as $setKey=>$setValue)
								{
									switch ($setKey)
									{
										case 'map':
											$arrTmp[] = $this->getMapArray($setValue);
											break;
									}
								}
							}
							else
							{
								$arrTmp[] = (string)$setNode;
							}
						}

						$this->_beanMap[$beanId]->{(string)$prop['name']} = $arrTmp;

						$arrTmp = null;
						unset($arrTmp);
					}
					else
					{
						$this->_beanMap[$beanId]->{(string)$prop['name']} = (string)$prop['value'];
					}
				}
			}
		}

		if (method_exists($this->_beanMap[$beanId], 'init'))
			$this->_beanMap[$beanId]->init();

		if ($this->_beanMap[$beanId] instanceof UrlBasedViewResolver)
			$this->_viewResolverMap[$this->_beanMap[$beanId]->getOrder()] = &$this->_beanMap[$beanId];

		if ($this->_beanMap[$beanId] instanceof SimpleMappingExceptionResolver)
			$this->_exceptionMap[] = &$this->_beanMap[$beanId];			
	}

	private function getPhpNamespaceByDotNotation($dotPath)
	{
		return str_replace('.','\\',$dotPath);
	}

	private function getMapArray($mapNode)
	{
		$map = array();

		foreach ($mapNode->entry as $entryNode)
		{
			$map[(string)$entryNode['key']] = (string)$entryNode['value'];
		}
		$arrTmp[] = $map;

		return $map;
	}

}

?>