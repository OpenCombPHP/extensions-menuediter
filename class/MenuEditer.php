<?php
namespace org\opencomb\menuediter;

use org\opencomb\platform\mvc\view\widget\Menu;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\platform\ext\Extension;
use org\jecat\framework\bean\BeanFactory;
use org\opencomb\frameworktest\aspect;
use org\opencomb\platform\system\PlatformSerializer;
use org\jecat\framework\ui\xhtml\weave\Patch;
use org\jecat\framework\ui\xhtml\weave\WeaveManager;

class MenuEditer extends Extension
{
	public function load()
	{
		// 注册菜单build事件的处理函数
		Menu::registerBuildHandle(
			'org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame'
			, 'frameView'
			, 'mainMenu'
			, array(__CLASS__,'buildControlPanelMenu')
		) ;
	}
	
	static public function buildControlPanelMenu(array & $arrConfig)
	{
		$arrConfig['item:system']['item:platform-manage']['item:menuediter'] = array(
				'title'=>'菜单编辑' ,
				'link' => '?c=org.opencomb.menuediter.MenuOpen' ,
				'query' => 'c=org.opencomb.menuediter.MenuOpen' ,
				);
	}
}