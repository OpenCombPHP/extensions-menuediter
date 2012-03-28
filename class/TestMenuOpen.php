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

// 		$arrTest =array('a'=>1,'b'=>2,'c'=>array('d'=>9));
// // 		foreach($arrTest as $key=>$item)
// // 		{
			
// // 			$arrTest[$key]=6;
// // 			if(is_array($item))
// // 			{
// // 				$this->xunhuan($item);
// // 			}
// // 		};
// 		$this->xunhuan($arrTest);
// 		var_dump($arrTest);
 		
// 		$xpath='';
 		$sViewPath='menuOpen';
 		$sWidgetId='testMenu';
 		//存放setting
 		
 		$arrTotal=array();
		$sControllerName='org\\opencomb\\menuediter\\TestMenuOpen';
		$aController = new $sControllerName();
		$aMenu=$aController->viewByName('menuOpen')->widget('testMenu');
		$aMenuIter=$aMenu->itemIterator();
 		$arrTotal['class']='menu';
 		$arrTotal['id']='testMenu';
 		$this->itemSetting($aMenuIter,$arrTotal);
 		$aSetting = Extension::flyweight('menuediter')->setting();
 		$akey=$aSetting->key('/'.'dd',true);
 		$akey->setItem($sViewPath.$sWidgetId,$arrTotal);
 		
// 		//$aMenuNew=BeanFactory::singleton()->createBean($arrTotal);
// 		//Menu::createBean($arrTotal, true);
// 		//$aArrTotald=$aMenuNew->itemIterator();
// 		//var_dump($aArrTotald);
// 		//$ssss=$this->itemItTest($aArrTotald,'');
// 		//$s=$this->itemIt($aMenuIter,$xpath,$sControllerName,$sViewPath,$sWidgetId);
// 		//var_dump($ssss);

	
		
		//var_dump($this->displaySetting($arrTotal,$xpath1));
 		//$this->itemSetting($aMenuIter,$arrTotal);
 		//$arrXpath=explode('/','item:B/item:BB2');
		$xpath='';
 		$xpathTarget='item:B/item:BB2/';
 		$aSetting = Extension::flyweight('menuediter')->setting();
 		$akey=$aSetting->key('/'.'dd',true);
 		$arrSetting=$akey->item($sViewPath.$sWidgetId);
 		
		$arrSettingNew=array();
		$this->settingEdit($arrSetting,'',$xpathTarget,$arrSettingNew);
		var_dump($arrSettingNew);
		//$this->arrMerge($arrSetting,$xpath1,'item:B/item:BB2/',$arrlist);
		//var_dump($arrlist);
		//$this->arrMerge($arrSetting,$arrlist,$arrXpath[0]);
		$aMenu=BeanFactory::singleton()->createBean($arrSettingNew);
		$arra=$aMenu->itemIterator();
		var_dump($this->itemMerge($arra));
	}
	
	public function settingEdit($arrSetting,$xpath,$xpathTarget,&$arrSettingNew){
		foreach($arrSetting as $key=>$item)
		{
			$xpathOld=$xpath;
			if($key=='xpath'){
				$xpath=$xpath.$arrSetting['xpath'].'/';
			}
			
			if($xpath==$xpathTarget){
				$arrSettingNew['title']="成功";
			}
			else 
			{	
				$arrSettingNew[$key]=$arrSetting[$key];
			}
			
			if(is_array($arrSetting[$key]))
			{	//exit;
				$this->settingEdit($arrSetting[$key],$xpath,$xpathTarget,$arrSettingNew[$key]);
				$xpath=$xpathOld;
			}
		}
	}
	
	public function itemMerge($arra)
	{
		$sItem='<ul style=margin-left:10px>';
		foreach($arra as $aItem)
		{
			$sItem=$sItem."<li>";
			if($aItem->title())
			{
				$sTitle=$aItem->title();
				$sDepth=$aItem->depth();
				$bActive=$aItem->isActive();
				$sLink=substr($aItem->link(),1);
				$sItem=$sItem.'<a>'.$aItem->title().'</a>';		
			}
			if($aItem->subMenu())
			{
				$sItem=$sItem.$this->itemMerge($aItem->subMenu()->itemIterator());
			}
			$sItem=$sItem.'</li>';
		}
		$sItem=$sItem.'</ul>';
		return $sItem;
	}
	
	public function marge1($arrSetting,$arrlist,$arrXpath)
	{
		foreach($arrSetting as $key=>$item)
		{
			if($key=='title')
			{
				if($arrSetting['depth']==$arrXpath[$i=0]){
					$this->marge1();
				};
			}
		}
	}
	
