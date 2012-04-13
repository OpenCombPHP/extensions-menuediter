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
		
// 		Menu::registerBuildHandle(
// 				'org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame'
// 				, 'frameView'
// 				, 'mainMenu'
// 				, array(__CLASS__,'buildControlPanelMenu3')
// 		) ;
		
		Menu::registerBuildHandle(
				'org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame'
				, 'frameView'
				, 'mainMenu'
				, array(__CLASS__,'buildControlPanelMenu2')
		) ;
		//MenuEditer::getHistory();
		
		
	}
	
	static public function buildControlPanelMenu3(array & $arrConfig)
	{
		$aSetting = Extension::flyweight('menuediter')->setting();
		$akey=$aSetting->key('/menu/'.'org\opencomb\menuediter\TestMenuOpen',true);
		$arrConfig=$akey->Item('testMenuOpenView'.'.'.'mainMenu');
	}
	
	static public function buildControlPanelMenu(array & $arrConfig)
	{
		$arrConfig['item:system']['item:platform-manage']['item:menuediter'] = array(
				'title'=>'菜单编辑' ,
				'link' => '?c=org.opencomb.menuediter.MenuOpen' ,
				'query' => 'c=org.opencomb.menuediter.MenuOpen' ,
				);
	}
	
	static public function buildControlPanelMenu2(array & $arrConfig)
	{
		$aSetting = Extension::flyweight('menuediter')->setting();
		$akey=$aSetting->key('/menu/'.'org\opencomb\coresystem\mvc\controller\ControlPanelFrame',true);
		$arrConfig=$akey->Item('frameView'.'.'.'mainMenu');
		//var_dump($arrConfig);
// 		$arrConfig['item:system']['item:platform-manage']['item:menuediter'] = array(
// 				'title'=>'菜单编辑' ,
// 				'link' => '?c=org.opencomb.menuediter.MenuOpen' ,
// 				'query' => 'c=org.opencomb.menuediter.MenuOpen' ,
// 		);
	}
	
	static public function getHistory()
	{
		$aSetting = Extension::flyweight('menuediter')->setting();
		$arrHistory=array();
		$i=0;
		foreach($aSetting->keyIterator('/menu') as $key=>$akey)
		{
			$i=$i+1;
			var_dump($akey->name());
			foreach($akey->itemIterator() as $key1=>$item)
			{
				$arrItem=explode('.',$item);
				Menu::registerBuildHandle(
						$akey->name()
						, '$arrItem[0]'
						, '$arrItem[0]'
						, array(__CLASS__,'buildControlPanelMenu2')
				) ;
			//$item->name();
				//var_dump($akey->item($item,array()));
				var_dump($item);
				$arrHistory[$i++]=$akey->item($item,array());
			}
		}
		
		//$this->viewMenuOpen->variables()->set('sHistory',$sHistory);
	}
}