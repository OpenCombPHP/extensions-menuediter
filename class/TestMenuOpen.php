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
							'id'=>'title',
							'class'=>'text',
							'title'=>'控制器'
					),
					array(
							'id'=>'depth',
							'class'=>'text',
							'title'=>'视图路径'
					),
					array(
							'id'=>'link',
							'class'=>'text',
							'title'=>'控件ID'
					),
					array(
							'id'=>'active',
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
// 		$arrTest=array('a'=>1000,'b'=>555555);
// 		$this->test($arrTest);exit;
		$sControllerName="dd";
 		$sViewPath='menuOpen';
 		$sWidgetId='testMenu';
 		$sXpathFirst=$this->params->get('xpath');
 		$sXpathTarget=$this->params->get('xpath');
 		
 		$arrXpathTarget=explode('/',$sXpathTarget);array_pop($arrXpathTarget);
//  		$sXpathOption=$this->params->get('xpathOption');
 		$sXpathOption='item:A/';
 		$sTitle='成功';
 		$sXpath='';
 		$arrXpath=array();
 		$arrSettingDelete=array();
 		$arrItemSettingNew=array();
 		$arrSettingChild=array();
 		$arrSettingNew=array();
 		$aSetting = Extension::flyweight('menuediter')->setting();
 		$akey=$aSetting->key('/'.$sControllerName,true);
 		$arrSetting=$akey->item($sViewPath.$sWidgetId);
 		$arrSettingDelete=$arrSetting;
 		$sMenu=$this->displaySetting($arrSetting, $sXpath);
 		$this->xpathOption($arrSetting, $sXpath, '' ,$arrXpath);
// 		$arrJson=array();
//  		$this->jsonSetting($arrSetting, '', $arrJson);
//  		$sJsonSetting=json_encode($arrJson);
//  		$this->viewTestMenuOpen->variables()->set('sJsonSetting',$sJsonSetting);
 		$this->viewTestMenuOpen->variables()->set('sMenu',$sMenu);
 		$this->viewTestMenuOpen->variables()->set('arrXpath',$arrXpath);
 		
		//$this->settingItemdelete(0,$arrSetting, '', $arrXpathTarget, $arrSettingDelete);
		$this->settingItemdelete2($arrSettingDelete, $arrXpathTarget);
		$this->itemSettingEdit($arrSetting, $sXpath, $sXpathTarget, $arrItemSettingNew,$arrSettingChild);//var_dump($arrSettingChild);
		$this->settingEdit($arrSettingDelete, $sXpath, $sXpathOption,$sXpathFirst, $arrSettingNew,$arrSettingChild);var_dump($arrSettingNew);
		//$this->itemSettingEdit($arrSetting, $sXpath, $sXpathTarget, $arrItemSettingNew,$arrSettingChild);
	}
	
// 	public function test($arrTest)
// 	{
// 		foreach($arrTest as $item)
// 		{
// 			if($item==1000)
// 			{	echo $item;
// 				return;
// 			}
// 			echo $item;
// 		}
// 	}
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
	
	//将修改的item放入setting中
	public function settingEdit($arrSetting,$sXpath,$sXpathTarget,$sXpathFirst,&$arrSettingNew,$arrSettingChild){
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
				$this->settingEdit($arrSetting[$key],$sXpath,$sXpathTarget,$sXpathFirst,$arrSettingNew[$key],$arrSettingChild);
				$sXpath=$sXpathOld;
			}
		}
	}
	
// 	public function settingItemdelete($h,$arrSetting,$sXpath,$arrXpathTarget,&$arrSettingDelete){
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
// 					}
// 				}
// 			}
// 		}
// 		var_dump($arrSetting);exit;
// 	}
	
	public function settingItemdelete2(&$arrSettingDelete,$arrXpathTarget)
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
						$this->settingItemdelete2($arrSettingDelete[$key],$arrXpathTarget);
					}
				}
			}	
		}	
	}
	
	//删除一个item
	public function settingItemdelete($h,$arrSetting,$sXpath,$arrXpathTarget,&$arrSettingDelete)
	{
		foreach($arrSetting as $key=>$item)
		{
			for($i=$h;$i<count($arrXpathTarget);$i++)
			{
				if($key==$arrXpathTarget[$i])
				{
					if($i==count($arrXpathTarget)-1)
					{
						//continue;
						unset($arrSetting[$key]);
						unset($arrSettingDelete[$key]);
						$i++;
						//$arrSettingDelete=$arrSetting;//var_dump($arrSetting);
					}else {
						$arrSettingDelete[$key]=$arrSetting[$key];
						$i=$i+$this->settingItemdelete($i,$arrSetting[$key],$sXpath,$arrXpathTarget,$arrSettingDelete[$key]);
					}
				}
				else {
					$arrSettingDelete[$key]=$arrSetting[$key];
				}
			}
		}
		return $i;
 	}
	
 	//删除一个item
// 	public function settingItemdelete($arrSetting,$sXpath,$sXpathTarget,&$arrSettingNew){
// 		foreach($arrSetting as $key=>$item)
// 		{	
// 			$sXpathOld=$sXpath;
// 			if($key=='xpath'){
// 				$sXpath=$sXpath.$arrSetting['xpath'].'/';
// 			}
	
