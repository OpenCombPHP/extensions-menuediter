<?php
/*
 * 功能:菜单项目在本层上的向上向下的移动
 * 参数:
 * 		sXpathTo 被移动的菜单项目路径
 * 		arrXpath 移动的选项下拉列表数组
 * 		arrSettingChild 被移动的菜单项目
 * 		
 * 
 */
namespace org\opencomb\menuediter;
use org\jecat\framework\lang\Object;
use org\jecat\framework\message\Message;
use org\jecat\framework\lang\oop\ClassLoader;
use org\jecat\framework\mvc\view\widget\menu\Menu;
use org\jecat\framework\mvc\view\View;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\platform\ext\Extension;
use org\jecat\framework\bean\BeanFactory;
use org\opencomb\frameworktest\aspect;
use org\opencomb\platform\system\PlatformSerializer;
use org\jecat\framework\ui\xhtml\weave\Patch;
use org\jecat\framework\ui\xhtml\weave\WeaveManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\opencomb\coresystem\mvc\controller\ControlPanelFrame;
use org\jecat\framework\mvc\controller\Controller;

class ItemSort extends ControlPanel
{
	protected $arrConfig = array(
			'title'=> '文章内容',
			'view' => array(
					'template'=>'ItemSort.html',
			)
	);
	
	public function process()
	{	
		$sItem_go = $this->params->get('item_go');
		$sXpathTo = $this->params->get('xpath');
		$arrToXpath = explode('/',$sXpathTo);
		array_pop($arrToXpath);
		$aSetting = Extension::flyweight('menuediter')->setting();
		$sMenuId = $this->params->get('menuid');
		if($this->params->get('controllername'))
		{
			$sControllerName = $this->params->get('controllername');
			$sViewPath = $this->params->get('viewpath');
			
			$aController = new $sControllerName();
			$aController->frame();
			$aView = $aController->view()->findXPath($aController->view(),$sViewPath);
			$sTempPath = $aView->template();
			
			if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
			{
				$i = 0;
				$arrSort = array();
				$arrXpath = array();
				$arrSettingMiddle = array();
				$arrSettingChild = array();
				$arrItemSettingNew = array();
				$sXpath = '';
				$akey = $aSetting->key('/menu',true);
				$arrSettingOld = $akey->Item($sControllerName.'-'.$sViewPath.'-'.$sMenuId);
				$this->xpathOption($arrSettingOld,$sXpath,$i,$arrXpath);			
	
				$iNubmerTo = array_search($sXpathTo, $arrXpath);
				$sXpathUp = '';
				
				if($sItem_go == 'up')
				{
					$iNubmerCons = 0;
					if(count(explode('/',$sXpathTo))>2)
					{	
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-2;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
						$iNubmerCons = array_search($sTemp, $arrXpath);
					}
					
					//得到向下移动目标的路径
					if($this->getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath))
					{
						$sXpathUp = $this->getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath);
					}else{
 						$skey = "只能在同级移动";
 						$this->view->createMessage(Message::error,"%s ",$skey);
 						$sControllerNamePage = str_replace('\\','.',$sControllerName);
 						$sUrl = "?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
 						$this->location($sUrl,0);
 						return;
					};
	
					$arrXpathUp = explode('/',$sXpathUp);
					array_pop($arrXpathUp);
					$arrXpathTo = explode('/',$sXpathTo);
					array_pop($arrXpathTo);
					
					$arrSettingNew = $arrSettingOld;
					//原菜单中删除被移动的菜单项目
					$this->settingItemdelete($arrSettingNew, $arrXpathUp);
					//编辑被移动的菜单项目
					$this->itemSettingEdit($arrSettingOld,$sXpath,$sXpathUp,$arrSettingMiddle,$arrSettingChild);
					//编辑新菜单，并将被移动的菜单项目插入菜单中
					$this->settingEditXpathOption1(0,$arrSettingNew,$arrXpathTo,$arrItemSettingNew,$arrSettingChild);
				}elseif($sItem_go=='down'){
					$iNubmerCons = 0;
					//判断移动范围
					if(count(explode('/',$sXpathTo))>2)
					{
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-2;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
						for($i=$iNubmerTo+1; $i<count($arrXpath); $i++)
						{
							if(stristr($arrXpath[$i],$sTemp))
							{
								$iNubmerCons++;
							}
						}
					}else{
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-1;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
						for($i=$iNubmerTo; $i<count($arrXpath); $i++)
						{
							if(stristr($arrXpath[$i],$sTemp))
							{
								$iNubmerCons++;
							}
						}
					}
					
					//得到向上移动目标的路径
					if($this->getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath)){
							$sXpathUp = $this->getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath);
					}else{
	 					$skey = "只能在同级移动";
	 					$this->view->createMessage(Message::error,"%s ",$skey);
	 					$sControllerNamePage = str_replace('\\','.',$sControllerName);
	 					$sUrl = "?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
	 					$this->location($sUrl,0);
	 					return;
					};
							
