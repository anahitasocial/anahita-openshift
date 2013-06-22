<?php 
$args = $_SERVER['argv'];
array_shift($args);

global $code, $root;

$root = array_shift($args);
if ( empty($root) ) {
	exit(0);
}

//Anahita
//$site_translation = realpath(dirname(__FILE__).'/../code/site/language/en-GB');

//App
$app = 'subscriptions';
$site_translation = realpath(dirname(__FILE__).'/../../../'.$app.'/trunk/code/site/language');

if($d_handle = opendir($site_translation))
{
	while(false !== ($filename = readdir($d_handle)))
	{
		if(substr($filename, -3, 3) == 'ini')
		{
			$file_path	= $site_translation.'/'.$filename;
			$f_handle	= fopen($file_path, "r");
			$content	= fread(fopen($file_path, "r"), filesize($file_path));
		
			$content = explode("\n", $content);
			
			foreach($content as $line)
			{
				if( in_array(substr($line, 0, 3), array('LIB', 'COM', 'MOD') ))
				{
					$line = explode('=', $line);
					$translation_tag = $line[0];
	
				//	$apps = array('anahita', 'connect', 'groups', 'topics', 'photos', 'todos', 'pages', 'subscriptions');
					$apps = array($app);
					$isUsed = false;
					
					foreach($apps as $app)
						if(isUsed($app, $translation_tag))
						{
							$isUsed = true;
							break;
						}

					if(!$isUsed)
						print $translation_tag."\n";	
				}				
			}
			
			fclose($f_handle);
		}
	}
}

function isUsed($app, $translation_tag)
{
	$cmdstr = 'grep -r --exclude="*\.svn*" --exclude="*.ini" \''.$translation_tag.'\' ';
	
	if($app == 'anahita')
		$cmdstr .= realpath(dirname(__FILE__).'/../code');
	else 
		$cmdstr .= realpath(dirname(__FILE__).'/../../../'.$app.'/trunk/code');
		
//	print $cmdstr."\n";	
		
	$fp = popen($cmdstr, "r");
	$resultArray = array();
	$buffer = fgetss($fp, 4096);	
	fclose($fp);
	
	return $buffer;
}