<?php
namespace org\opencomb\menuediter;

use org\jecat\framework\message\Message;
use org\jecat\framework\mvc\view\widget\menu\Menu;
use org\jecat\framework\mvc\view\View;
use org\jecat\framework\mvc\controller\Controller;
use org\opencomb\platform\ext\Extension;
use org\opencomb\frameworktest\aspect;
use org\opencomb\coresystem\mvc\controller\ControlPanel;

class MenuOpen extends ControlPanel
{
	protected $arrConfig = array(
					'title'=> '文章内容',
					'view' => array(
						'template'=>'MenuOpen.html',
						'widgets' => array(								
							array(
								'id'=>'controller_name',
								'class'=>'text',
								'title'=>'定义控制器'		
							),
							array(
								'id'=>'temp_Xpath',
								'class'=>'text',
								'title'=>'模板文件名'
							),
							array(
								'id'=>'menu_id',
								'class'=>'text',
								'title'=>'定义控件ID'
							),
							array(
								'id' => 'de_controller_name',
								'class' => 'text',
								'title' => '实例控制器'		
							),
							array(
									'id' => 'de_view_Xpath',
									'class' => 'text',
									'title' => '实例视图'
							),	
							array(
								'id'=>'edit_title',
								'class'=>'text',
								'title'=>'Item标题'
							),
	// 						array(
	// 							'id'=>'depth',
	// 							'class'=>'text',
	// 							'title'=>'层级'
	// 						),
							array(
								'id'=>'edit_link',
								'class'=>'text',
								'title'=>'edit_link',
							),
							array(
								'id'=>'edit_query',
								'class'=>'text',
								'type'=>'multiple',
								'title'=>'选中条件',
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
								'id'=>'hide_item_tempPath',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'编辑项模板名'
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
								'title'=>'xpath'
							),
							array(
								'id'=>'create_item_id',
								'class'=>'text',
								'title'=>'项目id'
							),
							array(
								'id'=>'create_title',
								'class'=>'text',
								'title'=>'控制器title'
							),
// 						array(
// 							'id'=>'create_depth',
// 							'class'=>'text',
// 							'title'=>'视图路径'
// 						),
							array(
								'id'=>'create_link',
								'class'=>'text',
								'title'=>'link'
							),
									
							array(
								'id'=>'create_query',
								'class'=>'text',
								'type'=>'multiple',
								'title'=>'激活'
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
								'title'=>'新建项controllerName'
							),
							array(
								'id'=>'hide_create_item_viewPath',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'新建项viewPath'
							),
							array(
									'id'=>'hide_create_item_tempPath',
									'class'=>'text',
									'type'=>'hidden',
									'title'=>'新建项模板名'
							),
							array(
								'id'=>'hide_create_item_menuId',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'新建项menuId'
							),
							array(
								'id'=>'hide_flag_create_item',
								'class'=>'text',
								'type'=>'hidden',
								'title'=>'判断提交项目编辑'
							),
						),
					)
				);
	
