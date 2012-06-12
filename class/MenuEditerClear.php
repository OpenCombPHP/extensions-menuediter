<?php
namespace org\opencomb\menuediter;
use org\jecat\framework\lang\Object;
use org\jecat\framework\message\Message;
use org\jecat\framework\lang\oop\ClassLoader;
use org\jecat\framework\mvc\view\widget\menu\Menu;
use org\jecat\framework\mvc\view\View;
use org\jecat\framework\mvc\controller\Controller;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\platform\ext\Extension;
use org\jecat\framework\bean\BeanFactory;
use org\opencomb\frameworktest\aspect;
use org\opencomb\platform\system\PlatformSerializer;
use org\jecat\framework\ui\xhtml\weave\Patch;
use org\jecat\framework\ui\xhtml\weave\WeaveManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\opencomb\coresystem\mvc\controller\ControlPanelFrame;

class MenuEditerClear extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=> '文章内容',
			'view:menuDelete'=>array(
				'template'=>'MenuEditerClear.html',
				'class'=>'form',
			),
		);
	}
	
	public function process()
	{	
		$sControllerName=$this->params->get('controllername');
		$sViewPath=$this->params->get('viewpath');
		$sMenuId=$this->params->get('menuid');
		
		$aSetting = Extension::flyweight('menuediter')->setting();
		$akey=$aSetting->key('/menu/'.$sControllerName,true);
		$akey->deleteItem($sViewPath.'.'.$sMenuId);
		$skey="清楚菜单成功";
		$this->viewMenuDelete->createMessage(Message::success,"%s ",$skey);
		$this->location('?c=org.opencomb.menuediter.MenuOpen');
	}
	
	public function settingItemdelete(&$arrSettingNew,$arrXpathTarget)
	{
		foreach($arrSettingNew as $key=>&$item)
		{
			for($i=0;$i<count($arrXpathTarget);$i++)
			{
				if($key==$arrXpathTarget[$i])
				{
					if($i==count($arrXpathTarget)-1)
					{
						unset($arrSettingNew[$key]);
					}
					else {
						$this->settingItemdelete($arrSettingNew[$key],$arrXpathTarget);
					}
				}
			}
		}
	}
	
	public function itemSetting($aMenuIterator,&$arrSettingOld)
	{
		$arrI=&$arrSettingOld;
		foreach($aMenuIterator as $key=>$aItem)
		{	
			if($aItem->title())
			{	
				$aItem->title();
				$arrI=&$arrSettingOld['item:'.$key];
				$arrI=array('xpath'=>'item:'.$aItem->id(),'title'=>$aItem->title(),'depth'=>$aItem->depth(),'link'=>$aItem->link(),'menu'=>$aItem->subMenu()?1:0,'active'=>$aItem->isActive());
				$arrI=&$arrSettingOld;
			}
			if($aItem->subMenu())
			{
				$arrI=&$arrI['item:'.$key];
				$this->itemSetting($aItem->subMenu()->itemIterator(),$arrI);
			}
		}
	}
}

?>