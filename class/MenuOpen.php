<?php
namespace org\opencomb\menuediter;
use org\jecat\framework\verifier\Length;

use org\jecat\framework\lang\Object;
use org\jecat\framework\message\Message;
use org\jecat\framework\lang\oop\ClassLoader;
use org\jecat\framework\mvc\view\widget\menu\Menu;
use org\jecat\framework\mvc\view\View;
use org\jecat\framework\mvc\controller\IController;
use org\jecat\framework\lang\aop\AOP;
use org\opencomb\platform\ext\Extension;
use org\jecat\framework\bean\BeanFactory;
use org\opencomb\frameworktest\aspect;
use org\opencomb\platform\system\PlatformSerializer;
use org\jecat\framework\ui\xhtml\weave\Patch;
use org\jecat\framework\ui\xhtml\weave\WeaveManager;
use org\opencomb\coresystem\mvc\controller\ControlPanel;
use org\opencomb\coresystem\mvc\controller\ControlPanelFrame;

class MenuOpen extends ControlPanel
{
	static $nCountTotal =0;
	public function createBeanConfig()
	{
		return array(
			'title'=> '文章内容',
			'view:menuOpen'=>array(
				'template'=>'MenuOpen.html',
				'class'=>'form',
				'widgets' => array(
					array(
						'id'=>'controller_name',
						'class'=>'text',
						'title'=>'控制器'		
					),
					array(
						'id'=>'viewXpath',
						'class'=>'text',
						'title'=>'视图路径'
					),
					array(
						'id'=>'menu_id',
						'class'=>'text',
						'title'=>'控件ID'
					),
					array(
						'id'=>'title',
						'class'=>'text',
						'title'=>'Item标题'
					),
					array(
						'id'=>'parentMenu',
						'class'=>'text',
						'title'=>'上一级菜单'
					),
					array(
						'id'=>'depth',
						'class'=>'text',
						'title'=>'层级'
					),
					array(
						'id'=>'link',
						'class'=>'text',
						'title'=>'link',
					),
					array(
						'id'=>'active',
						'class'=>'text',
						'title'=>'激活'
					),
					array(
						'id'=>'hide_flag_edit_item',
						'class'=>'text',
						'type'=>'hidden',
						'title'=>'判断提交项目编辑'		
					),
					array(
						'id'=>'hide_item_controllerName',
						'class'=>'text',
						'type'=>'hidden',
						'title'=>'编辑项controllerName'
					),
					array(
						'id'=>'hide_item_viewPath',
						'class'=>'text',
						'type'=>'hidden',
						'title'=>'编辑项viewPath'
					),
					array(
						'id'=>'hide_item_menuId',
						'class'=>'text',
						'type'=>'hidden',
						'title'=>'编辑项menuId'
					),
					array(
						'id'=>'hide_item_xpath',
						'class'=>'text',
						'type'=>'hidden',
						'title'=>'编辑项menuId'
					),
					array(
						'id'=>'create_item_id',
						'class'=>'text',
					),
					array(
						'id'=>'create_title',
						'class'=>'text',
						'title'=>'控制器'
					),
					array(
						'id'=>'create_depth',
						'class'=>'text',
						'title'=>'视图路径'
					),
					array(
						'id'=>'create_link',
						'class'=>'text',
						'title'=>'控件ID'
					),
					array(
						'id'=>'create_active',
						'class'=>'text',
						'title'=>'控件ID'
					),
					array(
						'id'=>'hide_item_create_xpath',
						'class'=>'text',
						'type'=>'hidden',
						'title'=>'编辑项menuId'
					),
						array(
								'id'=>'hide_create_item_controllerName',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'编辑项controllerName'
						),
						array(
								'id'=>'hide_create_item_viewPath',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'编辑项viewPath'
						),
						array(
								'id'=>'hide_create_item_menuId',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'编辑项menuId'
						),
						array(
								'id'=>'hide_flag_create_item',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'判断提交项目编辑'
						),
					array(
						'id'=>'testMenu',
						'class'=>'menu',
						'title'=>'sddsd',
						'item:testSub2'=>array(
								'title'=>'testSub2',
						),
						'item:testSub1'=>array(
								'title'=>'testSub1',
  								'menu'=>'1',
								'tearoff'=>1,
								'showDepths'=>5,
								'item:testSub11'=>array(
 										'title'=>'testSub11',
										'menu'=>'1',
										'item:testSub111'=>array(
											'title'=>'testSub111',
										)
  								),
								'item:testSub12'=>array(
										'title'=>'testSub12',
										'menu'=>'1',
										'item:testSub121'=>array(
											'title'=>'testSub121',		
										),
										'item:testSub122'=>array(
											'title'=>'testSub122',
										)
								),
						),
						'item:testSub3'=>array(
							'title'=>'testSub3',		
						),
					)
				),
			),
		);
	}
	