	public function process()
	{
		$this->doActions() ;
		if($this->params->get('history') == 'history'){
			$this->view->widget('menu_id')->setValue($this->params->get('menuid'));
			$sMenuId = $this->params->get('menuid');
			$aSetting = Extension::flyweight('menuediter')->setting();
			if($this->params->get('controllername'))
			{
				$this->view->widget('de_controller_name')->setValue($this->params->get('controllername'));
				$this->view->widget('de_view_Xpath')->setValue($this->params->get('viewpath'));
				$this->getHistory();
				
				$sControllerNamePage = $this->params->get('controllername');
				$sControllerName = str_replace('.','\\',$sControllerNamePage);
				$sViewPath = $this->params->get('viewpath');
				$sTempPath = $this->getTempPath($sControllerName,$sViewPath,$sMenuId);
				
				if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
				{
					$this->readSettingTemp($sViewPath,$sMenuId);
				}
				else{		
					$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
				}
			}else{
				$sTempPath = $this->params->get('temppath');
				$this->view->widget('temp_Xpath')->setValue($sTempPath);
				if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
				{
					$this->readSettingTemp($sTempPath,$sMenuId);
				}
				else{
					$this->readBeanConfigTemp($sTempPath,$sMenuId);
				}
			}
		}elseif($this->params->get('locationsort') == 'locationsort'){
			$sMenuId = $this->params->get('menuid');
			$aSetting = Extension::flyweight('menuediter')->setting();
			if($this->params->get('controllername'))
			{
				$sControllerNamePage=$this->params->get('controllername');
				$sControllerName=str_replace('.','\\',$sControllerNamePage);
				$sViewPath=$this->params->get('viewpath');
				$sTempPath = $this->getTempPath($sControllerName,$sViewPath,$sMenuId);
				
				if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
				{
					$this->readSettingTemp($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
					$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
				}else{
					$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
					$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
				}
			}else{
				$sTempPath = $this->params->get('temppath');
				if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
				{
					$this->readSettingTemp($sTempPath,$sMenuId);
					$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
				}else{
					$this->readBeanConfigTemp($sTempPath,$sMenuId);
					$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
				}
			}
		}elseif($this->params->get('locationdelete') == 'locationdelete'){
			$sMenuId=$this->params->get('menuid');
			if($this->params->get('controllername'))
			{
				$sControllerNamePage = $this->params->get('controllername');
				$sControllerName = str_replace('.','\\',$sControllerNamePage);
				$sViewPath = $this->params->get('viewpath');
				$sTempPath = $this->getTempPath($sControllerName,$sViewPath,$sMenuId);
				
				$this->readSettingTemp($sTempPath,$sMenuId);
				$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
			}else{
				$sTempPath = $this->params->get('temppath');
				$this->readSettingTemp($sTempPath,$sMenuId);
				$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
			}
		}else{
			$this->getHistory();
		}
	}
	
	public function formOpenItem()
	{	
		$this->view->loadWidgets($this->params);
		$sControllerNamePage = $this->view->widget('de_controller_name')->value();
		$sMenuId = $this->view->widget('menu_id')->value();
		$aSetting = Extension::flyweight('menuediter')->setting();
		
		if(empty($sControllerNamePage))
		{
			$sTempPath = $this->view->widget('temp_Xpath')->value();
			
			if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
			{
				$this->readSettingTemp($sTempPath,$sMenuId);
			}
			else{
				$this->readBeanConfigTemp($sTempPath,$sMenuId);
			}
		}else{
			$sControllerNamePage = $this->view->widget('de_controller_name')->value();
			$sControllerName = str_replace('.','\\',$sControllerNamePage);
			$aController = new $sControllerName();
			$aController->frame();
			$sViewPath = $this->view->widget('de_view_Xpath')->value();
			$aView = $aController->view()->findXPath($aController->view(),$sViewPath);var_dump($aController);exit;
			$sTempPath = $aView->template();
			
			if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
			{
				$this->readSettingTemp($sTempPath,$sMenuId);
			}
			else{
				//$this->readBeanConfigTemp($sTempPath,$sMenuId,$sControllerNamePage,$sViewPath);
				$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
			}
		}
	}
	
	public function formEditItem()
	{
		$this->view->loadWidgets($this->params);
		if($this->view->widget('hide_flag_edit_item')->value()==1)
		{
			$sEditTitle = trim($this->view->widget('edit_title')->value());
			$sEditLink =  trim($this->view->widget('edit_link')->value());
			$arrQueryNew = $this->getQuery('edit_query');
		
			$sControllerName = $this->view->widget('hide_item_controllerName')->value();
			$sControllerNamePage = str_replace('\\','.',$sControllerName);
			$sViewPath = $this->view->widget('hide_item_viewPath')->value();
			$sTempPath = $this->view->widget('hide_item_tempPath')->value();
			$sMenuId = $this->view->widget('hide_item_menuId')->value();
			$aSetting = Extension::flyweight('menuediter')->setting();
			
			if(empty($sEditTitle) ||  $sEditTitle==='')
			{
				$skey="请输入标题";
				$this->view->createMessage(Message::error,"%s ",$skey);
				if(empty($sControllerName))
				{
					if($aSetting->hasItem('/menu',$sControllerName.'-'.$sViewPath.'-'.$sMenuId))
					{
						$this->readSettingTemp($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
					}else{
						$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
					}
				}else{
					if($aSetting->hasItem('/menu',$sControllerName.'-'.$sViewPath.'-'.$sMenuId))
					{
						$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
					}else{
						$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
					}
				}
				return;
			}
		
			if(empty($sControllerName))
			{
				if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
				{	
					$sControllerNamePage = str_replace('\\','.',$sControllerName);
					$akey = $aSetting->key('/menu',true);
					$sXpathFrom = $this->view->widget('hide_item_xpath')->value();
					$sXpathOption = $this->params->get('xpathOption');
			
					//判断移动的层级
					if(!$this->xPathOptionBool($sXpathFrom,$sXpathOption))
					{
						$skey="移动层级错误";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$this->readSettingTemp($sTempPath,$sMenuId);
						$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
						return;
					}
			
					$arrToXpath = explode('/',$sXpathFrom);
					array_pop($arrToXpath);
					$arrSettingOld = array();
					$arrSettingDelete = array();
					$arrSettingChild = array();
					$arrItemSettingMiddle = array();
			
			
					$arrSettingNew = array();
					$arrSettingOld = $akey->item($sTempPath.'-'.$sMenuId);
					$arrSettingDelete = $arrSettingOld;
			
					$this->settingItemdelete($arrSettingDelete, $arrToXpath);
			
					$this->itemSettingEdit($arrSettingOld, '', $sXpathFrom, $arrItemSettingMiddle,$arrSettingChild);
			
					$arrSettingChild['title'] = $this->view->widget('edit_title')->value();
					$arrSettingChild['link'] = $this->view->widget('edit_link')->value();
					$arrSettingChild['query'] = $this->getQuery('edit_query');
					
					if($sXpathFrom == $sXpathOption)
					{
						$arrSettingNew = $arrSettingOld;
						$this->settingEditXpathOption2($arrSettingNew, '',$sXpathFrom,$arrSettingChild);
					}else{
						$this->settingEditXpathOption($arrSettingDelete, '', $sXpathOption,$sXpathFrom, $arrSettingNew,$arrSettingChild);
					}
					$akey->deleteItem($sTempPath.'-'.$sMenuId);
					$arrSettingNew['id']=$sMenuId;
					$arrSettingNew['class']='menu';
					$akey->setItem($sTempPath.'-'.$sMenuId,$arrSettingNew);
					$this->readSettingTemp($sTempPath,$sMenuId);
					$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
			
				}else {
					$akey = $aSetting->key('/menu',true);
					$arrSettingOld = array();
					$aView = new View($sTempPath);
					$aMenu = $aView->widget($sMenuId);
					$this->itemSetting($aMenu->itemIterator(),$arrSettingOld);
			
					$sXpathFrom = $this->view->widget('hide_item_xpath')->value();
					$sXpathOption = $this->params->get('xpathOption');
					//判断移动的层级
					if(!$this->xPathOptionBool($sXpathFrom,$sXpathOption))
					{
						$skey="移动层级错误";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$this->readBeanConfigTemp($sTempPath,$sMenuId);
						$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
						return;
					}
					
					$arrToXpath = explode('/',$sXpathFrom);
					array_pop($arrToXpath);
			
					$arrSettingDelete = array();
					$arrSettingChild = array();
					$arrItemSettingMiddle = array();
					$arrSettingNew = array();
					$arrSettingDelete = $arrSettingOld;
			
					$this->settingItemdelete($arrSettingDelete, $arrToXpath);
					
					$this->itemSettingEdit($arrSettingOld, '', $sXpathFrom, $arrItemSettingMiddle,$arrSettingChild);
					
					$arrSettingChild['title'] = $this->view->widget('edit_title')->value();
					$arrSettingChild['link'] = $this->view->widget('edit_link')->value();
					$arrSettingChild['query'] = $this->getQuery('edit_query');
					
					if($sXpathFrom == $sXpathOption)
					{
						$arrSettingNew = $arrSettingOld;
						$this->settingEditXpathOption2($arrSettingNew, '',$sXpathFrom,$arrSettingChild);
					}else{
						$this->settingEditXpathOption($arrSettingDelete, '', $sXpathOption,$sXpathFrom, $arrSettingNew,$arrSettingChild);
					}

					$arrSettingNew['id'] = $sMenuId;
					$arrSettingNew['class'] = 'menu';
					$akey->setItem($sTempPath.'-'.$sMenuId,$arrSettingNew);
			
					$this->readBeanConfigTemp($sTempPath,$sMenuId);
					$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
				}
			}else{
				$aController = new $sControllerName();
				$aController->frame();
				$aView = $aController->view()->findXPath($aController->view(),$sViewPath );//var_dump($aController);exit;
				$sTempPath = $aView->template();
				if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
				{
					$sControllerNamePage = str_replace('\\','.',$sControllerName);
					$akey = $aSetting->key('/menu',true);
					$sXpathFrom = $this->view->widget('hide_item_xpath')->value();
					$sXpathOption = $this->params->get('xpathOption');
				
					//判断移动的层级
					if(!$this->xPathOptionBool($sXpathFrom,$sXpathOption))
					{
						$skey="移动层级错误";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
						return;
					}
				
					$arrToXpath = explode('/',$sXpathFrom);
					array_pop($arrToXpath);
					$arrSettingOld=array();
					$arrSettingDelete=array();
					$arrSettingChild=array();
					$arrItemSettingMiddle=array();
				
				
					$arrSettingNew = array();
					$arrSettingOld = $akey->item($sControllerName.'-'.$sViewPath.'-'.$sMenuId);
					$arrSettingDelete = $arrSettingOld;
				
					$this->settingItemdelete($arrSettingDelete, $arrToXpath);
				
					$this->itemSettingEdit($arrSettingOld, '', $sXpathFrom, $arrItemSettingMiddle,$arrSettingChild);
				
					$arrSettingChild['title']=$this->view->widget('edit_title')->value();
					$arrSettingChild['link']=$this->view->widget('edit_link')->value();
					$arrSettingChild['query']=$this->getQuery('edit_query');
				
					if($sXpathFrom == $sXpathOption)
					{
						$arrSettingNew = $arrSettingOld;
						$this->settingEditXpathOption2($arrSettingNew, '',$sXpathFrom,$arrSettingChild);
					}else{
						$this->settingEditXpathOption($arrSettingDelete, '', $sXpathOption,$sXpathFrom, $arrSettingNew,$arrSettingChild);
					}
					$akey->deleteItem($sTempPath.'-'.$sMenuId);
					$arrSettingNew['id']=$sMenuId;
					$arrSettingNew['class']='menu';
					$akey->setItem($sTempPath.'-'.$sMenuId,$arrSettingNew);
					$this->readSettingTemp($sTempPath,$sMenuId);
					$this->setMenuOpen($sControllerNamePage,$sViewPath,'',$sMenuId);
				}else {
					$akey = $aSetting->key('/menu',true);
					$sControllerNamePage = str_replace('\\','.',$sControllerName);
					$arrSettingOld = array();
					$aMenu = $aView->widget($sMenuId);
					$aMenuIterator = $aMenu->itemIterator();
					$this->itemSetting($aMenuIterator,$arrSettingOld);
				
					$sXpathFrom = $this->view->widget('hide_item_xpath')->value();
					$sXpathOption = $this->params->get('xpathOption');
					//判断移动的层级
					if(!$this->xPathOptionBool($sXpathFrom,$sXpathOption))
					{
						$skey="移动层级错误";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
						return;
					}
					$arrToXpath = explode('/',$sXpathFrom);
					array_pop($arrToXpath);
				
					$arrSettingDelete = array();
					$arrSettingChild = array();
					$arrItemSettingMiddle = array();
					$arrSettingNew = array();
					$arrSettingDelete = $arrSettingOld;
				
					$this->settingItemdelete($arrSettingDelete, $arrToXpath);
				
					$this->itemSettingEdit($arrSettingOld, '', $sXpathFrom, $arrItemSettingMiddle,$arrSettingChild);
				
					$arrSettingChild['title'] = $this->view->widget('edit_title')->value();
					$arrSettingChild['link'] = $this->view->widget('edit_link')->value();
					$arrSettingChild['query'] = $this->getQuery('edit_query');
				
					if($sXpathFrom == $sXpathOption)
					{
						$arrSettingNew = $arrSettingOld;
						$this->settingEditXpathOption2($arrSettingNew, '',$sXpathFrom,$arrSettingChild);
					}else{
						$this->settingEditXpathOption($arrSettingDelete, '', $sXpathOption,$sXpathFrom, $arrSettingNew,$arrSettingChild);
					}
				
					$arrSettingNew['id'] = $sMenuId;
					$arrSettingNew['class'] = 'menu';
					$akey->setItem($sTempPath.'-'.$sMenuId,$arrSettingNew);
					
					$this->readBeanConfig($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);echo $sMenuId;exit;
					$this->setMenuOpen($sControllerNamePage,$sViewPath,null,$sMenuId);
				}
			}
		}
	}
	
	public function formCreateItem()
	{
		$this->view->loadWidgets($this->params);
		if($this->view->widget('hide_flag_create_item')->value()==2){
			
			$sControllerName = $this->view->widget('hide_create_item_controllerName')->value();
			$sControllerNamePage = str_replace('\\','.',$sControllerName);
			$sViewPath = $this->view->widget('hide_create_item_viewPath')->value();
			$sTempPath = $this->view->widget('hide_create_item_tempPath')->value();
			$sMenuId = $this->view->widget('hide_create_item_menuId')->value();
			$sCreateTitle = $this->view->widget('create_title')->value();
			$sCreateItem = $this->view->widget('create_item_id')->value();
			$aSetting = Extension::flyweight('menuediter')->setting();
		
			if(empty($sCreateTitle) ||  $sCreateTitle === '' || empty($sCreateItem) || $sCreateItem === '')
			{
				$skey = "请输入标题";
				$this->view->createMessage(Message::error,"%s ",$skey);
// 				if($aSetting->hasItem('/menu',$sControllerName.'-'.$sViewPath.'-'.$sMenuId))
// 				{
// 					$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
// 					$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
// 				}else{
// 					$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
// 					$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
// 				}
// 				return;
				if(empty($sControllerName))
				{
					if($aSetting->hasItem('/menu',$sControllerName.'-'.$sViewPath.'-'.$sMenuId))
					{
						$this->readSettingTemp($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
					}else{
						$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
					}
				}else{
					if($aSetting->hasItem('/menu',$sControllerName.'-'.$sViewPath.'-'.$sMenuId))
					{
						$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
					}else{
						$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
					}
				}
			}
		
			if(empty($sControllerName))
			{
				if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
				{
					$akey = $aSetting->key('/menu',true);
					$arrSettingOld = $akey->Item($sTempPath.'-'.$sMenuId);
				
					$sXpath='';
					$arrSettingNew=array();
					$sItemId='item:'.str_replace('item-','',$this->view->widget('create_item_id')->value());
				
					$sXpathTo=$this->view->widget('hide_item_create_xpath')->value();
				
					if($sXpathTo!=='Top/')
					{
						$sItemId=$sXpathTo.$sItemId.'/';
					}else{
						$sItemId = $sItemId . '/';
					}
				
					$bflag=false;
					if($this->idSearch($arrSettingOld,$sXpath,$sItemId,$bflag))
					{
						$skey="项目ID重复";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$this->readSettingTemp($sTempPath,$sMenuId);
						$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
						return;
					}
				
					$arrItemChild=array(
							'xpath'=>'item:'.str_replace('item-','',$this->view->widget('create_item_id')->value()),
							'title'=>$this->view->widget('create_title')->value(),
							//'depth'=>$this->view->widget('create_depth')->value(),
							'link'=>$this->view->widget('create_link')->value(),
							'query'=>$this->getQuery('create_query'),
					);
					$this->createItem($arrSettingOld, $sXpath, $sXpathTo, $arrSettingNew, $arrItemChild);
					$akey->deleteItem($sViewPath.'-'.$sMenuId);
					$arrSettingNew['id']=$sMenuId;
					$arrSettingNew['class']='menu';
					$akey->setItem($sTempPath.'-'.$sMenuId,$arrSettingNew);
				
					$sClear="<a href=\"?c=org.opencomb.menuediter.MenuEditerClear&temppath=$sTempPath&menuid=$sMenuId\">".'清除'.'</a>';
					$this->view->variables()->set('sClear',$sClear);
				
					$this->readSettingTemp($sTempPath,$sMenuId);
					$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
				}else {
					$akey = $aSetting->key('/menu',true);
					$sItemId = 'item:'.str_replace('item-','',$this->view->widget('create_item_id')->value());
					$sXpathTo = $this->view->widget('hide_item_create_xpath')->value();
					$arrSettingOld = array();
					$aView = new View($sTempPath);
					$aMenu = $aView->widget($sMenuId);
					$this->itemSetting($aMenu->itemIterator(),$arrSettingOld);
					$sXpath='';
					$arrSettingNew=array();
				
					if($sXpathTo!=='Top/')
					{
						$sItemId=$sXpathTo.$sItemId.'/';
					}else{
						$sItemId = $sItemId . '/';
					}
				
					$bflag=false;
				
					if($this->idSearch($arrSettingOld,$sXpath,$sItemId,$bflag))
					{
						$skey="项目ID重复";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$this->readBeanConfigTemp($sTempPath,$sMenuId);
						$this->setMenuOpen(null,null,$sTempPath,sMenuId);
						return;
					}
				
					$arrItemChild=array(
							'xpath'=>'item:'.str_replace('item-','',$this->view->widget('create_item_id')->value()),
							'title'=>$this->view->widget('create_title')->value(),
							//'depth'=>$this->view->widget('create_depth')->value(),
							'link'=>$this->view->widget('create_link')->value(),
							'query'=>$this->getQuery('create_query'),
					);
					$this->createItem($arrSettingOld, $sXpath, $sXpathTo, $arrSettingNew, $arrItemChild);
					$akey->deleteItem($sTempPath.'-'.$sMenuId);
					$arrSettingNew['id']=$sMenuId;
					$arrSettingNew['class']='menu';
					$akey->setItem($sTempPath.'-'.$sMenuId,$arrSettingNew);
					
					$sClear="<a href=\"?c=org.opencomb.menuediter.MenuEditerClear&temppath=$sTempPath&menuid=$sMenuId\">".'清除'.'</a>';
					$this->view->variables()->set('sClear',$sClear);
					
					$this->readSettingTemp($sTempPath,$sMenuId);
					$this->setMenuOpen(null,null,$sTempPath,$sMenuId);
				}	
			}else{
				
				$aController = new $sControllerName();
				$aController->frame();
				$aView = $aController->view()->findXPath($aController->view(),$sViewPath );//var_dump($aController);exit;
				$sTempPath = $aView->template();
				if($aSetting->hasItem('/menu',$sTempPath.'-'.$sMenuId))
				{
					$sControllerNamePage = str_replace('\\', '.', $sControllerName);
				
					$akey = $aSetting->key('/menu',true);
					$arrSettingOld = $akey->Item($sViewPath.'-'.$sMenuId);
				
					$sXpath='';
					$arrSettingNew=array();
					$sItemId='item:'.str_replace('item-','',$this->view->widget('create_item_id')->value());
				
					$sXpathTo=$this->view->widget('hide_item_create_xpath')->value();
				
					if($sXpathTo!=='Top/')
					{
						$sItemId=$sXpathTo.$sItemId.'/';
					}else{
						$sItemId = $sItemId . '/';
					}
				
					$bflag=false;
					if($this->idSearch($arrSettingOld,$sXpath,$sItemId,$bflag))
					{
						$skey="项目ID重复";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,$sMenuId);
						return;
					}
				
					$arrItemChild=array(
							'xpath'=>'item:'.str_replace('item-','',$this->view->widget('create_item_id')->value()),
							'title'=>$this->view->widget('create_title')->value(),
							//'depth'=>$this->view->widget('create_depth')->value(),
							'link'=>$this->view->widget('create_link')->value(),
							'query'=>$this->getQuery('create_query'),
					);
					$this->createItem($arrSettingOld, $sXpath, $sXpathTo, $arrSettingNew, $arrItemChild);
					$akey->deleteItem($sTempPath.'-'.$sMenuId);
					$arrSettingNew['id']=$sMenuId;
					$arrSettingNew['class']='menu';
					$akey->setItem($sTempPath.'-'.$sMenuId,$arrSettingNew);
				
					$sClear="<a href=\"?c=org.opencomb.menuediter.MenuEditerClear&controllername=$sControllerName&viewpath=$sViewPath&menuid=$sMenuId\">".'清除'.'</a>';
					$this->view->variables()->set('sClear',$sClear);
				
					$this->readSetting($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
					$this->setMenuOpen($sControllerNamePage,$sViewPath,'',$sMenuId);
				}else {
					$sControllerNamePage = str_replace('\\', '.', $sControllerName);
				
					$akey=$aSetting->key('/menu',true);
					$sItemId = 'item:'.str_replace('item-','',$this->view->widget('create_item_id')->value());
					$sXpathTo = $this->view->widget('hide_item_create_xpath')->value();
					$arrSettingOld = array();
					
					$aMenu = $aView->widget($sMenuId);
					$this->itemSetting($aMenu->itemIterator(),$arrSettingOld);
					$sXpath='';
					$arrSettingNew=array();
				
					if($sXpathTo!=='Top/')
					{
						$sItemId=$sXpathTo.$sItemId.'/';
					}else{
						$sItemId = $sItemId . '/';
					}
				
					$bflag=false;
				
					if($this->idSearch($arrSettingOld,$sXpath,$sItemId,$bflag))
					{
						$skey="项目ID重复";
						$this->view->createMessage(Message::error,"%s ",$skey);
						$this->readBeanConfig($sControllerNamePage,true,$sControllerName,$sViewPath,$sMenuId);
						$this->setMenuOpen($sControllerNamePage,$sViewPath,'',$sMenuId);
						return;
					}
				
					$arrItemChild=array(
							'xpath'=>'item:'.str_replace('item-','',$this->view->widget('create_item_id')->value()),
							'title'=>$this->view->widget('create_title')->value(),
							//'depth'=>$this->view->widget('create_depth')->value(),
							'link'=>$this->view->widget('create_link')->value(),
							'query'=>$this->getQuery('create_query'),
					);
					$this->createItem($arrSettingOld, $sXpath, $sXpathTo, $arrSettingNew, $arrItemChild);
					$akey->deleteItem($sTempPath.'-'.$sMenuId);
					$arrSettingNew['id']=$sMenuId;
					$arrSettingNew['class']='menu';
					$akey->setItem($sTempPath.'-'.$sMenuId,$arrSettingNew);
					$this->readBeanConfig($sControllerNamePage,$bFlag=true,$sControllerName,$sViewPath,$sMenuId);
					$this->setMenuOpen($sControllerNamePage,$sViewPath,'',$sMenuId);
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
			if($aItem->id())
			{	
				$arrItem = $aItem->beanConfig();
				$sQuery = isset($arrItem['query'])?$arrItem['query']:'';
				$arrI = &$arrSetting['item:'.$key];
				$arrI = array('xpath'=>'item:'.$aItem->id(),'title'=>$aItem->title(),'link'=>$aItem->link(),'menu'=>$aItem->subMenu()?1:0,'query'=>$sQuery);
				$arrI = &$arrSetting;
			}
			if($aItem->subMenu())
			{	
				$arrI = &$arrI['item:'.$key];
				$this->itemSetting($aItem->subMenu()->itemIterator(),$arrI);
			}
		}
	}
	
	//从BeanConfig中读取Menu，显示
	public function itemMerge($aMenuIterator,$sXpath,$sControllerName,$sViewPath,$sMenuId)
	{	
		$sItem = '<ul class=mo-middile-ul>';
		foreach($aMenuIterator as $aItem)
		{	
			$sXpathOld=$sXpath;
			$sXpath=$sXpath.'item:'.$aItem->id().'/';
			$sItem = $sItem."<li xpath=\"$sXpath\">";
			if($aItem->title())
			{	
				$sTitle = $aItem->title();
				$sLink = substr($aItem->link(),1);
				$sItem = $sItem.'<span>'.$aItem->title().'</span>'.'<em>'.
							"<a class=\"mo-del\" href=\"?c=org.opencomb.menuediter.ItemDelete&xpath=$sXpath&controllername=$sControllerName
							&viewpath=$sViewPath&menuid=$sMenuId\" onclick='javascript: return confirmDel()'>".'</a>'.
							"<a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('$sXpath')\">".'</a>'.'</a>'.
							"<a class=\"mo-edit\" href=\"#\" onclick=\"javascript: itemEdit('$sXpath')\">".'</a>'.
							"<a class=\"mo-up\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=up&xpath=$sXpath&controllername=$sControllerName
							&viewpath=$sViewPath&menuid=$sMenuId\">".'</a>'.
							"<a class=\"mo-down\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=down&xpath=$sXpath&controllername=$sControllerName
							&viewpath=$sViewPath&menuid=$sMenuId\">".'</a>'.'</em>';
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
	
	//显示菜单的字符串组合
	public function displaySetting($arrSetting,$sXpath,$sControllerName,$sViewPath,$sMenuId,$bflag=true)
	{
		$sMenu = '<ul class=mo-middile-ul>';
		foreach($arrSetting as $key => $item)
		{
			if(substr($key,0,5) == 'item:')
			{
				$sXpathOld=$sXpath;
				$sXpath = $sXpath.$item['xpath'].'/';
				$sMenu=$sMenu.'<li>'."<p>"."<span>".$item['title'].'</span>'.'<em>'.
						"<a class=\"mo-down\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=down&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\">".'</a>'.
						"<a class=\"mo-up\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=up&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\">".'</a>'.
						"<a class=\"mo-edit\" href=\"#\" onclick=\"javascript: itemEdit('$sXpath')\">".'</a>'.
						"<a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('$sXpath')\">".'</a>'.
						"<a class=\"mo-del\" href=\"?c=org.opencomb.menuediter.ItemDelete&xpath=$sXpath&controllername=$sControllerName
						&viewpath=$sViewPath&menuid=$sMenuId\" onclick='javascript: return confirmDel()'/>".'</a>'.
						'</em>'."</p>";
				$sMenu1=$this->displaySetting($item,$sXpath,$sControllerName,$sViewPath,$sMenuId,$bflag);
				if(stripos($sMenu1,"li"))
				{
					
				}else{
					$sMenu1='';
				}
				$sMenu=$sMenu.$sMenu1;
				$sXpath=$sXpathOld;
				$sMenu=$sMenu."</li>";
			}
		}
		$sMenu=$sMenu.'</ul>';
		return $sMenu;
	}
	
	public function displaySettingTemp($arrSetting,$sXpath,$sTempPath,$sMenuId,$bflag=true)
	{
		$sMenu='<ul class=mo-middile-ul>';
		foreach($arrSetting as $key=>$item)
		{
			if(substr($key,0,5)=='item:')
			{
				$sXpathOld=$sXpath;
				$sXpath=$sXpath.$item['xpath'].'/';
				$sMenu=$sMenu.'<li>'."<p>"."<span>".$item['title'].'</span>'.'<em>'.
						"<a class=\"mo-down\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=down&xpath=$sXpath
						&temppath=$sTempPath&menuid=$sMenuId\">".'</a>'.
						"<a class=\"mo-up\" href=\"?c=org.opencomb.menuediter.ItemSort&item_go=up&xpath=$sXpath
						&temppath=$sTempPath&menuid=$sMenuId\">".'</a>'.
						"<a class=\"mo-edit\" href=\"#\" onclick=\"javascript: itemEdit('$sXpath')\">".'</a>'.
						"<a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('$sXpath')\">".'</a>'.
						"<a class=\"mo-del\" href=\"?c=org.opencomb.menuediter.ItemDelete&xpath=$sXpath
						&temppath=$sTempPath&menuid=$sMenuId\" onclick='javascript: return confirmDel()'/>".'</a>'.
						'</em>'."</p>";
				$sMenu1 = $this->displaySettingTemp($item,$sXpath,$sTempPath,$sMenuId,$bflag);
				if(stripos($sMenu1,"li"))
				{
					
				}else{
					$sMenu1 = '';
				}
				$sMenu = $sMenu.$sMenu1;
				$sXpath = $sXpathOld;
				$sMenu = $sMenu."</li>";
			}
		}
		$sMenu = $sMenu.'</ul>';
		return $sMenu;
	}
	
	//json
	public function jsonSetting($arrSetting,$sXpath,&$arrJson)
	{
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/';
				if(is_array($arrSetting['query'])){
					$ss=null;
					foreach($arrSetting['query'] as $key=>$item)
					{
						$ss=$ss.$item."\r\n";
					}
					$arrJson[$sXpath]=array('title'=>$arrSetting['title'],
							'xpath'=>$arrSetting['xpath'],'link'=>$arrSetting['link'],
							'query'=>$ss
							);
				}else{
					$arrJson[$sXpath]=array('title'=>$arrSetting['title'],
							'xpath'=>$arrSetting['xpath'],'link'=>$arrSetting['link'],
							'query'=>$arrSetting['query']
							);
				}
			}
	
			if(substr($key,0,5)=='item:')
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
			$sXpathOld = $sXpath;
			$sTitleOld = $sTitle;
			if($key == 'xpath'){
				$sXpath = $sXpath.$arrSetting['xpath'].'/';
				$sTitle = $sTitle.$arrSetting['title'].'/';
				$arrXpath[$sXpath] = $sTitle;
			}
	
			if(substr($key,0,5) == 'item:')
			{
				$i = $i+$this->xpathOption($arrSetting[$key],$sXpath,$sTitle,$i++,$arrXpath);
				$sXpath = $sXpathOld;
				$sTitle = $sTitleOld;
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
				//$arrSettingNew['depth']=$arrItem['depth'];
				$arrSettingNew['link']=$arrItem['link'];
				$arrSettingNew['query']=$arrItem['query'];
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
	
			if(substr($key,0,5)=='item:')
			{
				$this->itemSettingEdit($arrSetting[$key],$sXpath,$sXpathTarget,$arrItemSettingNew[$key],$arrSettingChild);
				$sXpath=$sXpathOld;
			}
		}
	}
	
	//删除一个item
	public function settingItemDelete(&$arrSettingNew,$arrXpathTarget)
	{	
		$sLastKey = array_pop($arrXpathTarget) ;
		$arrCurrentKey =& $arrSettingNew ;
		
		foreach($arrXpathTarget as $sKey)
		{
			$arrCurrentKey =& $arrCurrentKey[$sKey] ;
		}
		unset($arrCurrentKey[$sLastKey]) ;
	}
	
	public function settingEditXpathOption($arrSetting,$sXpath,$sXpathTarget,$sXpathFirst,&$arrSettingNew,$arrSettingChild)
	{
		if($sXpathTarget=='Top/')
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
				{	
					$arrSettingNew[$key]=$arrSetting[$key];
				}
			
				if(substr($key,0,5)=='item:')
				{
					$this->settingEditXpathOption($arrSetting[$key],$sXpath,$sXpathTarget,$sXpathFirst,$arrSettingNew[$key],$arrSettingChild);
					$sXpath=$sXpathOld;
				}
			}
		}	
	}
	
	public function settingEditXpathOption2(&$arrSettingOld,$sXpath,$sXpathTarget,$arrItem)
	{
		foreach($arrSettingOld as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSettingOld['xpath'].'/';
			}
	
			//找到需要编辑的元素
			if($sXpath==$sXpathTarget){
				$arrSettingOld[$key]=$arrItem[$key];
			}
				//如果是数组递归
			if(substr($key,0,5)=='item:')
			{
				$this->settingEditXpathOption2($arrSettingOld[$key],$sXpath,$sXpathTarget,$arrItem);
				$sXpath=$sXpathOld;
			}
		}
	}
	
	//判断创建的菜单id是否相同
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
			if(substr($key,0,5)=='item:')
			{
				$bflag=$this->idSearch($arrSettingOld[$key],$sXpath,$sItemId,$bflag);
				$sXpath=$sXpathOld;
			}
		}
		return $bflag;
	}
	
	//创建一个菜单
	public function createItem($arrSetting,$sXpath,$sXpathTo,&$arrSettingNew,$arrItemChild)
	{
		if($sXpathTo=='Top/')
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
	
				if(substr($key,0,5)=='item:')
				{
					$this->createItem($arrSetting[$key],$sXpath,$sXpathTo,$arrSettingNew[$key],$arrItemChild);
					$sXpath=$sXpathOld;
				}
			}
		}
	}
	
