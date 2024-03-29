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
		MenuEditer::getNewMenu();
	}
	
	static public function buildNewControlPanelMenu(array & $arrConfig,$sNamespace,$aFactory,$arrSettigBean)
	{
		$arrConfig=$arrSettigBean;
	}
	
	static public function buildControlPanelMenu(array & $arrConfig)
	{
		$arrConfig['item:system']['item:platform-manage']['item:menuediter'] = array(
				'title'=>'菜单编辑' ,
				'link' => '?c=org.opencomb.menuediter.MenuOpen' ,
				'query' => array(
						'c=org.opencomb.menuediter.MenuOpen'
					   ,'c=org.opencomb.menuediter.ItemDelete'
					   ,'c=org.opencomb.menuediter.ItemSort'
	  				   ,'c=org.opencomb.menuediter.MenuEditerClear'
						
				)
		);
	}	
	
	static public function getNewMenu()
	{
		$aSetting = Extension::flyweight('menuediter')->setting();
		foreach($aSetting->keyIterator('/menu') as $key=>$akey)
		{	
			foreach($akey->itemIterator() as $key1=>$item)
			{	
				$arrItem=explode('.',$item);
				Menu::registerBuildHandle(
						$akey->name()
						, "$arrItem[0]"
						, "$arrItem[1]"
						, array(__CLASS__,'buildNewControlPanelMenu')
						, array($akey->item($item,array()))
				) ;
			}
		}
	}
}