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

class ItemDelete extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=> '文章内容',
			'view:itemDelete'=>array(
				'template'=>'ItemDelete.html',
				'class'=>'form',
			),
		);
	}
	
	public function process()
	{	
		$sXpathTo=$this->params->get('xpath');
		$arrToXpath=explode('/',$sXpathTo);
		array_pop($arrToXpath);
		$sControllerName=$sXpathTo=$this->params->get('controllername');
		$sViewPath=$sXpathTo=$this->params->get('viewpath');
		$sMenuId=$sXpathTo=$this->params->get('menuid');
		
		$aSetting = Extension::flyweight('menuediter')->setting();
		
		if($aSetting->hasItem('/menu/'.$sControllerName,$sViewPath.'.'.$sMenuId))
		{
			$akey=$aSetting->key('/menu/'.$sControllerName,true);
			$arrSettingOld=$akey->Item($sViewPath.'.'.$sMenuId);
			$arrSettingNew=$arrSettingOld;
			$this->settingItemdelete($arrSettingNew, $arrToXpath);
			$akey->deleteItem($sViewPath.'.'.$sMenuId);
			$arrSettingNew['id']=$sMenuId;
			$arrSettingNew['class']='menu';
			$akey->setItem($sViewPath.'.'.$sMenuId,$arrSettingNew);
		}
		else{
			$arrSettingOld=array();
			$akey=$aSetting->key('/menu/'.$sControllerName,true);
			$aController = new $sControllerName();
			$aView = View::findXPath($aController->mainView(),$sViewPath );
			$aMenu=$aView->widget($sMenuId);
			//将menu组成字符串在页面显示
			$arrJson=array();
			$arrSetting=array();
			$sXpath='';
			$aMenuIterator=$aMenu->itemIterator();;
		
			//将menu遍历成数组存放在settting
			$this->itemSetting($aMenuIterator,$arrSettingOld);
			$arrSettingNew=$arrSettingOld;

			$this->settingItemdelete($arrSettingNew, $arrToXpath);
			$arrSettingNew['id']=$sMenuId;
			$arrSettingNew['class']='menu';
			$akey->setItem($sViewPath.'.'.$sMenuId,$arrSettingNew);
		}
		
		$sControllerNamePage=str_replace('\\','.',$sControllerName);
		$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationdelete=locationdelete&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
		$this->viewItemDelete->createMessage(Message::success,"%s ",$skey='删除成功');
		$this->location($sUrl,0);
		
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
	
	public function itemSetting($aMenuIterator,&$arrSetting)
	{	
		$arrI=&$arrSetting;
		foreach($aMenuIterator as $key=>$aItem)
		{
			//var_dump($aItem->beanConfig()('query'));exit;
			//$arrItem=$aItem->beanConfig();
			//echo $arrItem['query']."</br>";exit;
			if($aItem->title())
			{	
// 				echo "<pre>";
// 				//var_dump($aItem->beanConfig());
// 				echo "</pre>";
				$arrItem=$aItem->beanConfig();
			//	var_dump($arrItem['query']);echo "</br>";
				//var_dump($arrItem['query']);echo "</br>";
				$sQuery=isset($arrItem['query'])?$arrItem['query']:'';
				$arrI=&$arrSetting['item:'.$key];
				$arrI=array('xpath'=>'item:'.$aItem->id(),'title'=>$aItem->title(),'link'=>$aItem->link(),'menu'=>$aItem->subMenu()?1:0,'query'=>$sQuery);
				$arrI=&$arrSetting;
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