	//获得打开过的菜单记录
	public function getHistory()
	{	
		$aSetting = Extension::flyweight('menuediter')->setting();
		$arrHistory=array();
		$arrHistory[0]=array('<ul><li><a href="?c=org.opencomb.menuediter.MenuOpen&history=history&temppath=coresystem:FrontFrame.html&menuid=mainMenu">前台菜单</a></li></ul>');
		$arrHistory[1]=array('<ul><li><a href="?c=org.opencomb.menuediter.MenuOpen&history=history&temppath=coresystem:ControlPanelFrame.html&menuid=mainMenu">控制面板菜单(后台)</a></li></ul>');
		//暂时隐藏用户面板菜单和开发菜单
		/*
		$arrHistory[2]=array('<ul><li><a href="?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=org.opencomb.coresystem.mvc.controller.ControlPanelFrame
				&viewpath=frameView&menuid=mainMenu">用户面板菜单</a></li></ul>');
		$arrHistory[3]=array('<ul><li><a href="?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=org.opencomb.coresystem.mvc.controller.ControlPanelFrame
				&viewpath=frameView&menuid=mainMenu">开发菜单</a></li></ul>');
		*/
		$arrHistory[4] = array('<p>最近打开的菜单</p>');
		$sHistory = null;
		$i = 5;	
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
		$this->view->variables()->set('sHistory',$sHistory);
	}
	
