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
							'id'=>'hide_flag_item',
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
 			if($this->viewMenuOpen->widget('hide_flag_item')->value()==1)
 			{
 				$sControllerName=$this->viewMenuOpen->widget('hide_item_controllerName')->value();
 				$sViewPath=$this->viewMenuOpen->widget('hide_item_viewPath')->value();
 				$sMenuId=$this->viewMenuOpen->widget('hide_item_menuId')->value();
 				
 				$aSetting = Extension::flyweight('menuediter')->setting();
 				$akey=$aSetting->key('/'.$sControllerName,true);
 				
 				

 				if($aSetting->hasItem('/'.$sControllerName,$sViewPath.$sMenuId))
 				{		$sXpathFrom=$this->viewMenuOpen->widget('hide_item_xpath')->value();//echo $sXpathFrom;exit;
 						$sXpathOption=$this->params->get('xpathOption');
 						$arrToXpath=explode('/',$sXpathFrom);
 						array_pop($arrToXpath);var_dump($arrToXpath);
 						$arrSettingOld=array();
 						$arrSettingDelete=array();
 						$arrSettingChild=array();
 						$arrItemSettingMiddle=array();
 						$arrSettingNew=array();

 						$arrSettingOld=$akey->item($sViewPath.$sMenuId);
 						$arrSettingDelete=$arrSettingOld;
 						
 						
 						
 						//$this->settingItemdelete(0,$arrSettingOld, '', $arrToXpath, $arrSettingDelete);
 						$this->settingItemdelete($arrSettingDelete, $arrToXpath);

 						$this->itemSettingEdit($arrSettingOld, '', $sXpathFrom, $arrItemSettingMiddle,$arrSettingChild);

 						$this->settingEditXpathOption($arrSettingDelete, '', $sXpathOption,$sXpathFrom, $arrSettingNew,$arrSettingChild);
 						$akey->deleteItem($sViewPath.$sMenuId);
 						$akey->setItem($sViewPath.$sMenuId,$arrSettingNew);
 						echo "<pre>";
 						print_r($arrSettingNew);
 						echo "</pre>";exit;

//  					$sTitle=$this->viewItemEditer->widget('title')->value();
//  					$sDepth=$this->viewItemEditer->widget('depth')->value();
//  					$sLink=$this->viewItemEditer->widget('link')->value();
//  					$sActive=$this->viewItemEditer->widget('active')->value();
//  					$arrItem=array();
//  					$sXpath='';
//  					$sXpathTarget=$this->params->get('xpath');
//  					$arrItem=array('title'=>$sTitle,'depth'=>$sDepth,'link'=>$sLink,'active'=>$sActive);
//  					$akey=$aSetting->key('/'.$sControllerName,true);
//  					$arrSettingOld=$akey->item($sViewPath.$sMenuId);
//  					$arrSettingNew=array();
//  					$this->settingEdit($arrSettingOld,$arrItem,$sXpath,$sXpathTarget,$arrSettingNew);
//  					$akey->deleteItem($sViewPath.$sMenuId);
//  					$akey->setItem($sViewPath.$sMenuId,$arrSettingNew);
 				}
 				else {
 					$sXpathOption=$this->params->get('xpathOption');
 					if($sXpathOption==$this->viewMenuOpen->widget('hide_item_xpath')->value())
 					{
 							;	
 					}else {
 						$sXpathFrom=$this->viewMenuOpen->widget('hide_item_xpath')->value();
 						$sXpathOption=$this->params->get('xpathOption');
 						$arrToXpath=explode('/',$sXpathOption);
 						array_pop($arrToXpath);
 						$arrSettingOld=array();
 						$arrSettingDelete=array();
 						$arrSettingChild=array();
 						$arrItemSettingMiddle=array();
 						$arrSettingNew=array();
 						
 						$aController = new $sControllerName();
 						$aView = View::xpath($aController->mainView(),$sViewPath );
 						$aMenu=$aView->widget($sMenuId);
 						$aMenuIterator=$aMenu->itemIterator();
 						$this->itemSetting($aMenuIterator,$arrSettingOld);
 						$this->settingItemdelete(0,$arrSettingOld, '', $arrToXpath, $arrSettingDelete);
 						var_dump($arrSettingDelete);
 						$this->itemSettingEdit($arrSettingOld, '', $sXpathFrom, $arrItemSettingMiddle,$arrSettingChild);
 						
 						$this->settingEditXpathOption($arrSettingDelete, '', $sXpathOption,$sXpathFrom, $arrSettingNew,$arrSettingChild);
 						
 					}
//  					$arrSettingOld=array();
//  					$aController = new $sControllerName();
//  					$aView = View::xpath($aController->mainView(),$sViewPath );
//  					$aMenu=$aView->widget($sMenuId);
//  					$aMenuIterator=$aMenu->itemIterator();
//  					$this->itemSetting($aMenuIterator,$arrSettingOld);
//  					$this->itemSetting($aMenuIterator,$arrSettingOld);
//  					$arrSettingNew=array();
//  					$this->settingEdit($arrSettingOld,$arrItem,$sXpath,$sXpathTarget,$arrSettingNew);
//  					$akey->deleteItem($sViewPath.$sMenuId);
//  					$akey->setItem($sViewPath.$sMenuId,$arrSettingNew);
 				}
 				
 			}else
 			{
 				$sControllerName=$this->viewMenuOpen->widget('controller_name')->value();
 				$sControllerName=str_replace('.','\\',$sControllerName);
 				$sViewPath=$this->viewMenuOpen->widget('viewXpath')->value();
 				$sMenuId=$this->viewMenuOpen->widget('menu_id')->value();
 				
 				$aSetting = Extension::flyweight('menuediter')->setting();
 				$akey=$aSetting->key('/'.$sControllerName,true);
 				
 				if($aSetting->hasItem('/'.$sControllerName,$sViewPath.$sMenuId))
 				{
 					$arrJson=array();
 					$arrXpath=array();
 					$sXpath='';
 					$arrSetting=$akey->Item($sViewPath.$sMenuId);
 					$sMenu=$this->displaySetting($arrSetting,$sXpath);
 					$this->viewMenuOpen->variables()->set('sMenu',$sMenu);
 					$arrJson['controllername']=$sControllerName;
 					$arrJson['viewpath']=$sViewPath;
 					$arrJson['menuid']=$sMenuId;
 					$this->jsonSetting($arrSetting, $sXpath, $arrJson);
 					$this->viewMenuOpen->variables()->set('sJsonSetting',json_encode($arrJson));
 					$this->xpathOption($arrSetting,'','',0,$arrXpath);
 					$this->viewMenuOpen->variables()->set('arrXpath',$arrXpath);
 				}
 				else {
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
 					if( !$aView = View::xpath($aController->mainView(),$sViewPath))
 					{
 						$skey="无此视图";
 						$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
 						return;
 					}else {
 						$aView = View::xpath($aController->mainView(),$sViewPath );
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
 					$arrJson['controllername']=$sControllerName;
 					$arrJson['viewpath']=$sViewPath;
 					$arrJson['menuid']=$sMenuId;
 					$this->jsonSetting($arrSetting, $sXpath, $arrJson);
 					$this->viewMenuOpen->variables()->set('sJsonSetting',json_encode($arrJson));
 					$this->xpathOption($arrSetting,'','',0,$arrXpath);
 					$this->viewMenuOpen->variables()->set('arrXpath',$arrXpath);
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
		$sItem='<ul style=margin-left:10px>';
		foreach($aMenuIterator as $aItem)
		{	
			$sXpathOld=$sXpath;
			$sXpath=$sXpath.'Item:'.$aItem->id().'/';
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
				"<a href=\"#\" onclick=\"javascript: itemEdit('$sXpath')\">".'编辑'.'</a>';
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
				$sMenu=$sMenu."<a>".$arrSetting['title'].'</a>'.'&nbsp'.'&nbsp'.'&nbsp'.
						"<a href=\"#\" onclick=\"javascript: itemEdit('$sXpath')\">".'编辑'.'</a>';
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
					var_dump($arrItemSettingNew);echo "<br/>"."<br/>"."<br/>";
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
	
	public function settingEditXpathOption($arrSetting,$sXpath,$sXpathTarget,$sXpathFirst,&$arrSettingNew,$arrSettingChild){
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