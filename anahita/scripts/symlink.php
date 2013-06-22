<?php 
$args = $_SERVER['argv'];
array_shift($args);
global $code, $root;
$root = array_shift($args);
if ( empty($root) ) {
    $root = realpath(dirname(__FILE__).'/../../../../site');
    if ( !file_exists($root) ) {
        $root = null;
    }
}
if ( empty($root) ) {
	exit(0);
}
$code = realpath(dirname(__FILE__).'/../code');

$symlinks = array(
	'vendors/mc/rt_missioncontrol_j15' => 'administrator/templates/rt_missioncontrol_j15',            
);

joomla_symlinks($symlinks);
nooku_symlinks($symlinks);
anahita_symlinks($symlinks);

foreach($symlinks as $target => $link) 
{
	if ( is_numeric($target) ) {
		$target = $link;
		$link   = str_replace('site/','', $link);
	}
	$link   = $root.'/'.$link;
	$target = $code.'/'.$target;
	`rm -rf $link`;
	if ( !is_dir($link) && !file_exists(dirname($link)) )
	{	     
	     mkdir(dirname($link), 0707, true);    
	}
	`ln -nsf $target $link`;
	print "linking $link\n";
}
$root = preg_replace('/\/$/','', $root);

$deadlinks = explode("\n", trim(`find -L {$root} -type l -lname '*'`));
foreach($deadlinks as $deadlink) {
    print "unlinking $deadlink\n";
    unlink($deadlink);
}


function nooku_symlinks(&$links)
{
    $postfix = "_trunk/code";
    $postfix = "";
    $links = array_merge($links, array(
        "vendors/nooku$postfix/administrator/components/com_default" => "administrator/components/com_default",
        "vendors/nooku$postfix/administrator/modules/mod_default" => "administrator/modules/mod_default",
        "vendors/nooku$postfix/site/components/com_default" => "components/com_default",
        "vendors/nooku$postfix/site/modules/mod_default" => "modules/mod_default",
        "vendors/nooku$postfix/media/lib_koowa" => "media/lib_koowa",
        "vendors/nooku$postfix/plugins/koowa" => "plugins/koowa",
        "vendors/nooku$postfix/plugins/system/koowa.php" => "plugins/system/koowa.php",
        "vendors/nooku$postfix/plugins/system/koowa.xml" => "plugins/system/koowa.xml",
        "vendors/nooku$postfix/libraries/koowa" => "libraries/koowa",
        "vendors/nooku$postfix/media/com_default" => "media/com_default",
        "vendors/nooku$postfix/media/lib_koowa" => "media/lib_koowa"            
    ));
}
function anahita_symlinks(&$links)
{
    $links = array_merge($links, array(
        'media/lib_anahita' => 'media/lib_anahita',
        'media/com_stories',
        'media/com_composer',            
        'libraries/anahita' => 'libraries/anahita',
        'plugins/profile/abstract.php' => 'plugins/profile/abstract.php',
        'plugins/storage',
        'plugins/contentfilter',
        'plugins/installer',            
        'plugins/system/anahita.php' => 'plugins/system/anahita.php',
        'plugins/system/anahita.xml' => 'plugins/system/anahita.xml',
        'plugins/system/debug.php' => 'plugins/system/debug.php',
        'plugins/system/debug.xml' => 'plugins/system/debug.xml',        
        'cli',
        'manifest.xml',
        'administrator/components/com_base',
        'administrator/components/com_notifications',
        'administrator/components/com_apps',
        'administrator/components/com_bazaar',
        'administrator/language/en-GB/en-GB.com_apps.ini',
        'administrator/language/en-GB/en-GB.tpl_shiraz.ini',
        'libraries/default/base',
        'libraries/default/people',
        'libraries/default/users',
        'libraries/default/theme',
        'site/templates/shiraz',
        'site/templates/base',
        'site/modules/mod_base',
        'site/modules/mod_viewer',        
        'site/components/com_dashboard',
        'site/components/com_actors',
        'site/components/com_medium',
        'site/components/com_comments',
        'site/components/com_people',
        'site/components/com_stories',
        'site/components/com_apps',
        'site/components/com_notifications',
        'site/components/com_socialapp',
        'site/components/com_composer',            
        'site/components/com_base',
        'site/language/en-GB/en-GB.tpl_shiraz.ini',
        'site/language/en-GB/en-GB.com_notifications.ini',
        'site/language/en-GB/en-GB.com_stories.ini',
        'site/language/en-GB/en-GB.com_dashboard.ini',
        'site/language/en-GB/en-GB.com_people.ini',
        'site/language/en-GB/en-GB.lib_anahita.ini',
        'site/language/en-GB/en-GB.com_actors.ini',            
        'site/language/en-GB/en-GB.lib_anahita.js',
        'site/language/en-GB/en-GB.mod_viewer.ini'
    ));
}

function joomla_symlinks(&$links)
{
    $links = array_merge($links, array(            
         'vendors/joomla/plugins/authentication/joomla.php' => 'plugins/authentication/joomla.php',
         'vendors/joomla/plugins/user/joomla.php' => 'plugins/user/joomla.php',
         'vendors/joomla/libraries/joomla' => 'libraries/joomla',
         'vendors/joomla/media/system' => 'media/system',
         'vendors/joomla/includes/js'  => 'includes/js',
         'vendors/joomla/language/en-GB/en-GB.ini'  => 'language/en-GB/en-GB.ini',
         'vendors/joomla/includes/application.php'  => 'includes/application.php',
		 'vendors/joomla/modules/mod_mainmenu'      => 'modules/mod_mainmenu',    		
         'vendors/joomla/modules/mod_login'   => 'modules/mod_login',
         'vendors/joomla/modules/mod_breadcrumbs'   => 'modules/mod_breadcrumbs',
         'vendors/joomla/administrator/components/com_modules' => 'administrator/components/com_modules',
         'vendors/joomla/administrator/components/com_templates' => 'administrator/components/com_templates',
         'vendors/joomla/administrator/components/com_menus' => 'administrator/components/com_menus',
    	'vendors/joomla/administrator/modules/mod_mainmenu'      => 'administrator/modules/mod_mainmenu',    		
         'vendors/joomla/administrator/components/com_admin' => 'administrator/components/com_admin',
    	 'vendors/joomla/administrator/components/com_config' => 'administrator/components/com_config',
    	 'vendors/joomla/administrator/components/com_admin' => 'administrator/components/com_admin',    		    		         
         'vendors/joomla/components/com_user' => 'components/com_user',
         'vendors/joomla/templates/system' => 'templates/system',
    	 'vendors/joomla/components/com_content' => 'components/com_content',                        
    ));    
}