	public function process()
	{	
 		if($this->viewMenuOpen->isSubmit($this->params))
 		{	
 			
 				$this->viewMenuOpen->loadWidgets($this->params);
 			if($this->viewMenuOpen->widget('hide_flag_edit_item')->value()==1)
 			{
 				$sControllerName=$this->viewMenuOpen->widget('hide_item_controllerName')->value();
 				$sViewPath=$this->viewMenuOpen->widget('hide_item_viewPath')->value();
 				$sMenuId=$this->viewMenuOpen->widget('hide_item_menuId')->value();
 				
 				$aSetting = Extension::flyweight('menuediter')->setting();
 			
 				if($aSetting->hasItem('/'.$sControllerName,$sViewPath.$sMenuId))
 				{		
 						$sControllerNamePage=str_replace('\\','.',$sControllerName);
 						$akey=$aSetting->key('/'.$sControllerName,true);
 						$sXpathFrom=$this->viewMenuOpen->widget('hide_item_xpath')->value();
 						$sXpathOption=$this->params->get('xpathOption');
 						
 						//判断移动的层级
 						if(!$this->xPathOptionBool($sXpathFrom,$sXpathOption))
 						{
 							$skey="移动层级错误";
 							$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
 							return;
 						}
 						
 						$arrToXpath=explode('/',$sXpathFrom);
 						array_pop($arrToXpath);
 						$arrSettingOld=array();
 						$arrSettingDelete=array();
 						$arrSettingChild=array();
 						$arrItemSettingMiddle=array();
 						
 						
 						$arrSettingNew=array();

 						$arrSettingOld=$akey->item($sViewPath.$sMenuId);
 						$arrSettingDelete=$arrSettingOld;
 						
 						$this->settingItemdelete($arrSettingDelete, $arrToXpath);

 						$this->itemSettingEdit($arrSettingOld, '', $sXpathFrom, $arrItemSettingMiddle,$arrSettingChild);
 						$arrSettingChild['title']=$this->viewMenuOpen->widget('title')->value();
 						$arrSettingChild['depth']=$this->viewMenuOpen->widget('depth')->value();
 						$arrSettingChild['link']=$this->viewMenuOpen->widget('link')->value();
 						$arrSettingChild['active']=$this->viewMenuOpen->widget('active')->value();
 						$this->settingEditXpathOption($arrSettingDelete, '', $sXpathOption,$sXpathFrom, $arrSettingNew,$arrSettingChild);
 						$akey->deleteItem($sViewPath.$sMenuId);
 						$arrSettingNew['id']=$sMenuId;
 						$arrSettingNew['class']='menu';
 						$akey->setItem($sViewPath.$sMenuId,$arrSettingNew);
 						
 						//$sClear="<a href=\"?c=org.opencomb.menuediter.MenuEditerClear&controllername=$sControllerName&viewpath=$sViewPath&menuid=$sMenuId\">".'清除'.'</a>';
 						//$this->viewMenuOpen->variables()->set('sClear',$sClear);
 						$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
 						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
 				}else {
 						$akey=$aSetting->key('/'.$sControllerName,true);
 						$sControllerNamePage=str_replace('\\','.',$sControllerName);
 						$arrSettingOld=array();
 						$aController = new $sControllerName();
 						$aView = View::findXPath($aController->mainView(),$sViewPath );
 						$aMenu=$aView->widget($sMenuId);
 						$aMenuIterator=$aMenu->itemIterator();
 						$this->itemSetting($aMenuIterator,$arrSettingOld);
 						
 					
 						$sXpathFrom=$this->viewMenuOpen->widget('hide_item_xpath')->value();
 						$sXpathOption=$this->params->get('xpathOption');
 						//判断移动层级
						$this->settingItemdelete($arrSettingDelete, $arrToXpath);
 						
 						$arrToXpath=explode('/',$sXpathFrom);
 						array_pop($arrToXpath);

 						$arrSettingDelete=array();
 						$arrSettingChild=array();
 						$arrItemSettingMiddle=array();
 						$arrSettingNew=array();
 						$arrSettingDelete=$arrSettingOld;

 						
 						
 						//$this->settingItemdelete(0,$arrSettingOld, '', $arrToXpath, $arrSettingDelete);
 						$this->settingItemdelete($arrSettingDelete, $arrToXpath);

 						$this->itemSettingEdit($arrSettingOld, '', $sXpathFrom, $arrItemSettingMiddle,$arrSettingChild);
 						$arrSettingChild['title']=$this->viewMenuOpen->widget('title')->value();
 						$arrSettingChild['depth']=$this->viewMenuOpen->widget('depth')->value();
 						$arrSettingChild['link']=$this->viewMenuOpen->widget('link')->value();
 						$arrSettingChild['active']=$this->viewMenuOpen->widget('active')->value();
 						$this->settingEditXpathOption($arrSettingDelete, '', $sXpathOption,$sXpathFrom, $arrSettingNew,$arrSettingChild);
 						$arrSettingNew['id']=$sMenuId;
 						$arrSettingNew['class']='menu';
 						$akey->setItem($sViewPath.$sMenuId,$arrSettingNew);
 						
 						$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
 						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
 				}
 			}elseif($this->viewMenuOpen->widget('hide_flag_create_item')->value()==2){
 				
 				$sControllerName=$this->viewMenuOpen->widget('hide_create_item_controllerName')->value();
 				$sViewPath=$this->viewMenuOpen->widget('hide_create_item_viewPath')->value();
 				$sMenuId=$this->viewMenuOpen->widget('hide_create_item_menuId')->value();
 				
 				$aSetting = Extension::flyweight('menuediter')->setting();

 				if($aSetting->hasItem('/'.$sControllerName,$sViewPath.$sMenuId))
 				{
 					$sControllerNamePage=str_replace('\\', '.', $sControllerName);
 					
 					$akey=$aSetting->key('/'.$sControllerName,true);
 					$arrSettingOld=$akey->Item($sViewPath.$sMenuId);
 					
 					$sXpath='';
					$arrSettingNew=array();
 					$sItemId='item:'.$this->viewMenuOpen->widget('create_item_id')->value();
 					$sXpathTo=$this->viewMenuOpen->widget('hide_item_create_xpath')->value();
 					$sItemId=$sXpathTo.$sItemId.'/';
 					$bflag=false;
 					
 					if($this->idSearch($arrSettingOld,$sXpath,$sItemId,$bflag))
 					{
 						$skey="项目ID重复";
 						$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
 						return;
 					}
 					
 					$arrItemChild=array(
 						'xpath'=>'item:'.$this->viewMenuOpen->widget('create_item_id')->value(),
 						'title'=>$this->viewMenuOpen->widget('create_title')->value(),
 						'depth'=>$this->viewMenuOpen->widget('create_depth')->value(),
 						'link'=>$this->viewMenuOpen->widget('create_link')->value(),
 						'active'=>$this->viewMenuOpen->widget('create_active')->value(),		
 					);
 					$this->createItem($arrSettingOld, $sXpath, $sXpathTo, $arrSettingNew, $arrItemChild);
 					$akey->deleteItem($sViewPath.$sMenuId);
 					$arrSettingNew['id']=$sMenuId;
 					$arrSettingNew['class']='menu';
 					$akey->setItem($sViewPath.$sMenuId,$arrSettingNew);
 					
 					$sClear="<a href=\"?c=org.opencomb.menuediter.MenuEditerClear&controllername=$sControllerName&viewpath=$sViewPath&menuid=$sMenuId\">".'清除'.'</a>';
 					$this->viewMenuOpen->variables()->set('sClear',$sClear);
 					
 					$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
 					$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
 				}else {
 					$sControllerNamePage=str_replace('\\', '.', $sControllerName);
 					
 					$akey=$aSetting->key('/'.$sControllerName,true);
 					$sItemId='item:'.$this->viewMenuOpen->widget('create_item_id')->value();
 					$sXpathTo=$this->viewMenuOpen->widget('hide_item_create_xpath')->value();
 					$arrSettingOld=array();
 					$aController = new $sControllerName();
 					$aView = View::findXPath($aController->mainView(),$sViewPath );
 					$aMenu=$aView->widget($sMenuId);
 					$aMenuIterator=$aMenu->itemIterator();
 					$this->itemSetting($aMenuIterator,$arrSettingOld);
 					$sXpath='';
					$arrSettingNew=array();
 					$sItemId=$sXpathTo.$sItemId.'/';
 					$bflag=false;
 					
 					if($this->idSearch($arrSettingOld,$sXpath,$sItemId,$bflag))
 					{
 						$skey="项目ID重复";
 						$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
 						return;
 					}
 					
 					$arrItemChild=array(
 						'xpath'=>'item:'.$this->viewMenuOpen->widget('create_item_id')->value(),
 						'title'=>$this->viewMenuOpen->widget('create_title')->value(),
 						'depth'=>$this->viewMenuOpen->widget('create_depth')->value(),
 						'link'=>$this->viewMenuOpen->widget('create_link')->value(),
 						'active'=>$this->viewMenuOpen->widget('create_active')->value(),		
 					);
 					$this->createItem($arrSettingOld, $sXpath, $sXpathTo, $arrSettingNew, $arrItemChild);
 					$akey->deleteItem($sViewPath.$sMenuId);
 					$arrSettingNew['id']=$sMenuId;
 					$arrSettingNew['class']='menu';
 					$akey->setItem($sViewPath.$sMenuId,$arrSettingNew);
					$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
					$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
 				}
 			}else {
 				$sControllerNamePage=$this->viewMenuOpen->widget('controller_name')->value();
 				$sControllerName=str_replace('.','\\',$sControllerNamePage);
 				$sViewPath=$this->viewMenuOpen->widget('viewXpath')->value();
 				$sMenuId=$this->viewMenuOpen->widget('menu_id')->value();
 				$aSetting = Extension::flyweight('menuediter')->setting();
 				
 				
 				if($aSetting->hasItem('/'.$sControllerName,$sViewPath.$sMenuId))
 				{	
 					$this->readSetting($sControllerNamePage,false,null,$sViewPath,$sMenuId);
 				}
 				else {
 					$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
 				}
 			}
 		}elseif($this->params->get('history')=='history'){
 			$this->viewMenuOpen->widget('controller_name')->setValue($this->params->get('controllername'));
 			$this->viewMenuOpen->widget('viewXpath')->setValue($this->params->get('viewpath'));
 			$this->viewMenuOpen->widget('menu_id')->setValue($this->params->get('menuid'));
 			$this->getHistory();
 		}elseif($this->params->get('locationsort')=='locationsort'){
 			$sControllerNamePage=$this->params->get('controllername');
 			$sControllerName=str_replace('.','\\',$sControllerNamePage);
 			$sViewPath=$this->params->get('viewpath');
 			$sMenuId=$this->params->get('menuid');
 			$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
 			$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
 		}elseif($this->params->get('locationdelete')=='locationdelete'){
 			$sControllerNamePage=$this->params->get('controllername');
 			$sControllerName=str_replace('.','\\',$sControllerNamePage);
 			$sViewPath=$this->params->get('viewpath');
 			$sMenuId=$this->params->get('menuid');
 			$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
 			$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
 		}else {
			$this->getHistory();
 		}
	}
	
