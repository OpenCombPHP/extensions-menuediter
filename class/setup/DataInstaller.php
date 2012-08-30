<?php
namespace org\opencomb\menuediter\setup;

use org\jecat\framework\db\DB ;
use org\jecat\framework\message\Message;
use org\jecat\framework\message\MessageQueue;
use org\opencomb\platform\ext\Extension;
use org\opencomb\platform\ext\ExtensionMetainfo ;
use org\opencomb\platform\ext\IExtensionDataInstaller ;
use org\jecat\framework\fs\Folder;

// 这个 DataInstaller 程序是由扩展 development-toolkit 的 create data installer 模块自动生成
// 扩展 development-toolkit 版本：0.2.0.0
// create data installer 模块版本：1.0.7.0

class DataInstaller implements IExtensionDataInstaller
{
	public function install(MessageQueue $aMessageQueue,ExtensionMetainfo $aMetainfo)
	{
		$aExtension = new Extension($aMetainfo);
		
		// 1 . create data table
		$aDB = DB::singleton();
		
		
		// 2. insert table data
		
		
		// 3. settings
		
		$aSetting = $aExtension->setting() ;
			
				
		$aSetting->setItem('/menu/','coresystem:FrontFrame.html-mainMenu',array (
  'item:index' => 
  array (
    'xpath' => 'item:index',
    'title' => '首页',
    'link' => '?c=index',
    'menu' => 0,
    'query' => '',
  ),
  'item:org.jecat.framework.mvc.view.widget.menu.Item42' => 
  array (
    'xpath' => 'item:org.jecat.framework.mvc.view.widget.menu.Item42',
    'title' => '设计作品',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=2',
    'menu' => 0,
    'query' => 
    array (
      0 => 'cid=2',
      1 => 'cid=2',
    ),
  ),
  'item:org.jecat.framework.mvc.view.widget.menu.Item43' => 
  array (
    'xpath' => 'item:org.jecat.framework.mvc.view.widget.menu.Item43',
    'title' => '公司新闻',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=3',
    'menu' => 0,
    'query' => 
    array (
      0 => 'cid=3',
      1 => 'cid=3',
    ),
  ),
  'item:org.jecat.framework.mvc.view.widget.menu.Item44' => 
  array (
    'xpath' => 'item:org.jecat.framework.mvc.view.widget.menu.Item44',
    'title' => '国际',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=6',
    'menu' => 0,
    'query' => 
    array (
      0 => 'cid=6',
      1 => 'cid=6',
    ),
  ),
  'item:org.jecat.framework.mvc.view.widget.menu.Item45' => 
  array (
    'xpath' => 'item:org.jecat.framework.mvc.view.widget.menu.Item45',
    'title' => '国内',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=5',
    'menu' => 0,
    'query' => 
    array (
      0 => 'cid=5',
      1 => 'cid=5',
    ),
  ),
  'item:org.jecat.framework.mvc.view.widget.menu.Item46' => 
  array (
    'xpath' => 'item:org.jecat.framework.mvc.view.widget.menu.Item46',
    'title' => '体育',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=7',
    'menu' => 0,
    'query' => 
    array (
      0 => 'cid=7',
      1 => 'cid=7',
    ),
  ),
  'item:org.jecat.framework.mvc.view.widget.menu.Item47' => 
  array (
    'xpath' => 'item:org.jecat.framework.mvc.view.widget.menu.Item47',
    'title' => '军事',
    'link' => '?c=org.opencomb.opencms.article.ArticleList&cid=8',
    'menu' => 0,
    'query' => 
    array (
      0 => 'cid=8',
      1 => 'cid=8',
    ),
  ),
  'id' => 'mainMenu',
  'class' => 'menu',
));
				
		$aMessageQueue->create(Message::success,'保存配置：%s',"/menu/");
			
				
		$aSetting->setItem('/','data-version','0.2.0');
				
		$aMessageQueue->create(Message::success,'保存配置：%s',"/");
			
		
		
		// 4. files
		
		$sFromPath = $aExtension->metainfo()->installPath().'/data/public';
		$sDestPath = $aExtension ->filesFolder()->path();
		Folder::RecursiveCopy( $sFromPath , $sDestPath );
		$aMessageQueue->create(Message::success,'复制文件夹： `%s` to `%s`',array($sFromPath,$sDestPath));
		
	}
}