	//从setting中读取已有的menu
	public function readSetting($sControllerNamePageFormal=null,$bFlag=true,$sControllerNameFormal,$sViewPathFormal,$sMenuIdFormal)
	{	 
		$sControllerNamePage = $sControllerNamePageFormal;
		if($bFlag)
		{
			$sControllerName = $sControllerNameFormal;
		}
		else {
			$sControllerName = str_replace('.','\\',$sControllerNamePage);
		}
		
		$sViewPath = $sViewPathFormal;
		$sMenuId = $sMenuIdFormal;
		$aSetting = Extension::flyweight('menuediter')->setting();
		
		$akey = $aSetting->key('/menu',true);
		$arrCountItem = array();
		$arrJson = array();
		$arrXpath = array();
		$sXpath='';
		$arrSetting = $akey->Item($sViewPath.'-'.$sMenuId);
		$this->getCreateItemCounttArray($arrSetting,'',$arrCountItem);
		$arrCreateJson=array();
		if(count($arrCountItem) == 0)
		{
			$arrCountItem[''] = array();
		}
		$this->setCreateItemId($arrCountItem[''],'',$arrCreateJson);
		$arrCreateTop = array('Top/'=>'item-'.(count($arrCountItem[''])+1));
		$arrCreateJson = array_merge($arrCreateTop,$arrCreateJson);
		$arrCreateJson['controllername'] = $sControllerName;
		$arrCreateJson['viewpath'] = $sViewPath;
		$arrCreateJson['menuid'] = $sMenuId;
		
		$sMenu = $this->displaySetting($arrSetting,$sXpath,$sControllerName,$sViewPath,$sMenuId);
		$sTopMenu = '<ul class=mo-middile-ul>'.'<li>'.'<p>'.'<span>'.'顶层'.'</span>'.
				"<em><a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('Top/')\">".'</a>'.'</em>'.'</p>'.'</li>'.'</ul>';
		$sMenu = $sTopMenu.$sMenu;
		$this->view->variables()->set('sMenu',$sMenu);
		$arrJson['controllername'] = $sControllerName;
		$arrJson['viewpath'] = $sViewPath;
		$arrJson['menuid'] = $sMenuId;
		
		$this->jsonSetting($arrSetting, $sXpath, $arrJson);
		$this->view->variables()->set('sJsonSetting',json_encode($arrJson));
		$this->view->variables()->set('CreateJson',json_encode($arrCreateJson));
		$this->xpathOption($arrSetting,'','',0,$arrXpath);
		$arrTop = array('Top/'=>'顶层');
		$arrXpath = array_merge($arrTop,$arrXpath);
		$this->view->variables()->set('arrXpath',$arrXpath);
	
		$aController = new $sControllerName();
		if($sControllerNamePage == 'org.opencomb.coresystem.mvc.controller.FrontFrame' or $sControllerNamePage == 'org.opencomb.coresystem.mvc.controller.ControlPanel')
		{
			$this->getHistory();
		}else {
			if($aController->title() == null)
			{
				$sHistoty = '<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".'控制器'.$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory = array($sHistoty);
				$akey = $aSetting->key('/history',true);
				$akey->setItem($sViewPath.'-'.$sMenuId,$arrHistory);
			}else
			{
				$sHistoty = '<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".$aController->title().$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory = array($sHistoty);
				$akey = $aSetting->key('/history',true);
				$akey->setItem($sViewPath.'-'.$sMenuId,$arrHistory);
			}
			$this->getHistory();
		}
		
		$sClear = "<a class=\"mo-clear\" href=\"?c=org.opencomb.menuediter.MenuEditerClear&controllername=$sControllerName&viewpath=$sViewPath&menuid=$sMenuId\">".'清除'.'</a>';
		$this->view->variables()->set('sClear',$sClear);
	}
	
