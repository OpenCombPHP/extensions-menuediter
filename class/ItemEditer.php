<?php
namespace org\opencomb\menuediter;
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
use org\jecat\framework\mvc\controller\Controller;

class ItemEditer extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=> '文章内容',
			'view:itemEditer'=>array(
				'template'=>'ItemEditer.html',
				'class'=>'form',
				'widgets' => array(
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
							'id'=>'hide_ItemId',
							'class'=>'text',
							'type'=>'hidden',
							'title'=>'Item标题'
					),
					array(
							'id'=>'hide_depth',
							'class'=>'text',
							'type'=>'hidden',
							'title'=>'层级'
					),
					array(
							'id'=>'hide_link',
							'class'=>'text',
							'type'=>'hidden',
							'title'=>'link',
					),
					array(
							'id'=>'hide_active',
							'class'=>'text',
							'type'=>'hidden',
							'title'=>'激活'
					),
					array(
							'id'=>'hide_controllername',
							'class'=>'text',
							'type'=>'hidden',
							'title'=>'层级'
					),
					array(
							'id'=>'hide_viewpath',
							'class'=>'text',
							'type'=>'hidden',
							'title'=>'link',
					),
					array(
							'id'=>'hide_widgetid',
							'class'=>'text',
							'type'=>'hidden',
							'title'=>'激活'
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
// 									'tearoff'=>1,
// 									'showDepths'=>5,
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
									'menu'=>1,
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
					)
				),
			),
		);
	}
	
	public function process()
	{	
		if($this->viewItemEditer->isSubmit($this->params))
		{
			$aSetting = Extension::flyweight('menuediter')->setting();
			$arrMenulist=array();
			//读取Menu参数
			$this->viewItemEditer->loadWidgets ( $this->params );
			$sTitle=$this->viewItemEditer->widget('title')->value();
			$sDepth=$this->viewItemEditer->widget('depth')->value();
			$sLink=$this->viewItemEditer->widget('link')->value();
			$sActive=$this->viewItemEditer->widget('active')->value();
			$arrItem=array('title'=>$sTitle,'depth'=>$sDepth,'link'=>$sLink,'active'=>$sActive);
			
			$sControllerName=$this->params->get('controllerName');
			$sViewPath=$this->params->get('viewPath');
			$sMenuId=$this->params->get('sMenuId');
			$xpathTarget=$this->params->get('xpath');
		
 			//获得menu的相关信息
 			$sXpath='';
 			$sXpathTarget=$this->params->get('xpath');
 			$sControllerName=$this->params->get('controllerName');
 			$sViewPath=$this->params->get('viewPath');
 			$sMenuId=$this->params->get('sMenuId');
 			//从setting中取得数组创建Menu
 			$akey=$aSetting->key('/'.$sControllerName,true);
 			$arrSettingOld=$akey->item($sViewPath.$sMenuId);
 			$arrSettingNew=array();
 			$this->settingEdit($arrSettingOld,$arrItem,$sXpath,$sXpathTarget,$arrSettingNew);
 			$akey->deleteItem($sViewPath.$sMenuId);
 			$akey->setItem($sViewPath.$sMenuId,$arrSettingNew);
		}
 		else 
 		{
 			$aSetting = Extension::flyweight('menuediter')->setting();
 			//获得menu的相关信息
 			$sXpath='';
 			$sXpathTarget=$this->params->get('xpath');
 			$sControllerName=$this->params->get('controllerName');
 			$sViewPath=$this->params->get('viewPath');
 			$sMenuId=$this->params->get('sMenuId');
 			
 			//从setting中取得数组
 			$akey=$aSetting->key('/'.$sControllerName,true);
 			$arrSetting=$akey->item($sViewPath.$sMenuId);
 			$this->displayItem($arrSetting, $sXpath, $sXpathTarget);
 		}	
	}
	
	public function displayItem($arrSetting,$sXpath,$sXpathTarget)
	{
		foreach($arrSetting as $key=>$item)
		{	
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
			}
			if($sXpath==$sXpathTarget){
				$this->viewItemEditer->widget('title')->setValue($arrSetting['title']);
				$this->viewItemEditer->widget('depth')->setValue($arrSetting['depth']);
				$this->viewItemEditer->widget('link')->setValue($arrSetting['link']);
				$this->viewItemEditer->widget('active')->setValue($arrSetting['active']);
				return;
			}
			if(is_array($item))
			{
				$this->displayItem($item,$sXpath,$sXpathTarget);
				$sXpath=$sXpathOld;
			}
		}
	}
	
	//Menu初始化页面
// 	public function searchItem($aMenuIterator,$xpath,$xpathTarget){
		
// 		foreach($aMenuIterator as $aItem)
// 		{
// 			$xpathOld=$xpath;
// 			$xpath=$xpath.'/'.$aItem->id();
// 			if($aItem->title())
// 			{
// 				if($xpath==$xpathTarget)
// 				{
// 					$this->viewItemEditer->widget('title')->setValue($aItem->title());
// 					$this->viewItemEditer->widget('link')->setValue($aItem->link());
// 					$this->viewItemEditer->widget('active')->setValue($aItem->isActive());
// 					return;
// 				}
// 				$sTitle=$aItem->title();
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$this->searchItem($aItem->subMenu()->itemIterator(),$xpath,$xpathTarget);
// 				$xpath=$xpathOld;
// 			}
// 			else{
// 				$xpath=$xpathOld;
// 			}
// 		}
// 	}
	
	//将Menu转换成数组存放在settings中
// 	public function itemSetting($aMenuIterator,&$arrlist)
// 	{
// 		$arrI=&$arrlist;
// 		foreach($aMenuIterator as $key=>$aItem)
// 		{
// 			if($aItem->title())
// 			{
// 				$arrI=&$arrlist['item:'.$key];
// 				$arrI=array('title'=>$aItem->title(),'depth'=>$aItem->depth(),'link'=>$aItem->link(),'menu'=>$aItem->subMenu()?1:0,'active'=>$aItem->isActive());
// 				$arrI=&$arrlist;
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$arrI=&$arrI['item:'.$key];
// 				$this->itemSetting($aItem->subMenu()->itemIterator(),$arrI);
// 			}
// 		}
// 	}
	
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
}

?>