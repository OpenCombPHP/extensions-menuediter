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
		MenuEditer::getHistory();
	}
	
	static public function buildControlPanelMenu3(array & $arrConfig,$sNamespace,$aFactory,$arrSettigBean)
	{
		$arrConfig=$arrSettigBean;
		var_dump($arrConfig);
	}
	
	static public function buildControlPanelMenu(array & $arrConfig)
	{
		$arrConfig['item:system']['item:platform-manage']['item:menuediter'] = array(
				'title'=>'菜单编辑' ,
				'link' => '?c=org.opencomb.menuediter.MenuOpen' ,
				'query' => 'c=org.opencomb.menuediter.MenuOpen' ,
				);
	}
	
// 	static public function buildControlPanelMenu2(array & $arrConfig, $a,$b,$c)
// 	{
// 		$aSetting = Extension::flyweight('menuediter')->setting();
// 		$akey=$aSetting->key('/menu/'.'org\opencomb\coresystem\mvc\controller\ControlPanelFrame',true);
// 		$arrConfig=$akey->Item('frameView'.'.'.'mainMenu');
// 	}
	
	static public function getHistory()
	{
		$aSetting = Extension::flyweight('menuediter')->setting();
		foreach($aSetting->keyIterator('/menu') as $key=>$akey)
		{
			var_dump($akey);
			foreach($akey->itemIterator() as $key1=>$item)
			{	
				//var_dump($item);
				$arrItem=explode('.',$item);
				Menu::registerBuildHandle(
						$akey->name()
						, "$arrItem[0]"
						, "$arrItem[1]"
						, array(__CLASS__,'buildControlPanelMenu3')
						, array($akey->item($item,array()))
				) ;
			}
		}
	}
}