<?php
/**
* @file Controller.php
* @synopsis  顶级控制器
* @author Yee, <rlk002@gmail.com>
* @version 1.0
* @date 2016-07-12 20:21:56
*/

abstract class App_Controller extends Zend_Controller_Action
{
	public $smarty;

	public function init()
	{
		parent::init();
		$this->_initsmarty();
	}

	protected function _initsmarty()
	{
		require_once("App/Smarty.php");
		$this->smarty = new Ysmarty();
	}

	public function preDispatch()
	{
		parent::preDispatch();
		$controllerName = $this->getRequest()->getControllerName();
		$actionName = $this->getRequest()->getActionName();
		Zend_Registry::set('controllerName', $controllerName);
		Zend_Registry::set('actionName', $actionName);
		$this->_checkAccess();
	}

	protected function _checkAccess()
	{
		$controllerName = Zend_Registry::get('controllerName');
		$actionName = Zend_Registry::get('actionName');
	}

	public function postDispatch()
	{
		$controllerName = Zend_Registry::get('controllerName');
		$actionName = Zend_Registry::get('actionName');
		parent::postDispatch();
		try
		{
			$this->smarty->display($controllerName . '_' . $actionName . '.html');
		}catch(Exception $e)
		{
			require_once 'Zend/Log/Exception.php';
			throw new Zend_Log_Exception($e->getMessage());
		}
	}
}