	//将BeanConfig中的Menue转换成数组存放在setting中
	public function itemSetting($aMenuIterator,&$arrSetting)
	{	
		$arrI=&$arrSetting;
		foreach($aMenuIterator as $key=>$aItem)
		{
			if($aItem->title())
			{	$aItem->title();
				$arrI=&$arrSetting['item:'.$key];
				$arrI=array('xpath'=>'item:'.$aItem->id(),'title'=>$aItem->title(),'depth'=>$aItem->depth(),'link'=>$aItem->link(),'menu'=>$aItem->subMenu()?1:0,'active'=>$aItem->isActive());
				$arrI=&$arrSetting;
			}
			if($aItem->subMenu())
			{
				$arrI=&$arrI['item:'.$key];
				$this->itemSetting($aItem->subMenu()->itemIterator(),$arrI);
			}
		}
	}
	
	//从BeanConfig中读取Menu，显示
	public function itemMerge($aMenuIterator,$sXpath,$sControllerName,$sViewPath,$sMenuId)
	{	
		$sItem='<ul class=mo-middile-ul>';
		foreach($aMenuIterator as $aItem)
		{	
			$sXpathOld=$sXpath;
			$sXpath=$sXpath.'item:'.$aItem->id().'/';
			$sItem=$sItem."<li xpath=\"$sXpath\">";
			if($aItem->title())
			{	
				$sTitle=$aItem->title();
				$sDepth=$aItem->depth();
				$bActive=$aItem->isActive();
				$sLink=substr($aItem->link(),1);
				$sItem=$sItem.'<span>'.$aItem->title().'</span>'.
						"<a class=\"mo-del\" href=\"?c=org.opencomb.menuediter.ItemDelete&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\" onclick='javascript: return confirmDel()'>".
						"删除".'</a>'.
						"<a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('$sXpath')\">".'新建'.'</a>'.'</a>'.
						"<a class=\"mo-edit\" href=\"#\" onclick=\"javascript: itemEdit('$sXpath')\">".'编辑'.'</a>'.
						"<a class=\"mo-up\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=up&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\">".'向上'.'</a>'.
						"<a class=\"mo-down\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=down&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\">".'向下'.'</a>';
			}
			if($aItem->subMenu())
			{
				$sItem=$sItem.$this->itemMerge($aItem->subMenu()->itemIterator(),$sXpath,$sControllerName,$sViewPath,$sMenuId);
				$sXpath=$sXpathOld;
			}
			else
			{
				$sXpath=$sXpathOld;
			}
			$sItem=$sItem.'</li>';
		}
		$sItem=$sItem.'</ul>';
		return $sItem;
	}
	
