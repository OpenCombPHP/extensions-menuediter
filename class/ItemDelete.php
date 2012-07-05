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
		$sXpathTo = $this->params->get('xpath');
		$arrToXpath = explode('/',$sXpathTo);
		$arrToXpathFirst = $arrToXpath;
		array_pop($arrToXpath);
		$sLastKey = array_pop($arrToXpath);
		$sFirstKey = array_shift($arrToXpathFirst);
		
		if($sFirstKey == 'item:system' and $sLastKey == 'item:menuediter')
		{
			$this->createMessage(Message::error, "%s ",$skey="无法删除系统菜单");
			$sUrl = "?c=org.opencomb.menuediter.MenuOpen";
			$this->location($sUrl,5);
		}elseif($sXpathTo == "item:system/")
		{
			$this->createMessage(Message::error, "%s ",$skey="无法删除系统菜单");
			$sUrl = "?c=org.opencomb.menuediter.MenuOpen";
			$this->location($sUrl,5);
		}
		$sTempXpathTo = $this->params->get('temppath');
		if($sTempXpathTo)
		{
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

				//将menu遍历成数组存放在settting
				$this->itemSetting($aMenu->itemIterator(),$arrSettingOld);
				$arrSettingNew = $arrSettingOld;

				$this->settingItemdelete($arrSettingNew, $arrToXpath);//exit;//var_dump($arrToXpath);exit;
				$arrSettingNew['id']=$sMenuId;
				$arrSettingNew['class']='menu';
				$akey->setItem($sTempXpathTo.'-'.$sMenuId,$arrSettingNew);
			}
			$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationdelete=locationdelete&temppath=$sTempXpathTo&menuid=$sMenuId";
			
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
				$aView = View::findXPath($aController->view(),$sViewPath );
				$aMenu = $aView->widget($sMenuId);
			
				//将menu遍历成数组存放在settting
				$this->itemSetting($aMenu->itemIterator(),$arrSettingOld);
				$arrSettingNew = $arrSettingOld;
			
				$this->settingItemdelete($arrSettingNew, $arrToXpath);
				$arrSettingNew['id']=$sMenuId;
				$arrSettingNew['class']='menu';
				$akey->setItem($sControllerName.'-'.$sViewPath.'.'.$sMenuId,$arrSettingNew);
			}
			
			$sControllerNamePage = str_replace('\\','.',$sControllerName);
			$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationdelete=locationdelete&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
		}

		$this->createMessage(Message::success,"%s ",$skey='删除成功');
		$this->location($sUrl,0);
		
	}
	
	public function settingItemDelete(&$arrSettingNew,$arrXpathTarget)
	{
		$sLastKey = array_pop($arrXpathTarget) ;
		$arrCurrentKey =& $arrSettingNew ;
		
		foreach($arrXpathTarget as $sKey)
		{
			$arrCurrentKey =& $arrCurrentKey[$sKey] ;
		}
		unset($arrCurrentKey[$sLastKey]) ;
	}
	
	public function itemSetting($aMenuIterator,&$arrSetting)
	{	
		$arrI=&$arrSetting;
		foreach($aMenuIterator as $key=>$aItem)
		{
			if($aItem->id())
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