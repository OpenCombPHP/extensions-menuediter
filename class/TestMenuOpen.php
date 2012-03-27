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

class TestMenuOpen extends ControlPanel
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
						'title'=>'Menu',
						'item:A'=>array(
								'title'=>'A',
						),
						'item:B'=>array(
								'title'=>'B',
  								'menu'=>'1',
								'item:BB1'=>array(
 										'title'=>'BB1',
										'menu'=>'1',
										'item:BBB1'=>array(
											'title'=>'BBB1',
										)
  								),
								'item:BB2'=>array(
										'title'=>'BB2',
										'menu'=>'1',
										'item:BBB2'=>array(
												'title'=>'BBB2',
										),
										'item:BBB22'=>array(
												'title'=>'BBB22',
										),
								),
						),
						'item:C'=>array(
								'title'=>'C',		
						),
					),
				),
			),
		);
	}
	
	public function process()
	{	

		$arrTotal=array();
		$xpath='';
		$sViewPath='menuOpen';
		$sWidgetId='testMenu';
		$sControllerName='org\\opencomb\\menuediter\\TestMenuOpen';
		$aController = new $sControllerName();
		$aMenu=$aController->viewByName('menuOpen')->widget('testMenu');
		$aMenuIter=$aMenu->itemIterator();
		$this->itemSetting($aMenuIter,$arrTotal);
		$arrTotal['class']='menu';
		$arrTotal['id']='testMenu';
		$aMenuNew=BeanFactory::singleton()->createBean($arrTotal);
		//Menu::createBean($arrTotal, true);
		//$aArrTotald=$aMenuNew->itemIterator();
		//var_dump($aArrTotald);
		//$ssss=$this->itemItTest($aArrTotald,'');
		//$s=$this->itemIt($aMenuIter,$xpath,$sControllerName,$sViewPath,$sWidgetId);
		//var_dump($ssss);
		//$aSetting = Extension::flyweight('menuediter')->setting();
		//$akey=$aSetting->key('/'.'dd',true);
		//$akey->setItem($sViewPath.$sWidgetId,$arrTotal);
 		//$this->itemSetting($aMenuIter,$arrTotal);
	}
	
	//settings数组递归方法 Menu转换成setting
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
	
	public function itemItTest($arra,$xpath)
	{ 
		$s='<ul style=margin-left:10px>';
		//$sParentMenu='';
		foreach($arra as $aItem)
		{echo "确实参数的好坏".$aItem->title();
			$xpath2=$xpath;
			$xpath=$xpath.'/'.$aItem->id();
			$s=$s."<li xpath=\"$xpath\">";
			if($aItem->title())
			{
	
				$sTitle=$aItem->title();
				//$sParentMenu=$aItem->parentMenu()->title();
				$s=$s.'<a>'.$aItem->title().'</a>'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$xpath\">".'删除'.'</a>';
	
			}
			if($aItem->subMenu())
			{	echo "这是submenu";
				$s=$s.$this->itemItTest($aItem->subMenu()->itemIterator(),$xpath);
				$xpath=$xpath2;
	
			}
			else{
				$xpath=$xpath2;
			}
			$s=$s.'</li>';
	
		}
		$s=$s.'</ul>';
		return $s;
	}
	
	
	public function itemIt($arra,$xpath,$sControllerName,$sViewPath,$sWidgetId)
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
				
				$sTitle=$aItem->title();
				$sParentMenu=$aItem->parentMenu()->title();
				$s=$s.'<a>'.$aItem->title().'</a>'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$xpath\">".'删除'.'</a>';

			}
			if($aItem->subMenu())
			{
				$s=$s.$this->itemIt($aItem->subMenu()->itemIterator(),$xpath,$sControllerName,$sViewPath,$sWidgetId);
				$xpath=$xpath2;
				
			}
			else{
				$xpath=$xpath2;
			}
			$s=$s.'</li>';
			
		}
		$s=$s.'</ul>';
		return $s;
	}
	
	public function itemIt1($arra)
	{
		$s='<ul style=margin-left:10px>';
		$sParentMenu='';
		foreach($arra as $aItem)
		{
			$s=$s.'<li>';
			if($aItem->title())
			{
				$sTitle=$aItem->title();
				$sDepth=$aItem->depth();
				$bActive=$aItem->isActive();
				$sLink=$aItem->link();
				$sParentMenu1=$aItem->parentMenu()->title();
				$s=$s."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1\">".
				$aItem->title().'</a>'.
				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1&xpath=$sTitles\">".'删除'.'</a>'.
				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.
				'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu1\" class=\"item_edit\">".'编辑'.'</a>'.
				'&nbsp'.'&nbsp'."<a>".'向上'.'</a>'.
				'|'.'&nbsp'.'&nbsp'."<a>".'向下'.'</a>';
			}
			if($aItem->subMenu())
			{
				$s=$s.$this->itemIt2($aItem->subMenu()->itemIterator(),$sParentMenu1);
			}
			$s=$s.'</li>';
		}
		$s=$s.'</ul>';
		return $s;
	}
	
	public function itemIt2($arra,$sParentMenu1)
	{
		$s='<ul style=margin-left:10px>';
		foreach($arra as $aItem)
		{
			$s=$s.'<li>';
			if($aItem->title())
			{
				$sTitle=$aItem->title();
				$sDepth=$aItem->depth();
				$bActive=$aItem->isActive();
				$sLink=$aItem->link();
				$sParentMenu2=$sParentMenu1;
				$s=$s."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2\">".
				$aItem->title().'</a>'.
				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2&xpath=$sTitles\">".'删除'.'</a>'.
				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.
				'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&title=$sTitle
				&depth=$sDepth&active=$bActive&parentMenu=$sParentMenu2\" class=\"item_edit\">".'编辑'.'</a>'.
				'&nbsp'.'&nbsp'."<a>".'向上'.'</a>'.
				'|'.'&nbsp'.'&nbsp'."<a>".'向下'.'</a>';
			}
			if($aItem->subMenu())
			{
				$s=$s.$this->itemIt2($aItem->subMenu()->itemIterator(),$sParentMenu2);
			}
			$s=$s.'</li>';
		}
		$s=$s.'</ul>';
		return $s;
	}
	
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