// 			if($sXpath==$sXpathTarget){
// 				var_dump($arrSetting);
// 				//continue;
// 				unset($arrSetting);
// 				unset($arrSettingNew);
// 				echo $sXpath;
// 				return;
// 			}
// 			else
// 			{	
// 				$arrSettingNew[$key]=$arrSetting[$key];		
// 			}
	
// 			if(is_array($arrSetting[$key]))
// 			{
// 				$this->settingItemdelete($arrSetting[$key],$sXpath,$sXpathTarget,$arrSettingNew[$key]);
// 				$sXpath=$sXpathOld;
// 			}
// 		}
// 	}
	
	public function xpathOption($arrSetting,$sXpath,$sTitle,&$arrXpath){
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			$sTitleOld=$sTitle;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
				$sTitle=$sTitle.$arrSetting['title'].'/';
				$arrXpath[$sXpath]=$sTitle;
			}
	
			if(is_array($arrSetting[$key]))
			{
				$this->xpathOption($arrSetting[$key],$sXpath,$sTitle,$arrXpath);
				$sXpath=$sXpathOld;
				$sTitle=$sTitleOld;
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
	
	public function arrMerge($arrSetting,$sXpath,$sXpathTarget,&$arrlist){
		foreach($arrSetting as $key=>$item)
		{	
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
			}
			
			if($sXpath==$sXpathTarget)
			{
				if($key=='title')
				{	
					$arrI=&$arrlist[$arrSetting['xpath']];
					$arrI=array('xpath'=>'成功','title'=>'成功');
					$arrI=&$arrlist;
				}

				if(is_array($item))
				{
					$arrI=&$arrlist[$item['xpath']];
					$this->arrMerge($item,$sXpath,$sXpathTarget,$arrI);
					$sXpath=$sXpathOld;
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
					$this->arrMerge($item,$sXpath,$sXpathTarget,$arrI);
					$sXpath=$sXpathOld;
				}
			}
		}
	}
	
// 	public function settingEdit($arrSetting,$sXpath,$sXpathTarget)
// 	{
// 		foreach($arrSetting as $key=>$item)
// 		{
// 			$sXpathOld=$sXpath;
// 			if($key=='xpath'){
// 				$sXpath=$sXpath.$arrSetting['xpath'].'/';
// 				if($sXpath==$sXpathTarget)
// 				{	echo "sdfsdfsdfsdfddddddddd";
// 					if($key=='title'){
// 					$arrSetting[$key]="成功";var_dump($arrSetting);break;
// 					}
					
// 				}
// 			}
// 			if(is_array($item))
// 			{
// 				$this->settingEdit($item,$sXpath,$sXpathTarget);
// 				$sXpath=$sXpathOld;
// 			}
// 		}
// 		//return $arrSetting;
// 	}
	
	//settings数组递归方法 Menu转换成setting
	public function itemSetting($arra,&$arrSetting)
	{
		$arrI=&$arrSetting;
		foreach($arra as $key=>$aItem)
		{	
			
			if($aItem->title())
			{	
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
	
	//从setting中读取Menu并显示
	public function displaySetting($arrSetting,$sXpath)
	{
		$sMenu='<ul style=margin-left:10px>';
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
			}
			$sMenu=$sMenu."<li>";
			if($key=='title')
			{
				$sMenu=$sMenu."<a href=\"?c=org.opencomb.menuediter.TestMenuOpen&xpath=$sXpath\">".$arrSetting['title'].'</a>';
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
	
	public function itemItTest($arra,$sXpath)
	{ 
		$s='<ul style=margin-left:10px>';
		//$sParentMenu='';
		foreach($arra as $aItem)
		{
			$sXpath2=$sXpath;
			$sXpath=$sXpath.'/'.$aItem->id();
			$s=$s."<li xpath=\"$sXpath\">";
			if($aItem->title())
			{
	
				$sTitle=$aItem->title();
				//$sParentMenu=$aItem->parentMenu()->title();
				$s=$s.'<a>'.$aItem->title().'</a>'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$sXpath\">".'删除'.'</a>';
	
			}
			if($aItem->subMenu())
			{	
				$s=$s.$this->itemItTest($aItem->subMenu()->itemIterator(),$sXpath);
				$sXpath=$sXpath2;
	
			}
			else{
				$sXpath=$sXpath2;
			}
			$s=$s.'</li>';
	
		}
		$s=$s.'</ul>';
		return $s;
	}
	
	
	public function itemIt($arra,$sXpath,$sControllerName,$sViewPath,$sWidgetId)
	{
		$s='<ul style=margin-left:10px>';
		$sParentMenu='';
		foreach($arra as $aItem)
		{
			$sXpath2=$sXpath;
			$sXpath=$sXpath.'/'.$aItem->id();
			$s=$s."<li xpath=\"$sXpath\">";
			if($aItem->title())
			{
				
				$sTitle=$aItem->title();
				$sParentMenu=$aItem->parentMenu()->title();
				$s=$s.'<a>'.$aItem->title().'</a>'.'&nbsp'.'&nbsp'."<a href=\"?c=org.opencomb.menuediter.ItemEditer&xpath=$sXpath\">".'删除'.'</a>';

			}
			if($aItem->subMenu())
			{
				$s=$s.$this->itemIt($aItem->subMenu()->itemIterator(),$sXpath,$sControllerName,$sViewPath,$sWidgetId);
				$sXpath=$sXpath2;
				
			}
			else{
				$sXpath=$sXpath2;
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
}

?>