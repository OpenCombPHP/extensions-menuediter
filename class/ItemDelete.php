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

class ItemDelete extends ControlPanel
{
	public function createBeanConfig()
	{
		return array(
			'title'=> '文章内容',
			'view:itemDelete'=>array(
				'template'=>'ItemDelete.html',
				'class'=>'form',
				'widgets' => array(
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
		
	}
}

?>