	public function readSettingTemp($sTempPathFormal,$sMenuIdFormal)
	{
		$sTempPath = $sTempPathFormal;
		$sMenuId = $sMenuIdFormal;
		$aSetting = Extension::flyweight('menuediter')->setting();
	
		$akey = $aSetting->key('/menu',true);
		$arrCountItem = array();
		$arrJson = array();
		$arrXpath = array();
		$sXpath='';
		$arrSetting = $akey->Item($sTempPathFormal.'-'.$sMenuId);
		$this->getCreateItemCounttArray($arrSetting,'',$arrCountItem);
		$arrCreateJson = array();
		if(count($arrCountItem) == 0)
		{
			$arrCountItem[''] = array();
		}
		$this->setCreateItemId($arrCountItem[''],'',$arrCreateJson);
		$arrCreateTop = array('Top/'=>'item-'.(count($arrCountItem[''])+1));
		$arrCreateJson = array_merge($arrCreateTop,$arrCreateJson);
		$arrCreateJson['controllername'] = null;
		$arrCreateJson['viewpath'] = null;
		$arrCreateJson['temppath'] = $sTempPath;
		$arrCreateJson['menuid'] = $sMenuId;
	
		$sMenu = $this->displaySettingTemp($arrSetting,$sXpath,$sTempPath,$sMenuId);
		$sTopMenu = '<ul class=mo-middile-ul>'.'<li>'.'<p>'.'<span>'.'顶层'.'</span>'.
					"<em><a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('Top/')\">".'</a>'.'</em>'.'</p>'.'</li>'.'</ul>';
		$sMenu = $sTopMenu.$sMenu;
		
		$arrJson['controllername'] = null;
		$arrJson['viewpath'] = null;
		$arrJson['temppath'] = $sTempPath;
		$arrJson['menuid'] = $sMenuId;
	
		$this->jsonSetting($arrSetting, $sXpath, $arrJson);
		$this->view->variables()->set('sJsonSetting',json_encode($arrJson));
		$this->view->variables()->set('CreateJson',json_encode($arrCreateJson));
		$this->xpathOption($arrSetting,'','',0,$arrXpath);
		$arrTop = array('Top/'=>'顶层');
		$arrXpath = array_merge($arrTop,$arrXpath);
		$this->view->variables()->set('sMenu',$sMenu);
		$this->view->variables()->set('arrXpath',$arrXpath);
	
// 		$aView = new View($sTempPathFormal);
// 		$aController = $aView->controller();
// 		$sControllerNamePage = $aController->name();
		
		if($sTempPath == 'coresystem:ControlPanelFrame.html' or $sTempPath== 'coresystem:FrontFrame.html')
		{
			$this->getHistory();
		}else {
			$sHistoty = '<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&temppath=$sTempPath&menuid=$sMenuId\">".'模板'.$sTempPath.'菜单'.$sMenuId.'</a>'.'</li>'.'</ul>';
			$arrHistory = array($sHistoty);
			$akey = $aSetting->key('/history',true);
			$akey->setItem($sTempPath.'.'.$sMenuId,$arrHistory);
			$this->getHistory();
		}
	
		$sClear = "<a class=\"mo-clear\" href=\"?c=org.opencomb.menuediter.MenuEditerClear&temppath=$sTempPath&menuid=$sMenuId\">".'清除'.'</a>';
		$this->view->variables()->set('sClear',$sClear);
	}
	
