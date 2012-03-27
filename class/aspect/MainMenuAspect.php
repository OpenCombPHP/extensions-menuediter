<?php
namespace org\opencomb\menuediter\aspect;

use org\jecat\framework\bean\BeanFactory;
use org\jecat\framework\lang\aop\jointpoint\JointPointMethodDefine;

class MainMenuAspect
{
	/**
	 * @pointcut
	 */
	public function pointcutCreateBeanConfig()
	{
		return array(
			new JointPointMethodDefine('org\\opencomb\\coresystem\\mvc\\controller\\ControlPanelFrame','createBeanConfig') ,
		) ;
	}
	
	/**
	 * @advice around
	 * @for pointcutCreateBeanConfig
	 */
	private function createBeanConfig()
	{
		// 调用原始原始函数
		$arrConfig = aop_call_origin() ;
		$arrConfig['frameview:frameView']['widget:mainMenu']['item:system']['item:platform-manage']['item:菜单管理']
							=array(
									'title'=>'菜单管理' ,
									'link' => '?c=org.opencomb.menuediter.MenuOpen' ,
									'query' => 'c=org.opencomb.menuediter.MenuOpen' ,
									);
										
		return $arrConfig ;
	}
}
?>
