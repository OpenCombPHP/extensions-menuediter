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
						'id'=>'menu_id',
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
// 		$arrSetting=array();
		
// 		$aController = new \org\opencomb\coresystem\mvc\controller\ControlPanelFrame();
// 		$sControllerName='coresystem\mvc\controller\ControlPanelFrame';
// 		$sViewPath='frameView';
// 		$sWidgetId='mainMenu';
// 		$aMenu=$aController->viewByName('frameView')->widget('mainMenu');
// 		$aMenuIterator=$aMenu->itemIterator();

//  		$sItem=$this->itemMerge($aMenuIterator,'',$sControllerName,$sViewPath,$sWidgetId);
//  		$this->itemSetting($aMenuIterator,$arrSetting);
//  		$this->viewMenuOpen->variables()->set('sItem',$sItem);
//  		$arrSetting['id']='mainMenu';
//  		$arrSetting['class']='menu';
//  		$aSetting = Extension::flyweight('menuediter')->setting();
//  		$akey=$aSetting->key('/'.$sControllerName,true);
//  		$akey->setItem($sViewPath.$sWidgetId,$arrSetting);

 		if($this->viewMenuOpen->isSubmit($this->params))
 		{
			$this->viewMenuOpen->loadWidgets($this->params);
			$sControllerName=$this->viewMenuOpen->widget('controller_name')->value();
			$sControllerName=str_replace('.','\\',$sControllerName);//echo $sControllerName;exit;
			$sViewPath=$this->viewMenuOpen->widget('viewXpath')->value();
			$sMenuId=$this->viewMenuOpen->widget('menu_id')->value();
			
			$aSetting = Extension::flyweight('menuediter')->setting();
			$akey=$aSetting->key('/'.$sControllerName,true);
			
			if($akey->hasItem($sViewPath.$sMenuId))
			{
				$sXpath='';
				$arrSetting=$akey->Item($sViewPath.$sMenuId);
				$sMenu=$this->displaySetting($arrSetting,$sXpath);
				$this->viewMenuOpen->variables()->set('sMenu',$sMenu);
			}
			else
			{
				// 检查 控制器类 是否有效
				if( !class_exists($sControllerName) or !new $sControllerName() instanceof IController)
				{	//echo $sControllerName."dd";exit;
				$skey="无此控制器";
				$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
				return;
				}
				else {
					$aController = new $sControllerName();
				}
				//$aController = new \org\opencomb\coresystem\mvc\controller\ControlPanelFrame();
				// 检查视图
				if( !$aView = View::xpath($aController->mainView(),$sViewPath))
				{
					echo $sControllerName;exit;
					$skey="无此视图";
					$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
					return;
				}
				else {
					$aView = View::xpath($aController->mainView(),$sViewPath );
				}
				//$aView = View::xpath($aController->mainView(),'frameView' );
				// 检查菜单
				if( !$aMenu=$aView->widget($sMenuId) or !$aMenu instanceof Menu)
				{
					$skey="无此菜单";
					$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
					return;
				}
				else {
					$aMenu=$aView->widget($sMenuId);
				}
				//将menu组成字符串在页面显示
				$sXpath='';
				$aMenuIterator=$aMenu->itemIterator();
				$sMenu=$this->itemMerge($aMenuIterator,$sXpath,$sControllerName,$sViewPath,$sMenuId);
				$this->viewMenuOpen->variables()->set('sMenu',$sMenu);
				//将menu遍历成数组存放在settting
				$arrSetting=array();
				$this->itemSetting($aMenuIterator,$arrSetting);
				$arrSetting['id']='mainMenu';
				$arrSetting['class']='menu';
				$aSetting = Extension::flyweight('menuediter')->setting();
				$akey=$aSetting->key('/'.$sControllerName,true);
				$akey->setItem($sViewPath.$sMenuId,$arrSetting);
			}	
 		}
	}
	
	//将BeanConfig中的Menue转换成数组存放在setting中
	public function itemSetting($aMenuIterator,&$arrSetting)
	{
		$arrI=&$arrSetting;
		foreach($aMenuIterator as $key=>$aItem)
		{
	
			if($aItem->title())
			{
				$arrI=&$arrSetting['item:'.$key];
				$arrI=array('xpath'=>$aItem->id(),'title'=>$aItem->title(),'depth'=>$aItem->depth(),'link'=>$aItem->link(),'menu'=>$aItem->subMenu()?1:0,'active'=>$aItem->isActive());
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
		$sItem='<ul style=margin-left:10px>';
		foreach($aMenuIterator as $aItem)
		{
			$sXpathOld=$sXpath;
			$sXpath=$sXpath.$aItem->id().'/';
			$sItem=$sItem."<li xpath=\"$sXpath\">";
			if($aItem->title())
			{
				$sTitle=$aItem->title();
				$sDepth=$aItem->depth();
				$bActive=$aItem->isActive();
				$sLink=substr($aItem->link(),1);
				$sItem=$sItem.'<a>'.$aItem->title().'</a>'.
				'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$sXpath&controllerName=$sControllerName&viewPath=$sViewPath&sMenuId=$sMenuId\">".'删除'.'</a>'.
				'&nbsp'.'&nbsp'."<a>".'新建'.'</a>'.'&nbsp'.'&nbsp'.'&nbsp'.
				"<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$sXpath&controllerName=$sControllerName&viewPath=$sViewPath&sMenuId=$sMenuId\">".'编辑'.'</a>';
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
	public function displaySetting($arrSetting,$sXpath)
	{
		$sMenu='<ul style=margin-left:10px>';
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
			}
			$sMenu=$sMenu."<li xpath=\"$sXpath\">";
			if($key=='title')
			{
				$sMenu=$sMenu."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$sXpath\">".$arrSetting['title'].'</a>';
			}
			if(is_array($item))
			{
				$sMenu=$sMenu.$this->displaySetting($item,$sXpath);
				$sXpath=$sXpathOld;
			}
			$sMenu=$sMenu."</li>";
		}
		$sMenu=$sMenu.'</ul>';
		return $sMenu;
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