	//从Beanconfig中读取menu
	public function readBeanConfig($sControllerNamePageFormal=null,$bFlag=true,$sControllerNameFormal,$sViewPathFormal,$sMenuIdFormal)
	{	
		$sControllerNamePage = $sControllerNamePageFormal;
		if($bFlag)
		{
			$sControllerName = $sControllerNameFormal;
		}
		else {
			$sControllerName = str_replace('.','\\',$sControllerNamePage);
		}
		$sViewPath = $sViewPathFormal;
		$sMenuId = $sMenuIdFormal;
		$aSetting = Extension::flyweight('menuediter')->setting();
		
		$arrJson = array();
		$arrXpath = array();
		// 检查 控制器类 是否有效
		if( !class_exists($sControllerName) or !new $sControllerName() instanceof Controller)
		{
			$skey = "无此控制器";
			$this->view->createMessage(Message::error,"%s ",$skey);
			$this->getHistory();
			return;
		}
		else {
			$aController = new $sControllerName();
			$aController->frame();
		}
		
		// 检查视图
		if( !$aView = $aController->view()->findXPath($aController->view(),$sViewPath))
		{
			$skey = "无此视图";
			$this->view->createMessage(Message::error,"%s ",$skey);
			$this->getHistory();
			return;
		}
		$sTempPath = $aView->template();
		// 检查菜单
		if( !$aMenu = $aView->widget($sMenuId) or !$aMenu instanceof Menu)
		{
			$skey = "无此菜单";
			$this->view->createMessage(Message::error,"%s ",$skey);
			$this->getHistory();
			return;
		}
		
		//将menu组成字符串在页面显示
		$arrCountItem = array();
		$arrJson = array();
		$arrSetting = array();
		$sXpath = '';
		//将menu遍历成数组存放在settting
		$this->itemSetting($aMenu->itemIterator(),$arrSetting);
		$sMenu = $this->displaySetting($arrSetting,$sXpath,$sControllerName,$sViewPath,$sMenuId);
		$sTopMenu = '<ul class=mo-middile-ul>'.'<li>'.'<p>'.'<span>'.'顶层'.'</span>'.
				"<em><a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('Top/')\">".'</a>'.'</em>'.'</li>'.'</ul>';
		$sMenu = $sTopMenu.$sMenu;
		
		$this->getCreateItemCounttArray($arrSetting,'',$arrCountItem);
		$arrCreateJson=array();
		$this->setCreateItemId($arrCountItem[''],'',$arrCreateJson);
		$arrCreateTop = array('Top/'=>'item-'.(count($arrCountItem[''])+1));
		$arrCreateJson = array_merge($arrCreateTop,$arrCreateJson);
		$arrCreateJson['controllername'] = $sControllerName;
		$arrCreateJson['viewpath'] = $sViewPath;
		$arrCreateJson['temppath'] = $sTempPath;
		$arrCreateJson['menuid'] = $sMenuId;
	
		$this->jsonSetting($arrSetting, $sXpath, $arrJson);
		$arrJson['controllername'] = $sControllerName;
		$arrJson['viewpath'] = $sViewPath;
		$arrJson['temppath'] = $sTempPath;
		$arrJson['menuid'] = $sMenuId;
	
		$this->xpathOption($arrSetting,'','',0,$arrXpath);
		$arrTop = array('Top/'=>'顶层');
		$arrXpath = array_merge($arrTop,$arrXpath);
	
		$this->view->variables()->set('sMenu',$sMenu);
		$this->view->variables()->set('sJsonSetting',json_encode($arrJson));
		$this->view->variables()->set('CreateJson',json_encode($arrCreateJson));
		$this->view->variables()->set('arrXpath',$arrXpath);
	
		$this->view->widget('hide_create_item_controllerName')->setValue($sControllerName);
		$this->view->widget('hide_create_item_viewPath')->setValue($sViewPath);
		$this->view->widget('hide_create_item_tempPath')->setValue($sTempPath);
		$this->view->widget('hide_create_item_menuId')->setValue($sMenuId);
		if($sControllerNamePage == 'org.opencomb.coresystem.mvc.controller.FrontFrame' or $sControllerNamePage == 'org.opencomb.coresystem.mvc.controller.ControlPanel' or
			$sTempPath == 'coresystem:ControlPanelFrame.html' or $sTempPath='coresystem:FrontFrame.html'
			)
		{
			$this->getHistory();
		}else {
			if($aController->title() == null)
			{
				$sHistoty = '<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".'控制器'.$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory = array($sHistoty);
				$akey = $aSetting->key('/history/'.$sControllerName,true);
				$akey->setItem($sViewPath.$sMenuId,$arrHistory);
			}else
			{
				$sHistoty = '<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&controllername=$sControllerNamePage&viewpath=$sViewPath&menuid=$sMenuId\">".$aController->title().$sMenuId.'</a>'.'</li>'.'</ul>';
				$arrHistory = array($sHistoty);
				$akey = $aSetting->key('/history/'.$sControllerName,true);
				$akey->setItem($sViewPath.$sMenuId,$arrHistory);
			}
			$this->getHistory();
		}
	}
	
