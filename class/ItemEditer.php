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
			$arrMenulist=array();
			//读取Menu参数
			$this->viewItemEditer->loadWidgets ( $this->params );
			$sTitle=$this->viewItemEditer->widget('title')->value();
			$sDepth=$this->viewItemEditer->widget('depth')->value();
			$sLink=$this->viewItemEditer->widget('link')->value();
			$sActive=$this->viewItemEditer->widget('active')->value();
			
			$sControllerName=$this->params->get('controllerName');
			$sViewPath=$this->params->get('viewPath');
			$sMenuId=$this->params->get('sMenuId');
			$xpathTarget=$this->params->get('xpath');
// 			$sControllerName=$this->viewItemEditer->widget('hide_controllername')->value();
// 			$sViewPath=$this->viewItemEditer->widget('hide_viewpath')->value();
// 			$sWidgetId=$this->viewItemEditer->widget('hide_widgetid')->value();

 			$aSetting = Extension::flyweight('menuediter')->setting();
 			//获得menu的相关信息
 			$sXpath=$this->params->get('xpath');
 			$sControllerName=$this->params->get('controllerName');
 			$sViewPath=$this->params->get('viewPath');
 			$sMenuId=$this->params->get('sMenuId');
 			//从setting中取得数组创建Menu
 			$akey=$aSetting->key('/'.$sControllerName,true);
 			$arrMenuSetting=$akey->item($sViewPath.$sMenuId);
 			//var_dump($arrMenuSetting);
 			$aMenu=BeanFactory::singleton()->createBean($arrMenuSetting);
 			$aItem=$aMenu->getItemByPath(substr($sXpath,1));
 			//var_dump($aItem);
 			$aItem->setLink($this->viewItemEditer->widget('link')->value());
 			$aMenuIterator=$aMenu->itemIterator();
 			$arrlist=array();
 			$this->itemSetting($aMenuIterator, $arrlist);
 			$akey->deleteItem($sViewPath.$sMenuId);
 			$arrlist['class']='menu';
 			$arrlist['id']=$sViewPath;
 			$akey->setItem($sViewPath.$sMenuId,$arrlist);
		}
 		else 
 		{
 			$aSetting = Extension::flyweight('menuediter')->setting();
 			//获得menu的相关信息
 			$sXpath=$this->params->get('xpath');
 			$sControllerName=$this->params->get('controllerName');
 			$sViewPath=$this->params->get('viewPath');
 			$sMenuId=$this->params->get('sMenuId');
 			//从setting中取得数组创建Menu
 			$akey=$aSetting->key('/'.$sControllerName,true);
 			$arrMenuSetting=$akey->item($sViewPath.$sMenuId);
 			//var_dump($arrMenuSetting);
 			$aMenu=BeanFactory::singleton()->createBean($arrMenuSetting);
			//遍历Menu,初始化页面	
 			$aMenuIterator=$aMenu->itemIterator();
 			$this->searchItem($aMenuIterator,'',$sXpath);
 		}	
	}
	
	//Menu初始化页面
	public function menuEdit(&$aMenuIterator,$xpath,$xpathTarget){
	
		foreach($aMenuIterator as $aItem)
		{
			$xpathOld=$xpath;
			$xpath=$xpath.'/'.$aItem->id();
			if($aItem->title())
			{
				if($xpath==$xpathTarget)
				{
					$aItem->setLink($this->viewItemEditer->widget('link')->setValue($aItem->link()));
				}
				$sTitle=$aItem->title();
			}
			if($aItem->subMenu())
			{
				$this->menuEdit($aItem->subMenu()->itemIterator(),$xpath,$xpathTarget);
				$xpath=$xpathOld;
			}
			else{
				$xpath=$xpathOld;
			}
		}
	}
	
	//Menu初始化页面
	public function searchItem($aMenuIterator,$xpath,$xpathTarget){
		
		foreach($aMenuIterator as $aItem)
		{
			$xpathOld=$xpath;
			$xpath=$xpath.'/'.$aItem->id();
			if($aItem->title())
			{
				if($xpath==$xpathTarget)
				{
					$this->viewItemEditer->widget('title')->setValue($aItem->title());
					$this->viewItemEditer->widget('link')->setValue($aItem->link());
					$this->viewItemEditer->widget('active')->setValue($aItem->isActive());
					return;
				}
				$sTitle=$aItem->title();
			}
			if($aItem->subMenu())
			{
				$this->searchItem($aItem->subMenu()->itemIterator(),$xpath,$xpathTarget);
				$xpath=$xpathOld;
			}
			else{
				$xpath=$xpathOld;
			}
		}
	}
	
	//将Menu转换成数组存放在settings中
	public function itemSetting($aMenuIterator,&$arrlist)
	{
		$arrI=&$arrlist;
		foreach($aMenuIterator as $key=>$aItem)
		{
			if($aItem->title())
			{
				$arrI=&$arrlist['item:'.$key];
				$arrI=array('title'=>$aItem->title(),'depth'=>$aItem->depth(),'link'=>$aItem->link(),'menu'=>$aItem->subMenu()?1:0,'active'=>$aItem->isActive());
				$arrI=&$arrlist;
			}
			if($aItem->subMenu())
			{
				$arrI=&$arrI['item:'.$key];
				$this->itemSetting($aItem->subMenu()->itemIterator(),$arrI);
			}
		}
	}
	
	//Menu遍历
	public function itemIt($arra,$xpath,$arrItem,$xpath3,$sControllerName,$sViewPath,$sWidgetId)
	{
		$s='<ul style=margin-left:10px>';
		$sParentMenu='';
		foreach($arra as $aItem)
		{
			$xpath2=$xpath;
			$xpath=$xpath.'/'.$aItem->id();
			$s=$s."<li xpath=\"$xpath\">";
			if($aItem->title())
			{
				if($xpath==$xpath3)
				{
					$arrItem['title'];
					$arrItem['depth'];
					$arrItem['active'];
					$arrItem['link'];
					
				}else
				{
					$sTitle=$aItem->title();
					$sDepth=$aItem->depth();
					$bActive=$aItem->isActive();
					$sLink=substr($aItem->link(),1);
					$sItemId=$aItem->id();
					$sParentMenu=$aItem->parentMenu()->title();
					$s=$s."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
					&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu\">".
					$aItem->title().'</a>'.
					'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$xpath&itemid=$sItemId&parentMenu=$sParentMenu&controllerName=$sControllerName&viewPath=$sViewPath&sMenuId=$sWidgetId\">".'删除'.'</a>'.
					'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.
					'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
					&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu\" class=\"item_edit\">".'编辑'.'</a>'.
					'&nbsp'.'&nbsp'."<a>".'向上'.'</a>'.
					'|'.'&nbsp'.'&nbsp'."<a>".'向下'.'</a>';
				}
			}
			if($aItem->subMenu())
			{
				$s=$s.$this->itemIt($aItem->subMenu()->itemIterator(),$xpath,$xpath3,$sControllerName,$sViewPath,$sWidgetId);
				$xpath=$xpath2;
	
			}
			else
			{
				$xpath=$xpath2;
			}
			$s=$s.'</li>';
	
		}
		$s=$s.'</ul>';
		return $s;
	}
	
	
