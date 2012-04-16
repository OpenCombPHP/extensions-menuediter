<?php 
return array(
	'class' => 'menu' ,
	'id' => 'mainMenu' ,
	'direction' => 'h' ,
	
	// items
	'item:OA'=>array(
		'title' => 'OA',
		'link' => '?c=org.opencomb.coresystem.system.DebugStatSetting' ,
		'query' => 'c=org.opencomb.coresystem.system.DebugStatSetting' ,
		'menu' => 1,
			'item:extension-setup' => array(
				'title'=>'公文收发' ,
				'link' => '?c=org.opencomb.coresystem.system.ExtensionSetupController' ,
				'query' => 'c=org.opencomb.coresystem.system.ExtensionSetupController' ,
			) ,
			'item:extension-manage' => array(
				'title'=>'协同办公' ,
				'link' => '?c=org.opencomb.coresystem.system.ExtensionManagerController' ,
				'query' => 'c=org.opencomb.coresystem.system.ExtensionManagerController' ,
			) ,
			'item:platform-upgrade' => array(
				'title'=>'文档管理' ,
				'link' => '?c=org.opencomb.coresystem.system.PlatformUpgrade' ,
				'query' => 'c=org.opencomb.coresystem.system.PlatformUpgrade' ,
			) ,
			'item:platform-rebuiild' => array(
				'title'=>'公共信息' ,
				'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
				'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
	)
) ;