	public function readBeanConfigTemp($sTempPath,$sMenuIdFormal,$sControllerNamePage=null,$sViewPath=null)
	{
		$sMenuId = $sMenuIdFormal;
		// 检查视图
		if( !$aView = new View($sTempPath))
		{
			$skey = "无此视图";
			$this->view->createMessage(Message::error,"%s ",$skey);
			$this->getHistory();
			return;
		}
		
		// 检查菜单
		if( !$aMenu = $aView->widget($sMenuId) or !$aMenu instanceof Menu)
		{
			$skey = "无此菜单";
			$this->view->createMessage(Message::error,"%s ",$skey);
			$this->getHistory();
			return;
		};
		
		//将menu组成字符串在页面显示
		$arrCountItem = array();
		$arrJson = array();
		$arrSetting = array();
		$sXpath = '';
		//将menu遍历成数组存放在settting
		$this->itemSetting($aMenu->itemIterator(),$arrSetting);
		$sMenu = $this->displaySettingTemp($arrSetting,$sXpath,$sTempPath,$sMenuId);
		$sTopMenu = '<ul class=mo-middile-ul>'.'<li>'.'<p>'.'<span>'.'顶层'.'</span>'.
				"<em><a class=\"mo-new\" href=\"#\" onclick=\"javascript: itemCreate('Top/')\">".'</a>'.'</em>'.'</li>'.'</ul>';
		$sMenu = $sTopMenu.$sMenu;
		
		$this->getCreateItemCounttArray($arrSetting,'',$arrCountItem);
		$arrCreateJson=array();
		$this->setCreateItemId($arrCountItem[''],'',$arrCreateJson);
		$arrCreateTop = array('Top/'=>'item-'.(count($arrCountItem[''])+1));
		$arrCreateJson = array_merge($arrCreateTop,$arrCreateJson);
		$arrCreateJson['controllername'] = null;
		$arrCreateJson['viewpath'] = null;
		$arrCreateJson['temppath'] = $sTempPath;
		$arrCreateJson['menuid'] = $sMenuId;
	
		$this->jsonSetting($arrSetting, $sXpath, $arrJson);
		$arrJson['controllername'] = null;
		$arrJson['viewpath'] = null;
		$arrJson['temppath'] = $sTempPath;
		$arrJson['menuid'] = $sMenuId;
	
		$this->xpathOption($arrSetting,'','',0,$arrXpath);
		$arrTop = array('Top/'=>'顶层');
		$arrXpath = array_merge($arrTop,$arrXpath);
	
		$this->view->variables()->set('sMenu',$sMenu);
		$this->view->variables()->set('sJsonSetting',json_encode($arrJson));
		$this->view->variables()->set('CreateJson',json_encode($arrCreateJson));
		$this->view->variables()->set('arrXpath',$arrXpath);
	
		$this->view->widget('hide_create_item_controllerName')->setValue(null);
		$this->view->widget('hide_create_item_viewPath')->setValue(null);
		$this->view->widget('hide_create_item_tempPath')->setValue($sTempPath);
		$this->view->widget('hide_create_item_menuId')->setValue($sMenuId);
		
// 		if($sTempPath == 'coresystem:ControlPanelFrame.html')
// 		{
// 			$this->getHistory();
// 		}else{
// 			$sHistoty = '<ul>'.'<li>'."<a href=\"?c=org.opencomb.menuediter.MenuOpen&history=history&temppath=$sTempPath&menuid=$sMenuId\">".'模板'.$sTempPath.'菜单'.$sMenuId.'</a>'.'</li>'.'</ul>';
// 			$arrHistory = array($sHistoty);
// 			$akey = $aSetting->key('/history',true);
// 			$akey->setItem($sTempPath.'.'.$sMenuId,$arrHistory);
// 		}
// 		$this->getHistory();
	}
	