	//从setting直接读取Menu，显示
	public function displaySetting($arrSetting,$sXpath,$sControllerName,$sViewPath,$sMenuId)
	{
		$sMenu='<ul class=mo-middile-ul>';
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
			}
			
			if($key=='title')
			{
				$sMenu=$sMenu."<li xpath=\"$sXpath\">";
			}
			
			if($key=='title')
			{
				$sMenu=$sMenu."<span>".$arrSetting['title'].'</span>'.
						"<a class=\"mo-del\" href=\"?c=org.opencomb.menuediter.ItemDelete&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\" onclick='javascript: return confirmDel()'>".
						"删除".'</a>'.
						"<a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('$sXpath')\">".'新建'.'</a>'.'</a>'.
						"<a class=\"mo-edit\" href=\"#\" onclick=\"javascript: itemEdit('$sXpath')\">".'编辑'.'</a>'.
						"<a class=\"mo-up\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=up&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\">".'向上'.'</a>'.
						"<a class=\"mo-down\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=down&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\">".'向下'.'</a>';
			}
			if(is_array($item))
			{
				$sMenu=$sMenu.$this->displaySetting($item,$sXpath,$sControllerName,$sViewPath,$sMenuId);
				$sXpath=$sXpathOld;
			}
			if($key=='title')
			{
				$sMenu=$sMenu."</li>";
			}
			//$sMenu=$sMenu."</li>";
		}
		$sMenu=$sMenu.'</ul>';
		return $sMenu;
	}
	
	//json
	public function jsonSetting($arrSetting,$sXpath,&$arrJson){
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
				$arrJson[$sXpath]=array('title'=>$arrSetting['title'],
						'xpath'=>$arrSetting['xpath'],'link'=>$arrSetting['link'],
						'depth'=>$arrSetting['depth'],'active'=>$arrSetting['active']);
			}
	
			if(is_array($arrSetting[$key]))
			{
				$this->jsonSetting($arrSetting[$key],$sXpath,$arrJson);
				$sXpath=$sXpathOld;
			}
		}
	}
	
	//移动选项xpath
	public function xpathOption($arrSetting,$sXpath,$sTitle,$i,&$arrXpath){
		foreach($arrSetting as $key=>$item)
		{
			$i++;
			$sXpathOld=$sXpath;
			$sTitleOld=$sTitle;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
				$sTitle=$sTitle.$arrSetting['title'].'/';
				$arrXpath[$sXpath]=$sTitle;
			}
	
			if(is_array($arrSetting[$key]))
			{
				$i=$i+$this->xpathOption($arrSetting[$key],$sXpath,$sTitle,$i++,$arrXpath);
				$sXpath=$sXpathOld;
				$sTitle=$sTitleOld;
			}
		}
		return $i;
	}
	
	//setting编辑
	public function settingEdit($arrSettingOld,$arrItem,$sXpath,$sXpathTarget,&$arrSettingNew){
		foreach($arrSettingOld as $key=>$item)
		{
			//上一级xpath
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSettingOld['xpath'].'/';
			}
	
			//找到需要编辑的元素
			if($sXpath==$sXpathTarget){
				$arrSettingNew['title']=$arrItem['title'];
				$arrSettingNew['depth']=$arrItem['depth'];
				$arrSettingNew['link']=$arrItem['link'];
				$arrSettingNew['active']=$arrItem['active'];
			}
			else
			{
				$arrSettingNew[$key]=$arrSettingOld[$key];
			}
	
			//如果是数组递归
			if(is_array($arrSettingOld[$key]))
			{
				$this->settingEdit($arrSettingOld[$key],$arrItem,$sXpath,$sXpathTarget,$arrSettingNew[$key]);
				$sXpath=$sXpathOld;
			}
		}
	}
	
	//获得修改的item
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
	
			if(is_array($arrSetting[$key]))
			{
				$this->itemSettingEdit($arrSetting[$key],$sXpath,$sXpathTarget,$arrItemSettingNew[$key],$arrSettingChild);
				$sXpath=$sXpathOld;
			}
		}
	}
	
	//删除一个item
	public function settingItemdelete(&$arrSettingDelete,$arrXpathTarget)
	{
		foreach($arrSettingDelete as $key=>&$item)
		{
			for($i=0;$i<count($arrXpathTarget);$i++)
			{
				if($key==$arrXpathTarget[$i])
				{
					if($i==count($arrXpathTarget)-1)
					{
						unset($arrSettingDelete[$key]);
					}
					else {
						$this->settingItemdelete($arrSettingDelete[$key],$arrXpathTarget);
					}
				}
			}
		}
	}
	
	//删除一个item