// 	public function searchItem($arrMenu,$sItemId)
// 	{
// 		foreach($arrMenu as $key=>$item)
// 		{
// 			if($key==$sItemId)
// 			{
// 				$this->viewItemEditer->widget('title')->setValue($arrMenu[$key]['title']);
// 				$this->viewItemEditer->widget('depth')->setValue($arrMenu[$key]['depth']);
// 				$this->viewItemEditer->widget('link')->setValue($arrMenu[$key]['link']);
// 				$this->viewItemEditer->widget('active')->setValue($arrMenu[$key]['active']);
// 				return;
// 			}
// 			elseif(is_array($item))
// 			{
// 				$this->searchItem($item,$sItemId);
// 			}
// 		}
// 	}
	
// 	public function itemIt($arra,&$arrlist,$sTitle,$sDepth,$sLink,$sActive,$sHideItem)
// 	{
// 		$arrI=&$arrlist;
// 		foreach($arra as $key=>$aItem)
// 		{
	
// 			if($aItem->title())
// 			{

// 				if($aItem->id()==$sHideItem)
// 				{
// 					$arrI=&$arrlist[$key];
// 					$arrI=array('title'=>$sTitle,'link'=>$sLink,'active'=>$sActive,'depth'=>$sDepth,'children'=>array());
// 					$arrI=&$arrlist;
// 				}
// 				else {
// 					$arrI=&$arrlist[$key];
// 					$arrI=array('title'=>$aItem->title(),'link'=>$aItem->link(),'active'=>$aItem->isActive(),'depth'=>$aItem->depth(),'children'=>array());
// 					$arrI=&$arrlist;
// 				}	
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$arrI=&$arrI[$key]['children'];
// 				$this->itemIt($aItem->subMenu()->itemIterator(),$arrI,$sTitle,$sDepth,$sLink,$sActive,$sHideItem);
// 			}
// 		}
// 	}
	
// 	public function arrIt($arra)
// 	{
// 		foreach($arra as $key=>$value)
// 		{
// 			if($key=="testSub1")
// 			{
// 				$this->arrIt($value);
// 			}
// 			else {
// 				continue;
// 			}
// 			if($arra['title']=='testSub11' and $arra['depth']==2)
// 			{
// 				echo "找到了";
// 			}
// 		}	
		
// 	}
	
}

?>