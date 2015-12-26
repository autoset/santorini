<?php

namespace org\autoset\santorini\view;

use SimpleXMLElement;
use Exception;
use ReflectionClass;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;

use org\autoset\santorini\ApplicationContext;


class TilesView
{
	private $_definitionMap = null;

	private function loadTilesConfiguration()
	{
		$tilesConfigurer = ApplicationContext::getBean('tilesConfigurer');

		$arrDefinitionsPath = $tilesConfigurer->getDefinitions();

		foreach ($arrDefinitionsPath as $definitionPath)
		{
			if (!file_exists(DIR_ROOT.'/'.$definitionPath))
				throw new Exception('Tiles 정의 파일이 지정된 위치에 존재하지 않습니다.');

			$xml = new SimpleXMLElement(DIR_ROOT.'/'.$definitionPath, NULL, TRUE);

			if (!isset($xml->definition))
			{
				$xml = null;
				unset($xml);
				continue;
			}

			foreach ($xml->definition as $definition)
			{
				$name = (string)$definition['name'];

				$attributes = array();

				if (isset($definition->{'put-attribute'}))
				{
					foreach ($definition->{'put-attribute'} as $putAttribute)
					{
						$attributes[(string)$putAttribute['name']] = array(
							'type'	=> isset($putAttribute['type']) ? (string)$putAttribute['type'] : 'template',
							'value'	=> (string)$putAttribute['value']
						);
					}
				}

				$this->_definitionMap[$name] = array(
					'template'	=> isset($definition['template']) ? (string)$definition['template'] : null,
					'extends'	=> isset($definition['extends']) ? (string)$definition['extends'] : null,
					'attribute'	=> $attributes
				);

				$attributes = null;
				unset($attributes);
			}
		}
	}

	public function display(HttpServletRequest &$req, HttpServletResponse &$res, &$viewName, $model = null)
	{
		$this->loadTilesConfiguration();

		if (!array_key_exists($viewName, $this->_definitionMap))
			return false;

		$definition = $this->_definitionMap[$viewName];

		$template = $definition['template'];
		$attributes = array();

		if (!is_null($definition['extends']) && isset($this->_definitionMap[$definition['extends']]['attribute']))
		{
			$template = $this->_definitionMap[$definition['extends']]['template'];

			foreach ($this->_definitionMap[$definition['extends']]['attribute'] as $attrName=>$attrValue)
				$attributes[$attrName] = $attrValue;
		}

		if (isset($definition['attribute']))
		{
			foreach ($definition['attribute'] as $attrName=>$attrValue)
				$attributes[$attrName] = $attrValue;
		}

		foreach ($attributes as $attrName => $attrValue)
		{
			if ($attrValue['type'] != 'template')
				continue;

			ob_start();
			include(DIR_ROOT.'/'.$attrValue['value']);
			$out = ob_get_contents();
			ob_end_clean();

			$attributes[$attrName]['template'] = $out;

			$out = null;
			unset($out);
		}

		ob_start();
		include(DIR_ROOT.'/'.$template);
		$out = ob_get_contents();
		ob_end_clean();

		if (preg_match_all("#<tiles:insertAttribute\sname=['\"](.*?)['\"]\s*/>#iU", $out, $m))
		{
			foreach ($m[0] as $idx=>$value)
			{
				if ($attributes[$m[1][$idx]]['type'] == 'template')
					$out = str_replace($value, $attributes[$m[1][$idx]]['template'], $out);
				else
					$out = str_replace($value, $attributes[$m[1][$idx]]['value'], $out);
			}
		}

		// 태그라이브러리를 찾아 처리합니다.
		if (preg_match_all("#<%@\staglib\sprefix=['\"](.*?)['\"]\suri=['\"](.*?)['\"]\s*%>#iU", $out, $m))
		{
			$arrTagLib = array();
			foreach ($m[0] as $idx=>$value)
			{
				$prefix = $m[1][$idx];
				$uri = $m[2][$idx];

				if (!file_exists(DIR_ROOT.'/'.$uri))
					throw new Exception($prefix.'의 TabLibrary 파일('.DIR_ROOT.'/'.$uri.')을 찾을 수 없습니다.');
				else
					$uri = DIR_ROOT.'/'.$uri;

				// tld XML 읽기
				$xml = new SimpleXMLElement($uri, NULL, TRUE);
				
				if (isset($xml->tag))
				{
					foreach ($xml->tag as $tagNode)
					{
						$tagName = (string)$tagNode->name;
						
						$arrTagLib[$prefix][$tagName] = array(
							'tag-class'	=> isset($tagNode->{'tag-class'}) ? (string)$tagNode->{'tag-class'} : null/*,
							'attribute'	=> array()*/
						);

						// NOTE: 원래는 XML에서 속성을 읽어서 처리해야 하겠지만. 편의상 XML에 있는 속성을 기준으로 setter호출하도록 함.
						/*
						if (isset($tagNode->attribute))
						{
							foreach ($tagNode->attribute as $attributeNode)
							{
								$arrTagLib[$prefix][$tagName]['attribute'][] = array(
									'name'			=> (string)$attributeNode->name,
									'required'		=> ((string)$attributeNode->required) == 'true',
									'rtexprvalue'	=> ((string)$attributeNode->rtexprvalue) == 'true'
								);
							}
						}
						*/
					}
				}

				$out = str_replace($value, '', $out);
			}

			// 버퍼에서 태그 찾기
			foreach ($arrTagLib as $prefix => $tabList)
			{
				foreach ($tabList as $tagName => $tagInfo)
				{
					//$out = str_replace(array("\$"), array(chr(1)), $out);

					if (preg_match_all("#<".$prefix.":".$tagName."\s*(.*)/\s*>|<".$prefix.":".$tagName."\s*(.*)>(.*)</".$prefix.":".$tagName.">#iUs", $out, $m))
					{
						foreach ($m[0] as $idx => $value)
						{
							$rc = new ReflectionClass(str_replace('.','\\',$tagInfo['tag-class']));
							$tagObj = $rc->newInstance();

							$tagBodyMethod = $rc->getMethod('setTagBody');
							$tagBodyMethod->invoke($tagObj, $m[2][$idx]);

							if (preg_match_all("#(.*)=['\"](.*)['\"]#iUs", $m[1][$idx], $attr) ||
								preg_match_all("#(.*)=['\"](.*)['\"]#iUs", $m[2][$idx], $attr) ||
								preg_match_all("#(.*)=['\"](.*)['\"]#iUs", $m[3][$idx], $attr))
							{
								foreach ($attr[1] as $attrIdx=>$attrValue)
								{
									$_method = $rc->getMethod('set'.trim($attr[1][$attrIdx]));
									$propVal = trim($attr[2][$attrIdx]);

									if (preg_match("#[$]{(.*)}#U", $propVal, $propM))
										$propVal = $model ? $model->get($propM[1]) : '';

									$_method->invoke($tagObj, $propVal);
								}
							}

							$_method = $rc->getMethod('getParsedContext');
							$out = str_replace($value, $_method->invoke($tagObj), $out);
						}

					}
					
					//$out = str_replace(array(chr(1)), array("\$"), $out);
				}
			}

		}
		
		// EL을 처리합니다.
		if (preg_match_all("#[$]{(.*)}#U", $out, $m))
		{
			foreach ($m[0] as $idx=>$value)
			{
				$out = str_replace($value, $model->get($m[1][$idx]), $out);
			}
		}

		$res->setContentType("text/html; charset=utf-8");
		$res->getWriter()->println($out);

		return true;
	}

} 

?>