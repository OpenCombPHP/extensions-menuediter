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
						'id'=>'widget_id',
						'class'=>'text',
						'title'=>'控件ID'
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
		$arrTotal=array();
		$aController = new \org\opencomb\coresystem\mvc\controller\ControlPanelFrame();
		$sControllerName='coresystem\mvc\controller\ControlPanelFrame';
		$sViewPath='frameView';
		$sWidgetId='mainMenu';
		$aMenu=$aController->viewByName('frameView')->widget('mainMenu');
		$aMenuIter=$aMenu->itemIterator();

 		$sItem=$this->itemMerge($aMenuIter,'',$sControllerName,$sViewPath,$sWidgetId);
 		$this->itemSetting($aMenuIter,$arrTotal);
 		$this->viewMenuOpen->variables()->set('sItem',$sItem);
 		$arrTotal['id']='mainMenu';
 		$arrTotal['class']='menu';
 		$aSetting = Extension::flyweight('menuediter')->setting();
 		$akey=$aSetting->key('/'.$sControllerName,true);
 		$akey->setItem($sViewPath.$sWidgetId,$arrTotal);

 		if($this->viewMenuOpen->isSubmit($this->params))
 		{
			$this->viewMenuOpen->loadWidgets($this->params);
			$sControllerName=$this->viewMenuOpen->widget('controller_name')->value();
			$sControllerName='\\org\\opencomb\\'.str_replace('.','\\',$sControllerName);
			$sViewPath=$this->viewMenuOpen->widget('viewXpath')->value();
			$sWidgetId=$this->viewMenuOpen->widget('widget_id')->value();
			// 检查 控制器类 是否有效
			if( !class_exists($sControllerName) or !$sControllerName instanceof IController )
			{
				$skey="无此控制器";
				$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
				//return;
			}
			else {
				$aController = new $sControllerName();
			}
			$aController = new \org\opencomb\coresystem\mvc\controller\ControlPanelFrame();
			// 检查视图
			if( !$aView = View::xpath($aController->mainView(),$sViewPath))
			{
				$skey="无此视图";
				$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
				//return;
			}
			else {
				$aView = View::xpath($aController->mainView(),'$sViewPath' );
			}
			$aView = View::xpath($aController->mainView(),'frameView' );
			// 检查菜单
			if( !$aWidget=$aView->widget($sWidgetId) or $aWidget instanceof Menu)
			{
				$skey="无此菜单";
				$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
				//return;
			}
			else {
				$aWidget=$aView->widget($sWidgetId);
			}//$arrI=&$arrlist[$key]
			//$aWidget=$aView->widget('mainMenu');
			//$aMenuIter=$aWidget->itemIterator();
			//$aMenu=$aController->viewByName('frameView')->widget('mainMenu');
 			//$aMenuIter=$aWidget->itemIterator();
 			//$sMenu=$this->itemIt($aMenuIter);
 			//var_dump($sMenu);
 			//$this->viewMenuOpen->variables()->set('sMenu',$sMenu);
 		}
	}
	
	//settings数组递归方法
	public function itemSetting($arra,&$arrlist)
	{
		$arrI=&$arrlist;
		foreach($arra as $key=>$aItem)
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
	
	public function itemMerge($arra,$xpath,$sControllerName,$sViewPath,$sWidgetId)
	{
		$sItem='<ul style=margin-left:10px>';
		foreach($arra as $aItem)
		{
			$xpathOld=$xpath;
			$xpath=$xpath.'/'.$aItem->id();
			$sItem=$sItem."<li xpath=\"$xpath\">";
			if($aItem->title())
			{
				$sTitle=$aItem->title();
				$sDepth=$aItem->depth();
				$bActive=$aItem->isActive();
				$sLink=substr($aItem->link(),1);
				$sItem=$sItem.'<a>'.$aItem->title().'</a>'.
				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$xpath&controllerName=$sControllerName&viewPath=$sViewPath&sMenuId=$sWidgetId\">".'删除'.'</a>'.
				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.'&nbsp'.'&nbsp'.'&nbsp'.
				"<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$xpath&controllerName=$sControllerName&viewPath=$sViewPath&sMenuId=$sWidgetId\">".'编辑'.'</a>';
			}
			if($aItem->subMenu())
			{
				$sItem=$sItem.$this->itemMerge($aItem->subMenu()->itemIterator(),$xpath,$sControllerName,$sViewPath,$sWidgetId);
				$xpath=$xpathOld;
			}
			else
			{
				$xpath=$xpathOld;
			}
			$sItem=$sItem.'</li>';
		}
		$sItem=$sItem.'</ul>';
		return $sItem;
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
	
// 	public function itemItss($arra)
// 	{
// 		foreach($arra as $key=>$aItem)
// 		{
// 			if($aItem->title())
// 			{
// 				$sTitle=$sTitle.$aItem->title();
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$this->itemItss($aItem->subMenu()->itemIterator(),$sTitle);
// 			}
// 		}
// 	}
	
// 	public function itemIt1($arra)
// 	{
// 		$s='<ul style=margin-left:10px>';
// 		$sParentMenu='';
// 		foreach($arra as $aItem)
// 		{
// 			$s=$s.'<li>';
// 			if($aItem->title())
// 			{
// 				$sTitle=$aItem->title();
// 				$sDepth=$aItem->depth();
// 				$bActive=$aItem->isActive();
// 				$sLink=$aItem->link();
// 				$sParentMenu1=$aItem->parentMenu()->title();
// 				$s=$s."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1\">".
// 				$aItem->title().'</a>'.
// 				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1&xpath=$sTitles\">".'删除'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1\" class=\"item_edit\">".'编辑'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'向上'.'</a>'.
// 				'|'.'&nbsp'.'&nbsp'."<a>".'向下'.'</a>';
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$s=$s.$this->itemIt2($aItem->subMenu()->itemIterator(),$sParentMenu1);
// 			}
// 			$s=$s.'</li>';
// 		}
// 		$s=$s.'</ul>';
// 		return $s;
// 	}
	
// 	public function itemIt2($arra,$sParentMenu1)
// 	{
// 		$s='<ul style=margin-left:10px>';
// 		foreach($arra as $aItem)
// 		{
// 			$s=$s.'<li>';
// 			if($aItem->title())
// 			{
// 				$sTitle=$aItem->title();
// 				$sDepth=$aItem->depth();
// 				$bActive=$aItem->isActive();
// 				$sLink=$aItem->link();
// 				$sParentMenu2=$sParentMenu1;
// 				$s=$s."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2\">".
// 				$aItem->title().'</a>'.
// 				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2&xpath=$sTitles\">".'删除'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2\" class=\"item_edit\">".'编辑'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'向上'.'</a>'.
// 				'|'.'&nbsp'.'&nbsp'."<a>".'向下'.'</a>';
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$s=$s.$this->itemIt2($aItem->subMenu()->itemIterator(),$sParentMenu2);
// 			}
// 			$s=$s.'</li>';
// 		}
// 		$s=$s.'</ul>';
// 		return $s;
// 	}
	
// 	public function itemIt($arra)
// 	{
// 		$s='<ul style=margin-left:10px>';
// 		$sParentMenu='';
// 		foreach($arra as $aItem)
// 		{
// 			$s=$s.'<li>';
// 			if($aItem->title())
// 			{	
// 				$sTitle=$aItem->title();
// 				$sDepth=$aItem->depth();
// 				$bActive=$aItem->isActive();
// 				$sLink=$aItem->link();
// 				$sParentMenu=$aItem->parentMenu()->title();
// 				$s=$s."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu\">".
// 				$aItem->title().'</a>'.
// 				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu&xpath=\">".'删除'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu\" class=\"item_edit\">".'编辑'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'向上'.'</a>'.
// 				'|'.'&nbsp'.'&nbsp'."<a>".'向下'.'</a>';
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$s=$s.$this->itemIt1($aItem->subMenu()->itemIterator());
// 			}
// 			$s=$s.'</li>';
// 		}
// 		$s=$s.'</ul>';
// 		return $s;
// 	}
	
// 	public function itemIt1($arra)
// 	{
// 		$s='<ul style=margin-left:10px>';
// 		$sParentMenu='';
// 		foreach($arra as $aItem)
// 		{
// 			$s=$s.'<li>';
// 			if($aItem->title())
// 			{
// 				$sTitle=$aItem->title();
// 				$sDepth=$aItem->depth();
// 				$bActive=$aItem->isActive();
// 				$sLink=$aItem->link();
// 				$sParentMenu1=$aItem->parentMenu()->title();
// 				$s=$s."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1\">".
// 				$aItem->title().'</a>'.
// 				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1\">".'删除'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1\" class=\"item_edit\">".'编辑'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'向上'.'</a>'.
// 				'|'.'&nbsp'.'&nbsp'."<a>".'向下'.'</a>';
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$s=$s.$this->itemIt2($aItem->subMenu()->itemIterator(),$sParentMenu1);
// 			}
// 			$s=$s.'</li>';
// 		}
// 		$s=$s.'</ul>';
// 		return $s;
// 	}
	
// 	public function itemIt2($arra,$sParentMenu1)
// 	{
// 		$s='<ul style=margin-left:10px>';
// 		foreach($arra as $aItem)
// 		{
// 			$s=$s.'<li>';
// 			if($aItem->title())
// 			{
// 				$sTitle=$aItem->title();
// 				$sDepth=$aItem->depth();
// 				$bActive=$aItem->isActive();
// 				$sLink=$aItem->link();
// 				$sParentMenu2=$sParentMenu1;
// 				$s=$s."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2\">".
// 				$aItem->title().'</a>'.
// 				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2\">".'删除'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
// 				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2\" class=\"item_edit\">".'编辑'.'</a>'.
// 				'&nbsp'.'&nbsp'."<a>".'向上'.'</a>'.
// 				'|'.'&nbsp'.'&nbsp'."<a>".'向下'.'</a>';
// 			}
// 			if($aItem->subMenu())
// 			{
// 				$s=$s.$this->itemIt2($aItem->subMenu()->itemIterator(),$sParentMenu2);
// 			}
// 			$s=$s.'</li>';
// 		}
// 		$s=$s.'</ul>';
// 		return $s;
// 	}
}

?>