// 	public function settingItemdelete($h,$arrSetting,$sXpath,$arrXpathTarget,&$arrSettingDelete)
// 	{
// 		foreach($arrSetting as $key=>$item)
// 		{
// 			for($i=$h;$i<count($arrXpathTarget);$i++)
// 			{
// 				if($key==$arrXpathTarget[$i])
// 				{
// 					if($i==count($arrXpathTarget)-1)
// 					{
// 						//continue;
// 						unset($arrSetting[$key]);
// 						unset($arrSettingDelete[$key]);
// 						$i++;
// 					}else {
// 						$arrSettingDelete[$key]=$arrSetting[$key];
// 						$i=$i+$this->settingItemdelete($i,$arrSetting[$key],$sXpath,$arrXpathTarget,$arrSettingDelete[$key]);
// 					}
// 				}
// 				else {
// 				$arrSettingDelete[$key]=$arrSetting[$key];
// 				}
// 			}
// 		}
// 		return $i;
// 	}
	
	public function settingEditXpathOption($arrSetting,$sXpath,$sXpathTarget,$sXpathFirst,&$arrSettingNew,$arrSettingChild)
	{
		if($sXpathTarget=='Top')
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
			
				if($sXpath==$sXpathTarget){
					$arrSettingNew[$arrSettingChild['xpath']]=$arrSettingChild;
				}
				else
				{	if($sXpath==$sXpathFirst){
						unset($arrSetting[$key]);
					}else {
						$arrSettingNew[$key]=$arrSetting[$key];
					}
			
				}
			
				if(is_array($arrSetting[$key]))
				{
					$this->settingEditXpathOption($arrSetting[$key],$sXpath,$sXpathTarget,$sXpathFirst,$arrSettingNew[$key],$arrSettingChild);
					$sXpath=$sXpathOld;
				}
			}
		}	
	}
	
	public function idSearch($arrSettingOld,$sXpath,$sItemId,$bflag)
	{
		foreach($arrSettingOld as $key=>$item)
		{
			//上一级xpath
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSettingOld['xpath'].'/';
			}
	
			if($sXpath==$sItemId){
				$bflag=true;
			}
			if(is_array($arrSettingOld[$key]))
			{
				$bflag=$this->idSearch($arrSettingOld[$key],$sXpath,$sItemId,$bflag);
				$sXpath=$sXpathOld;
			}
		}
		return $bflag;
	}
	
	public function createItem($arrSetting,$sXpath,$sXpathTo,&$arrSettingNew,$arrItemChild)
	{
		if($sXpathTo=='Top')
		{
			$arrSettingNew=array_merge(array($arrItemChild['xpath']=>$arrItemChild),$arrSetting);
		}
		else{
			foreach($arrSetting as $key=>$item)
			{
				$sXpathOld=$sXpath;
				if($key=='xpath'){
					$sXpath=$sXpath.$arrSetting['xpath'].'/';
				}
	
				if($sXpath==$sXpathTo){
					$arrSettingNew[$arrItemChild['xpath']]=$arrItemChild;
				}
				else
				{
					$arrSettingNew[$key]=$arrSetting[$key];
				}
	
				if(is_array($arrSetting[$key]))
				{
					$this->createItem($arrSetting[$key],$sXpath,$sXpathTo,$arrSettingNew[$key],$arrItemChild);
					$sXpath=$sXpathOld;
				}
			}
		}
	}
	
	
	public function getHistory()
	{
		$aSetting = Extension::flyweight('menuediter')->setting();
		$arrHistory=array();
		$arrHistory[0]=array('<ul><li><a href="?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=org.opencomb.coresystem.mvc.controller.FrontFrame
						&viewpath=frameView&menuid=mainMenu">前台</a></li></ul>');
		$arrHistory[1]=array('<ul><li><a href="?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=org.opencomb.coresystem.mvc.controller.UserPanelFrame
				&viewpath=userPanelFrame&menuid=mainMenu">用户</a></li></ul>');
		$sHistory=null;
		$i=2;	
		foreach($aSetting->keyIterator('/history') as $key=>$akey)
		{
			$i=$i+1;
			foreach($akey->itemIterator() as $key1=>$item)
			{
				$arrHistory[$i++]=$akey->item($item,array());
			}
		}
		foreach($arrHistory as $key=>$value)
		{
			$sHistory=$sHistory.$value[0];
		}
		$this->viewMenuOpen->variables()->set('sHistory',$sHistory);
	}
	
	//从setting中读取已有的menu
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
		
		$akey=$aSetting->key('/'.$sControllerName,true);
		$arrJson=array();
		$arrXpath=array();
		$sXpath='';
		$arrSetting=$akey->Item($sViewPath.$sMenuId);
		$sMenu=$this->displaySetting($arrSetting,$sXpath,$sControllerName,$sViewPath,$sMenuId);
		$sTopMenu='<ul class=mo-middile-ul>'.'<li>'.'<span>'.'顶层'.'</span>'.
				"<a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('Top')\">".'新建'.'</a>'.'</li>'.'</ul>';
		$sMenu=$sTopMenu.$sMenu;
		$this->viewMenuOpen->variables()->set('sMenu',$sMenu);
		$arrJson['controllername']=$sControllerName;
		$arrJson['viewpath']=$sViewPath;
		$arrJson['menuid']=$sMenuId;
		
		$this->jsonSetting($arrSetting, $sXpath, $arrJson);
		$this->viewMenuOpen->variables()->set('sJsonSetting',json_encode($arrJson));
		$this->xpathOption($arrSetting,'','',0,$arrXpath);
		$arrTop=array('Top'=>'顶层');
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
				$akey->setItem($sViewPath.$sMenuId,$arrHistory);
			}else
			{
				$sHistoty='<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".$aController->title().$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory=array($sHistoty);
				$akey=$aSetting->key('/history/'.$sControllerName,true);
				$akey->setItem($sViewPath.$sMenuId,$arrHistory);
			}
			$this->getHistory();
		}
		
		$sClear="<a class=\"mo-clear\" href=\"?c=org.opencomb.menuediter.MenuEditerClear&controllername=$sControllerName&viewpath=$sViewPath&menuid=$sMenuId\">".'清除'.'</a>';
		$this->viewMenuOpen->variables()->set('sClear',$sClear);
	}
	
	//从Beanconfig中读取menu
	public function readBeanConfig($sControllerNamePageFormal=null,$bFlag=true,$sControllerNameFormal,$sViewPathFormal,$sMenuIdFormal)
	{
		//$sControllerNamePage=$this->viewMenuOpen->widget('controller_name')->value();
		//$sControllerName=str_replace('.','\\',$sControllerNamePage);
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
		
		$arrJson=array();
		$arrXpath=array();
		// 检查 控制器类 是否有效
		if( !class_exists($sControllerName) or !new $sControllerName() instanceof IController)
		{
			$skey="无此控制器";
			$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
			return;
		}
		else {
			$aController = new $sControllerName();
		}
		
		// 检查视图
		if( !$aView = View::findXPath($aController->mainView(),$sViewPath))
		{
			$skey="无此视图";
			$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
			return;
		}else {
			$aView = View::findXPath($aController->mainView(),$sViewPath );
		}
		// 检查菜单
		if( !$aMenu=$aView->widget($sMenuId) or !$aMenu instanceof Menu)
		{
			$skey="无此菜单";
			$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
			return;
		}else {
			$aMenu=$aView->widget($sMenuId);
		}
		
		//将menu组成字符串在页面显示
		$arrSettingOld=array();
		$arrJson=array();
		$arrSetting=array();
		$sXpath='';
		$aMenuIterator=$aMenu->itemIterator();
		$this->itemSetting($aMenuIterator,$arrSettingOld);
		$sMenu=$this->displaySetting($arrSettingOld,$sXpath,$sControllerName,$sViewPath,$sMenuId);
		//$sMenu=$this->itemMerge($aMenuIterator,$sXpath,$sControllerName,$sViewPath,$sMenuId);
		$sTopMenu='<ul class=mo-middile-ul>'.'<li>'.'<span>'.'顶层'.'</span>'.
				"<a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('Top')\">".'新建'.'</a>'.'</li>'.'</ul>';
		$sMenu=$sTopMenu.$sMenu;
		
		//将menu遍历成数组存放在settting
		
		$this->itemSetting($aMenuIterator,$arrSetting);
		$this->jsonSetting($arrSetting, $sXpath, $arrJson);
		
		
		$arrJson['controllername']=$sControllerName;
		$arrJson['viewpath']=$sViewPath;
		$arrJson['menuid']=$sMenuId;
		
		$this->xpathOption($arrSetting,'','',0,$arrXpath);
		$arrTop=array('Top'=>'顶层');
		$arrXpath=array_merge($arrTop,$arrXpath);
		
		$this->viewMenuOpen->variables()->set('sMenu',$sMenu);
		$this->viewMenuOpen->variables()->set('sJsonSetting',json_encode($arrJson));
		$this->viewMenuOpen->variables()->set('arrXpath',$arrXpath);
		
		$this->viewMenuOpen->widget('hide_create_item_controllerName')->setValue($sControllerName);
		$this->viewMenuOpen->widget('hide_create_item_viewPath')->setValue($sViewPath);
		$this->viewMenuOpen->widget('hide_create_item_menuId')->setValue($sMenuId);
		
		if($sControllerNamePage=='org.opencomb.coresystem.mvc.controller.FrontFrame' or $sControllerNamePage=='org.opencomb.coresystem.mvc.controller.UserPanelFrame')
		{
			$this->getHistory();
		}else {
			if($aController->title()==null)
			{
				$sHistoty='<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".'控制器'.$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory=array($sHistoty);
				$akey=$aSetting->key('/history/'.$sControllerName,true);
				$akey->setItem($sViewPath.$sMenuId,$arrHistory);
			}else
			{
				$sHistoty='<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".$aController->title().$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory=array($sHistoty);
				$akey=$aSetting->key('/history/'.$sControllerName,true);
				$akey->setItem($sViewPath.$sMenuId,$arrHistory);
			}
			$this->getHistory();
		}
	}
	
	public function setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId){
		$this->viewMenuOpen->widget('controller_name')->setValue($sControllerNamePage);
		$this->viewMenuOpen->widget('viewXpath')->setValue($sViewPath);
		$this->viewMenuOpen->widget('menu_id')->setValue($sMenuId);
	}
	
	//判断编辑Item时，移动层级
	public function xPathOptionBool($sXpathFrom,$sXpathOption)
	{
		$bflag=true;
		$h=0;
		$arrXpathForm=explode('/',$sXpathFrom);
		$arrXpathOption=explode('/',$sXpathOption);
		array_pop($arrXpathForm);
		array_pop($arrXpathOption);
		if(count($arrXpathForm)>=count($arrXpathOption))
		{
			for($i=0;$i<count($arrXpathOption);$i++)
			{
				if($arrXpathForm[$i]==$arrXpathOption[$i])
				{
					$h++;
				}
			}
			if($h==count($arrXpathOption))
			{
				return false;
			}
			else{
				return true;
			}
		}elseif(count($arrXpathForm)<=count($arrXpathOption))
		{
			for($i=0;$i<count($arrXpathForm);$i++)
			{
				if($arrXpathForm[$i]==$arrXpathOption[$i])
				{
					$h++;
				}
			}
			if($h==count($arrXpathForm))
			{
				return false;
			}else{
				return true;
			}
		}
		//return $bflag;
	}
	
	// 	//settings数组递归方法
// 	public function itemSetting($arra,&$arrlist)
// 	{
// 		$arrI=&$arrlist;
// 		foreach($arra as $key=>$aItem)
// 		{	
			
// 			if($aItem->title())
// 			{	
// 				$arrI=&$arrlist[$key];
// 				$arrI=array('title'=>$aItem->title(),'depth'=>$aItem->depth(),'link'=>$aItem->link(),'active'=>$aItem->isActive(),'children'=>array());
// 				$arrI=&$arrlist;
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$arrI=&$arrI[$key]['children'];
// 				$this->itemSetting($aItem->subMenu()->itemIterator(),$arrI);
// 			}
// 		}
// 	}
}

?>