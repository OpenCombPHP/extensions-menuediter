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
	) ,
	'item:user' => array(
		'title' => '人力资源系统' ,
		'link'=>'?c=org.opencomb.coresystem.user.AdminUsers' ,
		'query'=>'c=org.opencomb.coresystem.user.AdminUsers' ,
		
		// items 
		'menu' => 1,
		'item:user-manager' => array(
			'title'=>'人事管理' ,
			'link'=>'?c=org.opencomb.coresystem.user.AdminUsers' ,
			'query' => array(
				'c=org.opencomb.coresystem.user.AdminUsers' ,
				'c=org.opencomb.coresystem.auth.PurviewSetting&type=user' ,
				'c=org.opencomb.coresystem.auth.PurviewTester' ,
				'c=org.opencomb.coresystem.user.UserGroupsSetting' ,
			) ,
		) ,
	) ,
	'item:caiwu'=>array(
		'title' => '财务管理系统',
		'link' => '?c=org.opencomb.coresystem.system.DebugStatSetting' ,
		'query' => 'c=org.opencomb.coresystem.system.DebugStatSetting' ,
		'menu' => 1,
			'item:extension-setup' => array(
				'title'=>'账务' ,
				'link' => '?c=org.opencomb.coresystem.system.ExtensionSetupController' ,
				'query' => 'c=org.opencomb.coresystem.system.ExtensionSetupController' ,
			) ,
			'item:extension-manage' => array(
				'title'=>'应收' ,
				'link' => '?c=org.opencomb.coresystem.system.ExtensionManagerController' ,
				'query' => 'c=org.opencomb.coresystem.system.ExtensionManagerController' ,
			) ,
			'item:platform-upgrade' => array(
				'title'=>'应付' ,
				'link' => '?c=org.opencomb.coresystem.system.PlatformUpgrade' ,
				'query' => 'c=org.opencomb.coresystem.system.PlatformUpgrade' ,
			) ,
			'item:platform-rebuiild' => array(
				'title'=>'电子报表' ,
				'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
				'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:bank' => array(
					'title'=>'现金银行' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:systemmanage' => array(
					'title'=>'系统管理' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
	) ,
	'item:caiwuyitihua'=>array(
			'title' => '财务业务一体化系统',
			'link' => '?c=org.opencomb.coresystem.system.DebugStatSetting' ,
			'query' => 'c=org.opencomb.coresystem.system.DebugStatSetting' ,
			'menu' => 1,
			'item:extension-setup' => array(
					'title'=>'账务' ,
					'link' => '?c=org.opencomb.coresystem.system.ExtensionSetupController' ,
					'query' => 'c=org.opencomb.coresystem.system.ExtensionSetupController' ,
			) ,
			'item:extension-manage' => array(
					'title'=>'应收' ,
					'link' => '?c=org.opencomb.coresystem.system.ExtensionManagerController' ,
					'query' => 'c=org.opencomb.coresystem.system.ExtensionManagerController' ,
			) ,
			'item:platform-upgrade' => array(
					'title'=>'应付' ,
					'link' => '?c=org.opencomb.coresystem.system.PlatformUpgrade' ,
					'query' => 'c=org.opencomb.coresystem.system.PlatformUpgrade' ,
			) ,
			'item:platform-rebuiild' => array(
					'title'=>'电子报表' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:bank' => array(
					'title'=>'现金银行' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:systemmanage' => array(
					'title'=>'采购管理' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:xiaoshouguanli' => array(
					'title'=>'销售管理' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:systemmanage' => array(
					'title'=>'库存管理' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:systemmanage' => array(
					'title'=>'存货管理' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:systemmanage' => array(
					'title'=>'领导系统' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
			'item:systemmanage' => array(
					'title'=>'系统管理' ,
					'link' => '?c=org.opencomb.coresystem.system.RebuildPlatform' ,
					'query' => 'c=org.opencomb.coresystem.system.RebuildPlatform' ,
			) ,
	) ,
	'item:zichanmannage' => array(
			'title' => '资产管理系统' ,
			'link'=>'?c=org.opencomb.coresystem.user.AdminUsers' ,
			'query'=>'c=org.opencomb.coresystem.user.AdminUsers' ,
	
			// items
			'menu' => 1,
			'item:user-manager' => array(
					'title'=>'资产管理' ,
					'link'=>'?c=org.opencomb.coresystem.user.AdminUsers' ,
					'query' => array(
							'c=org.opencomb.coresystem.user.AdminUsers' ,
							'c=org.opencomb.coresystem.auth.PurviewSetting&type=user' ,
							'c=org.opencomb.coresystem.auth.PurviewTester' ,
							'c=org.opencomb.coresystem.user.UserGroupsSetting' ,
					) ,
			) ,
	) ,
	'item:vidosystem' => array(
			'title' => '视频监控系统' ,
			'link'=>'?c=org.opencomb.coresystem.user.AdminUsers' ,
			'query'=>'c=org.opencomb.coresystem.user.AdminUsers' ,
	
			// items
			'menu' => 1,
			'item:user-manager' => array(
					'title'=>'视频监控' ,
					'link'=>'?c=org.opencomb.coresystem.user.AdminUsers' ,
					'query' => array(
							'c=org.opencomb.coresystem.user.AdminUsers' ,
							'c=org.opencomb.coresystem.auth.PurviewSetting&type=user' ,
							'c=org.opencomb.coresystem.auth.PurviewTester' ,
							'c=org.opencomb.coresystem.user.UserGroupsSetting' ,
					) ,
			) ,
	) ,
	'item:shengchanmanage'=>array(
			'title' => '生产管理平台',
			'link' => '?c=org.opencomb.coresystem.system.DebugStatSetting' ,
			'query' => 'c=org.opencomb.coresystem.system.DebugStatSetting' ,
			'menu' => 1,
			'item:extension-setup' => array(
					'title'=>'实时数据库' ,
					'link' => '?c=org.opencomb.coresystem.system.ExtensionSetupController' ,
					'query' => 'c=org.opencomb.coresystem.system.ExtensionSetupController' ,
			) ,
			'item:extension-manage' => array(
					'title'=>'生产管理平台' ,
					'link' => '?c=org.opencomb.coresystem.system.ExtensionManagerController' ,
					'query' => 'c=org.opencomb.coresystem.system.ExtensionManagerController' ,
			) ,
			'item:platform-upgrade' => array(
					'title'=>'报表展示' ,
					'link' => '?c=org.opencomb.coresystem.system.PlatformUpgrade' ,
					'query' => 'c=org.opencomb.coresystem.system.PlatformUpgrade' ,
			) ,
	)
) ;
