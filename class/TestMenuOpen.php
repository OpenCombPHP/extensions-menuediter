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
			'view:testMenuOpen'=>array(
				'template'=>'TestMenuOpen.html',
				'class'=>'form',
				'widgets' => array(
					array(
						'id'=>'create_item_id',	
						'class'=>'text',	
					),	
					array(
						'id'=>'controller_name',
						'class'=>'text',
						'title'=>'控制器'		
					),
					array(
							'id'=>'controller_name1',
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
								'id'=>'hide_item_create_xpath',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'编辑项menuId'
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
										'title'=>'BB1',
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
		//创建setting
// 		$sXpath='';
// 		$arrSettingOld=array();
// 		$sControllerName='org\opencomb\menuediter\TestMenuOpen';
// 		$sViewPath='testMenuOpen';
// 		$sMenuId='testMenu';
//  		$aController = new $sControllerName();
//  		$aView = View::xpath($aController->mainView(),$sViewPath );
//  		$aMenu=$aView->widget($sMenuId);
//  		$aMenuIterator=$aMenu->itemIterator();
//  		$sMenu=$this->itemMerge($aMenuIterator,$sXpath,$sControllerName,$sViewPath,$sMenuId);
//  		$this->itemSetting($aMenuIterator,$arrSettingOld);
//  		$this->viewTestMenuOpen->variables()->set('sMenu',$sMenu);
// //  		if('item:B'=='item:B')
// //  		{
// //  			exit;
// //  		}
//  		if($this->viewTestMenuOpen->isSubmit($this->params))
//  		{
//  			$arrSettingNew=array();
//  			$this->viewTestMenuOpen->loadWidgets($this->params);
//  			$sItemId=$this->viewTestMenuOpen->widget('create_item_id')->value();
//  			$sXpathTo=$this->viewTestMenuOpen->widget('hide_item_create_xpath')->value();
//  			$sItemId=$sXpathTo.$sItemId.'/';
//  			$bflag=false;
//  			if($this->idSearch($arrSettingOld,$sXpath,$sItemId,$bflag))
//  			{
//  				$skey="项目ID重复";
//  				$this->viewMenuOpen->createMessage(Message::error,"%s ",$skey);
//  				return;
//  			}
//  			$arrItemChild=array(
//  				'title'=>$this->viewTestMenuOpen->widget('create_title')->value(),
//  				'xpath'=>$this->viewTestMenuOpen->widget('create_item_id')->value(),
//  				'depth'=>$this->viewTestMenuOpen->widget('create_depth')->value(),
//  				'link'=>$this->viewTestMenuOpen->widget('create_link')->value(),
//  				'active'=>$this->viewTestMenuOpen->widget('create_active')->value(),		
//  			);
//  			$this->createItem($arrSettingOld, $sXpath, $sXpathTo, $arrSettingNew, $arrItemChild);
//  		}
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
	
	public function idSearch($arrSettingOld,$sXpath,$sItemId,$bflag)
	{	
		foreach($arrSettingOld as $key=>$item)
		{	
			//上一级xpath
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSettingOld['xpath'].'/';
				echo $sXpath."<br/>";
			}
	
			if($sXpath==$sItemId){
				echo "dddd";
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
			$sXpath=$sXpath.'item:'.$aItem->id().'/';
			$sItem=$sItem."<li xpath=\"$sXpath\">";
			if($aItem->title())
			{	
				$sTitle=$aItem->title();
				$sDepth=$aItem->depth();
				$bActive=$aItem->isActive();
				$sLink=substr($aItem->link(),1);
				$sItem=$sItem.'<a>'.$aItem->title().'</a>'.'&nbsp'.'&nbsp'.'&nbsp'.
				"<a href=\"#\" onclick=\"javascript: itemCreate('$sXpath')\">".'新建'.'</a>';
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
						"<a href=\"\" >"."删除".'</a>'.'&nbsp'.'&nbsp'.'&nbsp'.
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