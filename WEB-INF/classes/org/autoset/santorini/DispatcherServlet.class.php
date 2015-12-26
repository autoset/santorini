<?php

namespace org\autoset\santorini;

use Exception;
use SimpleXMLElement;

use Reflection;
use ReflectionClass;
use ReflectionMethod;

use org\autoset\santorini\io\FileSystem;
use org\autoset\santorini\annotation\AnnotationsMatcher;
use org\autoset\santorini\view\UrlBasedViewResolver;

use org\autoset\santorini\handler\HandlerInterceptorAdapter;
use org\autoset\santorini\handler\InterceptorHandler;
use org\autoset\santorini\handler\SessionHandler;

use org\autoset\santorini\http\HttpServletRequest;
use org\autoset\santorini\http\HttpServletResponse;

class DispatcherServlet
{ 
	private $_contextConfigLocation = null;
	private $_bReload = false;

	private $_contextComponentScan = null;
	private $_annotationMap = array();
	private $_urlPattern = null;
	private $_requestURI = null;

	private $_beanMap = array(); // BeanFactory
	private $_viewResolverMap = array();
	private $_interceptorMap = array();

	private $_calledMethodName = null;
	private $_pathVars = array();

	public function __construct($param)
	{
		if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] == 'reload')
			$this->_bReload = true;

		if (array_key_exists('alwaysReload', $param) && $param['alwaysReload'])
			$this->_bReload = true;

		if (array_key_exists('contextConfigLocation', $param))
			$this->loadContextConfig($param['contextConfigLocation']);

		if (array_key_exists('url-pattern', $param))
		{
			if (is_string($param['url-pattern']))
				$param['url-pattern'] = array($param['url-pattern']);

			foreach ($param['url-pattern'] as $urlIdx => $urlPattern)
			{
				$param['url-pattern'][$urlIdx] = str_replace(array('*','.'), array('(.*?)','\\.'), $urlPattern);
			}

			$this->_urlPattern = implode('|',$param['url-pattern']);
		}

		$this->_requestURI = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';

		if (!preg_match("#{$this->_urlPattern}$#iU", $this->_requestURI))
			throw new Exception('서블릿에서 지원하는 URI가 아닙니다.');


		$this->doContextComponentScan();

		$this->setSessionHandler();

		$this->doBeanMapping();
	}

	private function loadContextConfig($path)
	{
		if (!file_exists($path))
			throw new Exception('컨텍스트 설정 파일이 지정한 위치('.$path.')에 존재하지 않습니다.');

		$this->_contextConfigLocation = $path;

		$xml = new SimpleXMLElement($this->_contextConfigLocation, NULL, TRUE);

		$arrBasePackage = array();
		$arrComponentScan = $xml->xpath('//context:component-scan');

		foreach ($arrComponentScan as $componentScan)
			$arrBasePackage[] = (string)$componentScan['base-package'];

		$arrComponentScan = null;
		unset($arrComponentScan);

		$arrBeans = array();
		$arrBean = $xml->bean;

		foreach ($arrBean as $bean)
		{
			$this->praseBeanNode($bean);
		}

		$arrBean = null;
		unset($arrBean);

		$arrInterceptors = $xml->xpath('//mvc:interceptors');

		if (sizeof($arrInterceptors) > 0 && isset($arrInterceptors[0]->bean))
		{
			foreach ($arrInterceptors[0]->bean as $bean)
			{
				$this->praseBeanNode($bean);
			}
		}

		$arrInterceptors = null;
		unset($arrInterceptors);

		ksort($this->_viewResolverMap);

		ApplicationContext::setBeanFactory($this->_beanMap);

		$this->_contextComponentScan = &$arrBasePackage;
	}

	private function praseBeanNode($bean)
	{
		$beanClass = $this->getPhpNamespaceByDotNotation((string)$bean['class']);
		$beanId = isset($bean['id']) ? (string)$bean['id'] : (string)$bean['class'];
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
						throw new Exception($beanId.'에서 참조된 bean ID가 정확하지 않습니다.');
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

		if ($this->_beanMap[$beanId] instanceof UrlBasedViewResolver)
			$this->_viewResolverMap[$this->_beanMap[$beanId]->getOrder()] = &$this->_beanMap[$beanId];

		if ($this->_beanMap[$beanId] instanceof HandlerInterceptorAdapter)
			$this->_interceptorMap[] = &$this->_beanMap[$beanId];
		
			
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

	private function doContextComponentScan()
	{
		foreach ($this->_contextComponentScan as $basePackage)
		{
			// 컨트롤러만 스캔합니다.
			if (!$this->_bReload && file_exists($this->_contextConfigLocation.'.contextComponentSacn.obj'))
			{
				$arrClasses = unserialize(file_get_contents($this->_contextConfigLocation.'.contextComponentSacn.obj'));
			}
			else
			{
				$arrClasses = FileSystem::globr(DIR_CLASSES."/".str_replace('.','/',$basePackage),"*Controller.class.php");
				file_put_contents($this->_contextConfigLocation.'.contextComponentSacn.obj', serialize($arrClasses));
			}

			$this->parseAnnotation($arrClasses);


			$arrClasses = null;
			unset($arrClasses);
		}
	}

	private function getNamespaceByClassPath($classPath)
	{
		$namespace = substr($classPath,strlen(DIR_CLASSES)+1);
		$namespace = str_replace('/','\\',substr($namespace,0,strrpos($namespace,'.class.php')));

		return $namespace;
	}

	private function getPhpNamespaceByDotNotation($dotPath)
	{
		return str_replace('.','\\',$dotPath);
	}

	private function parseAnnotation(&$arrClasses)
	{
		if (!$this->_bReload && file_exists($this->_contextConfigLocation.'.parseAnnotation.obj'))
		{
			$this->_annotationMap = unserialize(file_get_contents($this->_contextConfigLocation.'.parseAnnotation.obj'));

		}
		else
		{
			foreach ($arrClasses as $classPath)
			{
				include_once($classPath);
				
				$namespace = $this->getNamespaceByClassPath($classPath);

				$rc = new ReflectionClass($namespace);
				$arrMethods = $rc->getMethods(-1);

				$arrAnnotations = array();

				foreach ($arrMethods as $rm)
				{
					$methodName = $rm->getName();
					$arrAnnotations[$methodName] = array();
					
					$annotations = array();
					$parser = new AnnotationsMatcher;
					$parser->matches($rm->getDocComment(), $annotations);

					foreach ($annotations as $className => $properties)
					{
						// NOTE: 흠냐.. 여기 좀 싫으네.. 일단은.. 어노테이션은 framework 아래에 두도록 합시다.
						$className2 = 'org\\autoset\\santorini\\annotation\\'.$className;

						$arrAnnotations[$methodName][$className] = new $className2;

						foreach ($properties as $values)
						{
							foreach ($values as $k => $v)
								$arrAnnotations[$methodName][$className]->{$k} = $v;
						}
					}

					$parser = null;
					unset($parser);
				}

				$rc = null;
				$arrMethods = null;

				unset($rc);
				unset($arrMethods);

				$this->_annotationMap[$classPath] = $arrAnnotations;

				$arrAnnotations = null;
				unset($arrAnnotations);
			}

			file_put_contents($this->_contextConfigLocation.'.parseAnnotation.obj', serialize($this->_annotationMap));
		}
	}

	private function setSessionHandler()
	{
		$oSessionHandler = new SessionHandler;

		session_set_save_handler(array(&$oSessionHandler, 'open'),
								 array(&$oSessionHandler, 'close'),
								 array(&$oSessionHandler, 'read'),
								 array(&$oSessionHandler, 'write'),
								 array(&$oSessionHandler, 'destroy'),
								 array(&$oSessionHandler, 'gc')
								 );

		register_shutdown_function('session_write_close');
	}

	private function doBeanMapping()
	{
		$oCurClass = false;

		foreach ($this->_annotationMap as $classPath => $arrAnnotations)
		{
			foreach ($arrAnnotations as $methodName => $arrAnnotationClasses)
			{
				foreach ($arrAnnotationClasses as $className => $classObj)
				{
					if ($className != 'RequestMapping')
						continue;

					$matches = array();

					if ($classObj->value == $this->_requestURI ||
						preg_match('#^'.$classObj->value.'$#', $this->_requestURI, $matches))
					{
						if (sizeof($matches) > 0)
							array_shift($matches);

						$oCurClass = array(
							'className'		=> $this->getNamespaceByClassPath($classPath),
							'methodName'	=> $methodName,
							'annotation'	=> $arrAnnotationClasses,
							'pathVars'		=> $matches
						);
						
						break 3;
					}
				}
			}
		}

		if (!$oCurClass)
			throw new Exception('요청 주소('.$this->_requestURI.')를 처리할 수 있는 매핑기를 찾을 수 없습니다.');

		$this->_annotationMap = null;
		
		
		$this->createController($oCurClass);
	}

	private function createController($classInfo)
	{
		$rc = new ReflectionClass($classInfo['className']);
		$rc->setStaticPropertyValue('__parent__', $this);

		$this->_calledMethodName = $classInfo['methodName'];
		$this->_pathVars = $classInfo['pathVars'];

		$request = new HttpServletRequest;
		$response = new HttpServletResponse;
		$handle = new InterceptorHandler($classInfo);

		// 인터셉터의 전처리
		if (sizeof($this->_interceptorMap) > 0)
		{
			foreach ($this->_interceptorMap as $interceptorBean)
			{
				if (method_exists($interceptorBean, 'preHandle'))
				{
					if ($interceptorBean->preHandle($request, $response, $handle) === false)
						return ;
				}
			}
		}

		$oController = $rc->newInstance();

		// 인터셉터 말단처리(view 처리 후)
		if (sizeof($this->_interceptorMap) > 0)
		{
			foreach ($this->_interceptorMap as $interceptorBean)
			{
				if (method_exists($interceptorBean, 'afterCompletion'))
					$interceptorBean->afterCompletion($request, $response, $handle);
			}
		}
	}

	public function getCalledMethodName()
	{
		return $this->_calledMethodName;
	}

	public function getViewResolverMap()
	{
		return $this->_viewResolverMap;
	}

	public function getInterceptorMap()
	{
		return $this->_interceptorMap;
	}

	public function getPathVar($itemIdx)
	{
		return isset($this->_pathVars[$itemIdx]) ? $this->_pathVars[$itemIdx] : null;
	}

} 

?>