// 	public function xunhuan($arrTest)
// 	{
// 		if(is_array($arrTest))
// 		{
// 			foreach($arrTest as $key=>$item)
// 			{
// 				$this->xunhuan($arrTest[$key]);
// 			}
// 		}
// 		else
// 		{
// 			$arrTest['a']=100;
// 		}
// 		return $arrTest;
// 	}
	
	public function arrMerge($arrSetting,$xpath,$xpathTarget,&$arrlist){
		foreach($arrSetting as $key=>$item)
		{	
			$xpathOld=$xpath;
			if($key=='xpath'){
				$xpath=$xpath.$arrSetting['xpath'].'/';
			}
			
			if($xpath==$xpathTarget)
			{
				if($key=='title')
				{	echo "chenggong";
					$arrI=&$arrlist[$arrSetting['xpath']];
					$arrI=array('xpath'=>'成功','title'=>'成功');
					$arrI=&$arrlist;
				}

				if(is_array($item))
				{
					$arrI=&$arrlist[$item['xpath']];
					$this->arrMerge($item,$xpath,$xpathTarget,$arrI);
					$xpath=$xpathOld;
				}	
			}
			else 
			{
				if($key=='title')
				{	
					$arrI=&$arrlist[$arrSetting['xpath']];
					$arrI=array('xpath'=>$arrSetting['xpath'],'title'=>$arrSetting['title']);
					$arrI=&$arrlist;
				}
				
				if(is_array($item))
				{
					$arrI=&$arrlist[$item['xpath']];
					$this->arrMerge($item,$xpath,$xpathTarget,$arrI);
					$xpath=$xpathOld;
				}
			}
		}
	}
	
// 	public function settingEdit($arrSetting,$xpath,$xpathTarget)
// 	{
// 		foreach($arrSetting as $key=>$item)
// 		{
// 			$xpathOld=$xpath;
// 			if($key=='xpath'){
// 				$xpath=$xpath.$arrSetting['xpath'].'/';
// 				if($xpath==$xpathTarget)
// 				{	echo "sdfsdfsdfsdfddddddddd";
// 					if($key=='title'){
// 					$arrSetting[$key]="成功";var_dump($arrSetting);break;
// 					}
					
// 				}
// 			}
// 			if(is_array($item))
// 			{
// 				$this->settingEdit($item,$xpath,$xpathTarget);
// 				$xpath=$xpathOld;
// 			}
// 		}
// 		//return $arrSetting;
// 	}
	
	//settings数组递归方法 Menu转换成setting
	public function itemSetting($arra,&$arrlist)
	{
		$arrI=&$arrlist;
		foreach($arra as $key=>$aItem)
		{	
			
			if($aItem->title())
			{	
				$arrI=&$arrlist['item:'.$key];
				$arrI=array('xpath'=>'item:'.$aItem->id(),'title'=>$aItem->title(),'depth'=>$aItem->depth(),'link'=>$aItem->link(),'menu'=>$aItem->subMenu()?1:0,'active'=>$aItem->isActive());
				$arrI=&$arrlist;
			}
			if($aItem->subMenu())
			{
				$arrI=&$arrI['item:'.$key];
				$this->itemSetting($aItem->subMenu()->itemIterator(),$arrI);
			}
		}
	}
	
	//从setting中读取Menu并显示
	public function displaySetting($arrSetting,$xpath)
	{
		$sMenu='<ul style=margin-left:10px>';
		foreach($arrSetting as $key=>$item)
		{
			$xpathOld=$xpath;
			if($key=='xpath'){
				$xpath=$xpath.$arrSetting['xpath'].'/';
			}
			$sMenu=$sMenu."<li>";
			if($key=='title')
			{
				$sMenu=$sMenu."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$xpath\">".$arrSetting['title'].'</a>';
			}
			if(is_array($item))
			{	
				$sMenu=$sMenu.$this->displaySetting($item,$xpath);
				$xpath=$xpathOld;
			}
			$sMenu=$sMenu."</li>";
		}
		$sMenu=$sMenu.'</ul>';
		return $sMenu;
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