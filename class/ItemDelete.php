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
	protected $arrConfig =  array(
					'title'=> '文章内容',
					'view'=> array(
							'template'=>'ItemDelete.html',
					)
				);
	
	public function process()
	{	
		$sTempXpathTo = $this->params->get('temppath');
		if($sTempXpathTo)
		{
			$sXpathTo = $this->params->get('xpath');
			$arrToXpath = explode('/',$sXpathTo);
			array_pop($arrToXpath);
			$sControllerName = $this->params->get('controllername');
			$sViewPath=$sXpathTo = $this->params->get('viewpath');
			$sMenuId=$sXpathTo = $this->params->get('menuid');
			
			$aSetting = Extension::flyweight('menuediter')->setting();
			
			if($aSetting->hasItem('/menu',$sTempXpathTo.'-'.$sMenuId))
			{
				$akey = $aSetting->key('/menu',true);
				$arrSettingOld = $akey->Item($sTempXpathTo.'-'.$sMenuId);
				$arrSettingNew = $arrSettingOld;
				$this->settingItemdelete($arrSettingNew, $arrToXpath);
				$akey->deleteItem($sTempXpathTo.'-'.$sMenuId);
				$arrSettingNew['id']=$sMenuId;
				$arrSettingNew['class']='menu';
				$akey->setItem($sTempXpathTo.'-'.$sMenuId,$arrSettingNew);
			}
			else{
				$arrSettingOld=array();
				$akey = $aSetting->key('/menu',true);
				$aView = new View($sTempXpathTo);
				$aMenu = $aView->widget($sMenuId);
				//将menu组成字符串在页面显示
				$arrJson = array();
				$arrSetting = array();
				$sXpath='';
			
				//将menu遍历成数组存放在settting
				$this->itemSetting($aMenu->itemIterator(),$arrSettingOld);
				$arrSettingNew = $arrSettingOld;
			
				$this->settingItemdelete($arrSettingNew, $arrToXpath);
				$arrSettingNew['id']=$sMenuId;
				$arrSettingNew['class']='menu';
				$akey->setItem($sTempXpathTo.'-'.$sMenuId,$arrSettingNew);
			}
		}else{
			$sXpathTo = $this->params->get('xpath');
			$arrToXpath = explode('/',$sXpathTo);
			array_pop($arrToXpath);
			$sControllerName = $this->params->get('controllername');
			$sViewPath=$sXpathTo = $this->params->get('viewpath');
			$sMenuId=$sXpathTo = $this->params->get('menuid');
			
			$aSetting = Extension::flyweight('menuediter')->setting();
			
			if($aSetting->hasItem('/menu',$sControllerName.'-'.$sViewPath.'-'.$sMenuId))
			{
				$akey = $aSetting->key('/menu',true);
				$arrSettingOld = $akey->Item($sControllerName.'-'.$sViewPath.'-'.$sMenuId);
				$arrSettingNew = $arrSettingOld;
				$this->settingItemdelete($arrSettingNew, $arrToXpath);
				$akey->deleteItem($sControllerName.'-'.$sViewPath.'-'.$sMenuId);
				$arrSettingNew['id']=$sMenuId;
				$arrSettingNew['class']='menu';
				$akey->setItem($sControllerName.'-'.$sViewPath.'-'.$sMenuId,$arrSettingNew);
			}
			else{
				$arrSettingOld = array();
				$akey = $aSetting->key('/menu',true);
				$aController = new $sControllerName();
				$aView = View::findXPath($aController->mainView(),$sViewPath );
				$aMenu = $aView->widget($sMenuId);
				//将menu组成字符串在页面显示
				$arrJson = array();
				$arrSetting = array();
				$sXpath = '';
				$aMenuIterator = $aMenu->itemIterator();;
			
				//将menu遍历成数组存放在settting
				$this->itemSetting($aMenuIterator,$arrSettingOld);
				$arrSettingNew = $arrSettingOld;
			
				$this->settingItemdelete($arrSettingNew, $arrToXpath);
				$arrSettingNew['id']=$sMenuId;
				$arrSettingNew['class']='menu';
				$akey->setItem($sControllerName.'-'.$sViewPath.'.'.$sMenuId,$arrSettingNew);
			}
		}
		
		$sControllerNamePage = str_replace('\\','.',$sControllerName);
		$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationdelete=locationdelete&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
		$this->view()->createMessage(Message::success,"%s ",$skey='删除成功');
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
			if($aItem->title())
			{	
				$arrItem=$aItem->beanConfig();
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