	public function setMenuOpen($sControllerNamePage,$sViewPath,$sTempPath,$sMenuId)
	{
		if(empty($sControllerNamePage))
		{
			$this->view->widget('temp_Xpath')->setValue($sTempPath);
		}else{
			$this->view->widget('de_controller_name')->setValue($sControllerNamePage);
			$this->view->widget('de_view_Xpath')->setValue($sViewPath);
		}
		$this->view->widget('menu_id')->setValue($sMenuId);
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
		if(count($arrXpathForm)==1 and $arrXpathOption[0]=='Top')
		{
			return false;
		}elseif($sXpathFrom==$sXpathOption){
			return true;
		}
		elseif(count($arrXpathForm)>=count($arrXpathOption))
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
				if(count($arrXpathForm)-count($arrXpathOption)>=2)
				{
					return true;
				}elseif(count($arrXpathForm)-count($arrXpathOption)==1){
					return true;
				}else{
					return false;
				}
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
	
	public function getQuery($sflag)
	{
		if($sflag=='edit_query')
		{
			$arrQuery=explode("\n",str_replace('\r','',$this->view->widget('edit_query')->value()));
		}elseif($sflag=='create_query')
		{
			$arrQuery=explode("\n",$this->view->widget('create_query')->value());
		}
		$arrQueryNew=array();
		$nI=0;
		foreach($arrQuery as $key=>$item)
		{
			if($item=='' or $item=="\n" or $item=="\r")
			{
				continue;
			}else{
				$arrQueryNew[$nI]=trim($item);
				$nI++;
			}
		
		}
		if(count($arrQueryNew)==1)
		{
			return $arrQueryNew[0];
		}elseif(count($arrQueryNew)==0){
			return null;
		}else{
			return $arrQueryNew;
		}
	}
	
	public function getCreateItemCounttArray($arrSetting,$sXpath,&$arrCountItem)
	{
		foreach($arrSetting as $key=>$item)
		{
			$sXpathOld=$sXpath;
			if($key=='xpath'){
				$sXpath=$sXpath.$arrSetting['xpath'].'/'; 
				$arrCountItem[$sXpath]=array(); 
			}
			if(substr($key,0,5)=='item:')
			{	
				$this->getCreateItemCounttArray($arrSetting[$key],$sXpath,$arrCountItem[$sXpath]);
				$sXpathOld=$sXpath;
			}
		}
	}
	
	public function setCreateItemId($arrCountItem,$sXpath,&$arrCreateJson)
	{
		foreach($arrCountItem as $key=>$item)
		{
			if(substr($key,0,5)=='item:')
			{	
				$arrCreateJson[$key]='item-'.(count($item)+1);
				$this->setCreateItemId($arrCountItem[$key],$sXpath,$arrCreateJson);
				$sXpathOld=$sXpath;
			}
		}
	}
	
	public function getTempPath($sControllerName,$sViewPath,$sMenuId)
	{
		if( !class_exists($sControllerName) or !new $sControllerName() instanceof Controller)
		{
			$skey = "无此控制器";
			$this->view->createMessage(Message::error,"%s ",$skey);
			$this->getHistory();
			return;
		}
		else {
			$aController = new $sControllerName();
			$aController->frame();
		}
		
		// 检查视图
		if( !$aView = $aController->view()->findXPath($aController->view(),$sViewPath))
		{
			$skey = "无此视图";
			$this->view->createMessage(Message::error,"%s ",$skey);
			$this->getHistory();
			return;
		}
		
		// 检查菜单
		if( !$aMenu = $aView->widget($sMenuId) or !$aMenu instanceof Menu)
		{
			$skey = "无此菜单";
			$this->view->createMessage(Message::error,"%s ",$skey);
			$this->getHistory();
			return;
		}
		
		return $sTempPath = $aView->template();
	}
}

?>