					$arrXpathUp = explode('/',$sXpathUp);
					//删除数组的最后一个元素
					array_pop($arrXpathUp);
					$arrXpathTo = explode('/',$sXpathTo);
					array_pop($arrXpathTo);
					
					$arrSettingNew = $arrSettingOld;
					$this->settingItemdelete($arrSettingNew, $arrXpathTo);
					$this->itemSettingEdit($arrSettingOld,$sXpath,$sXpathTo,$arrSettingMiddle,$arrSettingChild);
					$this->settingEditXpathOption1(0,$arrSettingNew,$arrXpathUp,$arrItemSettingNew,$arrSettingChild);	
				}
				
				$akey->deleteItem($sTempPath.'.'.$sMenuId);
				$arrItemSettingNew['id'] = $sMenuId;
				$arrItemSettingNew['class'] = 'menu';
				$akey->setItem($sTempPath.'.'.$sMenuId,$arrItemSettingNew);
				$sControllerNamePage = str_replace('\\','.',$sControllerName);
				$sUrl = "?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
				$this->view->createMessage(Message::success,"%s ",$skey='移动成功');
				$this->location($sUrl,0);
			}else{
				$i=0;
				$arrXpath = array();
				$arrSettingMiddle = array();
				$arrSettingChild = array();
				$arrItemSettingNew = array();
				$arrSettingOld = array();
				$akey = $aSetting->key('/menu',true);
				$aController = new $sControllerName();
				$aController->frame();
				$aView = $aController->view()->findXPath($aController->view(),$sViewPath );
				$aMenu = $aView->widget($sMenuId);
				//将menu组成字符串在页面显示
				$arrJson = array();
				$arrSetting = array();
				$sXpath='';
				$aMenuIterator = $aMenu->itemIterator();
			
				//将menu遍历成数组存放在settting
				$this->itemSetting($aMenuIterator,$arrSettingOld);
				$arrSettingNew = $arrSettingOld;
				
				$this->xpathOption($arrSettingOld,$sXpath,$i,$arrXpath);
				$iNubmerTo = array_search($sXpathTo, $arrXpath);
				
					if($sItem_go == 'up')
					{
						$iNubmerCons = 0;
						if(count(explode('/',$sXpathTo))>2)
						{
							$arrTemp = explode('/',$sXpathTo);
							$sTemp = "";
							for($i=0;$i<count($arrTemp)-2;$i++)
							{
								$sTemp = $sTemp.$arrTemp[$i].'/';
							}
							$iNubmerCons = array_search($sTemp, $arrXpath);
						}
						
						
						if($this->getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath)){
							$sXpathUp = $this->getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath);
						}else {
		 						$skey = "只能在同级移动";
		 						$this->view->createMessage(Message::error,"%s ",$skey);
		 						
		 						$sControllerNamePage = str_replace('\\','.',$sControllerName);
		 						$sUrl = "?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
		 						$this->location($sUrl,0);
		 						
		 						//return;
						};
						
						$arrXpathUp=explode('/',$sXpathUp);
						array_pop($arrXpathUp);
						$arrXpathTo=explode('/',$sXpathTo);
						array_pop($arrXpathTo);
						
						$arrSettingNew=$arrSettingOld;
						$this->settingItemdelete($arrSettingNew, $arrXpathUp);
						$this->itemSettingEdit($arrSettingOld,$sXpath,$sXpathUp,$arrSettingMiddle,$arrSettingChild);
						$this->settingEditXpathOption1(0,$arrSettingNew,$arrXpathTo,$arrItemSettingNew,$arrSettingChild);
					}elseif($sItem_go=='down'){
						$iNubmerCons = 0;
						if(count(explode('/',$sXpathTo))>2)
						{
							$arrTemp = explode('/',$sXpathTo);
							$sTemp = "";
							for($i=0;$i<count($arrTemp)-2;$i++)
							{
								$sTemp = $sTemp.$arrTemp[$i].'/';
							}
							for($i=$iNubmerTo+1; $i<count($arrXpath); $i++)
							{
								if(stristr($arrXpath[$i],$sTemp))
								{
									$iNubmerCons++;
								}
							}
							
						}else{
							$arrTemp = explode('/',$sXpathTo);
							$sTemp = "";
							for($i=0;$i<count($arrTemp)-1;$i++)
							{
								$sTemp = $sTemp.$arrTemp[$i].'/';
							}
							for($i=$iNubmerTo; $i<count($arrXpath); $i++)
							{
								if(stristr($arrXpath[$i],$sTemp))
								{
									$iNubmerCons++;
								}
							}
						}
						
						if($this->getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath)){
							$sXpathUp=$this->getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath);
						}else {
	 						$skey="只能在同级移动";
	 						$this->view->createMessage(Message::error,"%s ",$skey);
	
	 						$sControllerNamePage=str_replace('\\','.',$sControllerName);
	 						$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
	 						$this->location($sUrl,0);
	 						
	 						//return;
						};		
						$arrXpathUp=explode('/',$sXpathUp);
						array_pop($arrXpathUp);
						$arrXpathTo=explode('/',$sXpathTo);
						array_pop($arrXpathTo);
						
						$arrSettingNew=$arrSettingOld;
						$this->settingItemdelete($arrSettingNew, $arrXpathTo);
						$this->itemSettingEdit($arrSettingOld,$sXpath,$sXpathTo,$arrSettingMiddle,$arrSettingChild);
						$this->settingEditXpathOption1(0,$arrSettingNew,$arrXpathUp,$arrItemSettingNew,$arrSettingChild);	
					}
				
					$akey->deleteItem($sTempPath.'.'.$sMenuId);
					$arrItemSettingNew['id']=$sMenuId;
					$arrItemSettingNew['class']='menu';
					$akey->setItem($sTempPath.'.'.$sMenuId,$arrItemSettingNew);
					$sControllerNamePage=str_replace('\\','.',$sControllerName);
					$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
					$this->view->createMessage(Message::success,"%s ",$skey='移动成功');
					$this->location($sUrl,0);
			}
		}else{
			$sTempPath = $this->params->get('temppath');
			if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
			{
				$i=0;
				$arrSort = array();
				$arrXpath = array();
				$arrSettingMiddle = array();
				$arrSettingChild = array();
				$arrItemSettingNew = array();
				$sXpath = '';
				$akey = $aSetting->key('/menu',true);
				$arrSettingOld = $akey->Item($sTempPath.'-'.$sMenuId);
				$this->xpathOption($arrSettingOld,$sXpath,$i,$arrXpath);
			
				$iNubmerTo = array_search($sXpathTo, $arrXpath);
				$sXpathUp = '';
			
				if($sItem_go == 'up')
				{
					$iNubmerCons = 0;
					if(count(explode('/',$sXpathTo))>2)
					{
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-2;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
						$iNubmerCons = array_search($sTemp, $arrXpath);
					}
					if($this->getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath))
					{
						$sXpathUp = $this->getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath);
					}else{
						$skey = "只能在同级移动";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$sUrl = "?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&temppath=$sTempPath&menuid=$sMenuId";
						$this->location($sUrl,0);
						return;
					};
					$arrXpathUp = explode('/',$sXpathUp);
					array_pop($arrXpathUp);
					$arrXpathTo = explode('/',$sXpathTo);
					array_pop($arrXpathTo);
		
					$arrSettingNew = $arrSettingOld;
					$this->settingItemdelete($arrSettingNew, $arrXpathUp);
					$this->itemSettingEdit($arrSettingOld,$sXpath,$sXpathUp,$arrSettingMiddle,$arrSettingChild);
					$this->settingEditXpathOption1(0,$arrSettingNew,$arrXpathTo,$arrItemSettingNew,$arrSettingChild);
				}elseif($sItem_go=='down'){
			
					$iNubmerCons = 0;
					if(count(explode('/',$sXpathTo))>2)
					{
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-2;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
						for($i=$iNubmerTo+1; $i<count($arrXpath); $i++)
						{
							if(stristr($arrXpath[$i],$sTemp))
							{
								$iNubmerCons++;
							}
						}
					}else{
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-1;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
						for($i=$iNubmerTo; $i<count($arrXpath); $i++)
						{
							if(stristr($arrXpath[$i],$sTemp))
							{
								$iNubmerCons++;
							}
						}
					}
					if($this->getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath)){
						$sXpathUp = $this->getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath);
					}else {
						$skey = "只能在同级移动";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$sUrl = "?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&temppath=$sTempPath&menuid=$sMenuId";
						$this->location($sUrl,0);
						return;
					};
					$arrXpathUp = explode('/',$sXpathUp);
					array_pop($arrXpathUp);
					$arrXpathTo = explode('/',$sXpathTo);
					array_pop($arrXpathTo);
		
					$arrSettingNew = $arrSettingOld;
					$this->settingItemdelete($arrSettingNew, $arrXpathTo);
					$this->itemSettingEdit($arrSettingOld,$sXpath,$sXpathTo,$arrSettingMiddle,$arrSettingChild);
					$this->settingEditXpathOption1(0,$arrSettingNew,$arrXpathUp,$arrItemSettingNew,$arrSettingChild);
				}
				$akey->deleteItem($sTempPath.'-'.$sMenuId);
				$arrItemSettingNew['id'] = $sMenuId;
				$arrItemSettingNew['class'] = 'menu';
				$akey->setItem($sTempPath.'-'.$sMenuId,$arrItemSettingNew);
				$sUrl = "?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&temppath=$sTempPath&menuid=$sMenuId";
				$this->view->createMessage(Message::success,"%s ",$skey='移动成功');
				$this->location($sUrl,0);
			}else{
				$i = 0;
				$arrXpath = array();
				$arrSettingMiddle = array();
				$arrSettingChild = array();
				$arrItemSettingNew = array();
				$arrSettingOld = array();
				$akey = $aSetting->key('/menu',true);
				$aView = new View($sTempPath);
				$aMenu = $aView->widget($sMenuId);
				//将menu组成字符串在页面显示
				$arrJson = array();
				$arrSetting = array();
				$sXpath = '';
	
				//将menu遍历成数组存放在settting
				$this->itemSetting($aMenu->itemIterator(),$arrSettingOld);
				$arrSettingNew = $arrSettingOld;
	
				$this->xpathOption($arrSettingOld,$sXpath,$i,$arrXpath);
				$iNubmerTo = array_search($sXpathTo, $arrXpath);
	
				if($sItem_go == 'up')
				{	
					$iNubmerCons = 0;
					if(count(explode('/',$sXpathTo))>2)
					{
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-2;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
							$iNubmerCons = array_search($sTemp, $arrXpath);
					}
		
					if($this->getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath))
					{
						$sXpathUp=$this->getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath);
					}else{
						$skey="只能在同级移动";
						$this->view()->createMessage(Message::error,"%s ",$skey);
						$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&temppath=$sTempPath&menuid=$sMenuId";
						//$this->location($sUrl,5);
						return;
					};
					$arrXpathUp = explode('/',$sXpathUp);
					array_pop($arrXpathUp);
					$arrXpathTo = explode('/',$sXpathTo);
					array_pop($arrXpathTo);
		
					$arrSettingNew = $arrSettingOld;
					$this->settingItemdelete($arrSettingNew, $arrXpathUp);
					$this->itemSettingEdit($arrSettingOld,$sXpath,$sXpathUp,$arrSettingMiddle,$arrSettingChild);
					$this->settingEditXpathOption1(0,$arrSettingNew,$arrXpathTo,$arrItemSettingNew,$arrSettingChild);
				}elseif($sItem_go == 'down'){
					$iNubmerCons = 0;
					if(count(explode('/',$sXpathTo))>2)
					{
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-2;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
						for($i=$iNubmerTo+1; $i<count($arrXpath); $i++)
						{
							if(stristr($arrXpath[$i],$sTemp))
							{
								$iNubmerCons++;
							}
						}
					}else{
						$arrTemp = explode('/',$sXpathTo);
						$sTemp = "";
						for($i=0;$i<count($arrTemp)-1;$i++)
						{
							$sTemp = $sTemp.$arrTemp[$i].'/';
						}
						for($i=$iNubmerTo; $i<count($arrXpath); $i++)
						{
							if(stristr($arrXpath[$i],$sTemp))
							{
								$iNubmerCons++;
							}
						}
					}
	
					if($this->getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath)){
						$sXpathUp=$this->getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo, $arrXpath);
					}else {
						$skey="只能在同级移动";
						$this->view->createMessage(Message::error,"%s ",$skey);
			
						$sControllerNamePage=str_replace('\\','.',$sControllerName);
						$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId";
						$this->location($sUrl,0);
						return;
		
					//return;
					};
					$arrXpathUp=explode('/',$sXpathUp);
					array_pop($arrXpathUp);
					$arrXpathTo=explode('/',$sXpathTo);
					array_pop($arrXpathTo);

					$arrSettingNew=$arrSettingOld;
					$this->settingItemdelete($arrSettingNew, $arrXpathTo);
					$this->itemSettingEdit($arrSettingOld,$sXpath,$sXpathTo,$arrSettingMiddle,$arrSettingChild);
					$this->settingEditXpathOption1(0,$arrSettingNew,$arrXpathUp,$arrItemSettingNew,$arrSettingChild);
				}
				$akey->deleteItem($sTempPath.'-'.$sMenuId);
				$arrItemSettingNew['id']=$sMenuId;
				$arrItemSettingNew['class']='menu';
				$akey->setItem($sTempPath.'-'.$sMenuId,$arrItemSettingNew);
				$sUrl="?c=org.opencomb.menuediter.MenuOpen&locationsort=locationsort&temppath=$sTempPath&menuid=$sMenuId";
				$this->view->createMessage(Message::success,"%s ",$skey='移动成功');
				//return;
				//exit;
				$this->location($sUrl,10);
			}	
		}	
	}
	
	public function xpathOption($arrSetting,$sXpath,$i,&$arrXpath)
	{
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
				$arrXpath[$i]=$sXpath;
				$i++;
			}
	
			if(substr($key,0,5)=='item:')
			{
				$i=$this->xpathOption($arrSetting[$key],$sXpath,$i,$arrXpath);
				$sXpath=$sXpathOld;
			}
		}
		return $i;
	}
	
	public function createSort($h,&$arrSort,$arrToXpath,$arrSettingChild){
		for($i=$h;$i<count($arrToXpath);$i++)
		{
			if($i==count($arrToXpath)-1){
				$arrSort[$arrToXpath[$i]]=$arrSettingChild;
				$h++;
			}
			else {
				$arrSort[$arrToXpath[$i]]=array();
			}
			
			if($i<count($arrToXpath)-1)
			{	
				$i=$this->createSort(++$h,$arrSort[$arrToXpath[$i]],$arrToXpath,$arrSettingChild);
			}
		}
		return $h;
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
	
	//将BeanConfig中的Menue转换成数组存放在setting中
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
	
	
	public function itemSettingEdit($arrSetting,$sXpath,$sXpathTarget,&$arrItemSettingNew,&$arrSettingChild)
	{	
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
			}
			if($sXpath==$sXpathTarget){
	
				if(!is_array($arrSetting[$key])){
					$arrSettingChild=$arrItemSettingNew;
				}
			}
			else
			{
				$arrItemSettingNew[$key]=$arrSetting[$key];
			}
	
			if(substr($key,0,5)=='item:')
			{
				$this->itemSettingEdit($arrSetting[$key],$sXpath,$sXpathTarget,$arrItemSettingNew[$key],$arrSettingChild);
				$sXpath=$sXpathOld;
			}
		}
	}
	
	public function settingEditXpathOption($i,$arrSetting,$sXpath,$sXpathTarget,$sXpathFirst,&$arrSettingNew,$arrSettingChild)
	{
		if($sXpathTarget=='Top/')
		{
			$arrXpathFrom=explode('/',$sXpathFirst);
			array_pop($arrXpathFrom);
			$arrSettingNew=array_merge(array($arrXpathFrom[count($arrXpathFrom)-1]=>$arrSettingChild),$arrSetting);
		}
		else{
			foreach($arrSetting as $key=>$item)
			{
				$sXpathOld=$sXpath;
				if($key=='xpath'){
					$sXpath=$sXpath.$arrSetting['xpath'].'/';
				}
	
				if($key==$sXpathTarget[$i]){
					$arrSettingNew[$key]=$arrSetting[$key];
					$arrSettingNew[$sXpathFirst]=$arrSettingChild;
					
				}
				else
				{	
					$arrSettingNew[$key]=$arrSetting[$key];
				}
	
				if(substr($key,0,5)=='item:')
				{
					$this->settingEditXpathOption($arrSetting[$key],$sXpath,$sXpathTarget,$sXpathFirst,$arrSettingNew[$key],$arrSettingChild);
					$sXpath=$sXpathOld;
				}
			}
		}
	}
	
	public function settingEditXpathOption1($i,$arrSetting,$arrXpathTo,&$arrSettingNew,$arrSettingChild)
	{
		if($arrXpathTo == 'Top/')
		{
			$arrXpathFrom = explode('/',$arrSettingChild['xpath']);
			array_pop($arrXpathFrom);
			$arrSettingNew=array_merge(array($arrXpathFrom[count($arrXpathFrom)-1]=>$arrSettingChild),$arrSetting);
		}
		else{
			foreach($arrSetting as $key=>$item)
			{	
				if($key == $arrXpathTo[$i]){
					if($i == count($arrXpathTo)-1){
						$arrSettingNew[$key] = $arrSetting[$key];
						$arrSettingNew[$arrSettingChild['xpath']] = $arrSettingChild;						
						$i=0;
					}
					else {
						if(array_key_exists('xpath',$arrSetting))
						{
							$arrSettingNew['xpath']=$arrSetting['xpath'];
						}
						if(array_key_exists('title',$arrSetting))
						{
							$arrSettingNew['title']=$arrSetting['title'];
						}
// 						if(array_key_exists('depth',$arrSetting))
// 						{
// 							$arrSettingNew['depth']=$arrSetting['depth'];
// 						}
						if(array_key_exists('link',$arrSetting))
						{
							$arrSettingNew['link']=$arrSetting['link'];
						}
						if(array_key_exists('menu',$arrSetting))
						{
							$arrSettingNew['menu']=$arrSetting['menu'];
						}
						if(array_key_exists('query',$arrSetting))
						{
							$arrSettingNew['query']=$arrSetting['query'];
						}
						if(substr($key,0,5)=='item:')
						{
							$arrSettingNew[$key]=array();
						}
						//$arrSettingNew[$key]=$arrSetting[$key];
					}
					if(count($arrXpathTo)>1)
					{
						$i++;
					}
				}
				else
				{	
					if(array_key_exists('xpath',$arrSetting))
					{
						$arrSettingNew['xpath']=$arrSetting['xpath'];
					}
					if(array_key_exists('title',$arrSetting))
					{
						$arrSettingNew['title']=$arrSetting['title'];
					}
// 					if(array_key_exists('depth',$arrSetting))
// 					{
// 						$arrSettingNew['depth']=$arrSetting['depth'];
// 					}
					if(array_key_exists('link',$arrSetting))
					{
						$arrSettingNew['link']=$arrSetting['link'];
					}
					if(array_key_exists('menu',$arrSetting))
					{
						$arrSettingNew['menu']=$arrSetting['menu'];
					}
					if(array_key_exists('query',$arrSetting))
					{
						$arrSettingNew['query']=$arrSetting['query'];
					}
					if(substr($key,0,5)=='item:')
					{
						$arrSettingNew[$key]=array();
					}
					//$arrSettingNew[$key]=$arrSetting[$key];
				}
	
				if(substr($key,0,5)=='item:')
				{
					$this->settingEditXpathOption1($i,$arrSetting[$key],$arrXpathTo,$arrSettingNew[$key],$arrSettingChild);
				}
			}
		}
	}
	
	public function getUpKey($iNubmerTo, $iNubmerCons, $sXpathTo,$arrXpath)
	{
		for($i=$iNubmerTo-1;$i>=$iNubmerCons;$i--)
		{
			if(count(explode('/',$sXpathTo))!=count(explode('/',$arrXpath[$i])))
			{
				continue;
			}else {
				return array_key_exists($i, $arrXpath) ? $arrXpath[$i] : false;
			}
		}
	}
	
	public function getDownKey($iNubmerTo, $iNubmerCons, $sXpathTo,$arrXpath)
	{	
		$s = true;
		for($i=$iNubmerTo+1; $i<=$iNubmerTo+$iNubmerCons; $i++)
		{	 
			if(array_key_exists($i, $arrXpath))
			{	

				if(count(explode('/',$sXpathTo))!=count(explode('/',$arrXpath[$i])))
				{
					$s = false;
					continue;
				}else {
					$s = true;
					return array_key_exists($i, $arrXpath)?$arrXpath[$i]:false;
				}

			}
			else {
				return array_key_exists($i, $arrXpath)?$arrXpath[$i]:false;
			}
		}
		
		if(!$s)
		{
			return false;
		}
	}
	
	public function insertArr(&$arrInsert,&$arrne)
	{
		foreach($arrInsert as $key=>$item)
		{
			if($key=='a')
			{
				$arrne[$key]=$arrInsert[$key];
				$arrne['w']=2;
			}
			else{
				$arrne[$key]=$arrInsert[$key];
			}
		}
	}
	
	public function readSetting($sControllerNamePageFormal=null,$bFlag=true,$sControllerNameFormal,$sViewPathFormal,$sMenuIdFormal)
	{
		$sControllerNamePage=$sControllerNamePageFormal;
		if($bFlag)
		{
			$sControllerName=$sControllerNameFormal;
		}
		else {
			$sControllerName=str_replace('.','\\',$sControllerNamePage);
		}
		$sViewPath=$sViewPathFormal;
		$sMenuId=$sMenuIdFormal;
		$aSetting = Extension::flyweight('menuediter')->setting();
	
		$akey=$aSetting->key('/menu/'.$sControllerName,true);
		$arrJson=array();
		$arrXpath=array();
		$sXpath='';
		$arrSetting=$akey->Item($sViewPath.'.'.$sMenuId);
		$sMenu=$this->displaySetting($arrSetting,$sXpath,$sControllerName,$sViewPath,$sMenuId);
		$sTopMenu='<ul style=margin-left:10px>'.'<li>'.'<a>'.'顶层'.'</a>'.'&nbsp'.'&nbsp'.'&nbsp'.
				"<a href=\"#\" onclick=\"javascript: itemCreate('Top/')\">".'新建'.'</a>'.'</li>'.'</ul>';
		$sMenu=$sTopMenu.$sMenu;
		$this->viewMenuOpen->variables()->set('sMenu',$sMenu);
		$arrJson['controllername']=$sControllerName;
		$arrJson['viewpath']=$sViewPath;
		$arrJson['menuid']=$sMenuId;
	
		$this->jsonSetting($arrSetting, $sXpath, $arrJson);
		$this->viewMenuOpen->variables()->set('sJsonSetting',json_encode($arrJson));
		$this->xpathOption($arrSetting,'','',0,$arrXpath);
		$arrTop=array('Top/'=>'顶层');
		$arrXpath=array_merge($arrTop,$arrXpath);
		$this->viewMenuOpen->variables()->set('arrXpath',$arrXpath);
	
		$this->viewMenuOpen->widget('hide_create_item_controllerName')->setValue($sControllerName);
		$this->viewMenuOpen->widget('hide_create_item_viewPath')->setValue($sViewPath);
		$this->viewMenuOpen->widget('hide_create_item_menuId')->setValue($sMenuId);
		$aController = new $sControllerName();
		if($sControllerNamePage=='org.opencomb.coresystem.mvc.controller.FrontFrame' or $sControllerNamePage=='org.opencomb.coresystem.mvc.controller.UserPanelFrame')
		{
			$this->getHistory();
		}else {
			if($aController->title()==null)
			{
				$sHistoty='<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".'控制器'.$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory=array($sHistoty);
				$akey=$aSetting->key('/history/'.$sControllerName,true);
				$akey->setItem($sViewPath.'.'.$sMenuId,$arrHistory);
			}else
			{
				$sHistoty='<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".$aController->title().$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory=array($sHistoty);
				$akey=$aSetting->key('/history/'.$sControllerName,true);
				$akey->setItem($sViewPath.'.'.$sMenuId,$arrHistory);
			}
			$this->getHistory();
		}
	
		$sClear="<a href=\"?c=org.opencomb.menuediter.MenuEditerClear&controllername=$sControllerName&viewpath=$sViewPath&menuid=$sMenuId\">".'清除'.'</a>';
		$this->viewMenuOpen->variables()->set('sClear',$sClear);
	}
}

?>