<?php 
//requires Facebook Webdriver PHP wrapper
//https://github.com/facebook/php-webdriver
 
function get_options()
{
    $options = json_decode('{'.str_replace("\n",',',trim(file_get_contents(dirname(__FILE__).'/config.txt'))).'}');
    return $options;
}

$options = get_options();

require_once($options->webdriver.'/__init__.php');
define('DEV_SITE_URL',  $options->target->url);
define('DEV_SITE_PATH', $options->target->path);
$url = parse_url(DEV_SITE_URL);
$_SERVER['HTTP_HOST'] = $url['host'];
require_once 'cli.php';

$wd_host    = 'http://localhost:4444/wd/hub'; // this is the default

global $web_driver, $user;

$web_driver = new WebDriver($wd_host);
$user       = get_options()->user;

function split_keys($toSend) 
{
    $payload = array("value" => preg_split("//u", $toSend, -1, PREG_SPLIT_NO_EMPTY));
    return $payload;
}

register_shutdown_function('close_session');

function get_url($url = '')
{
    if ( is_array($url) ) {
        $url = http_build_query($url);
    }    
    return DEV_SITE_URL.'/index.php?'.$url;
}

function close_session()
{
   /*
   global   $session;
   if ( $session ) { 
       $session->close();
   }*/
}

function get_current_session()
{
    global $web_driver;
    $sessions = $web_driver->sessions();
    if ( count($sessions) > 0 )
    {
        $session = $sessions[count($sessions) -  1];
        return $session;
    }    
}


function get_session($username = null, $password = null)
{
   global $web_driver, $session, $user;
   
   if ( !$username ) 
   {
       $username = $user->username;
       $password = $user->password;
   }
   
   $current_session = get_current_session();

   if ( $current_session ) 
   {       
       if ( $username != @$current_session->username )
           $current_session->close();
       else
           return $current_session;
   }
     
   $session  = $web_driver->session('firefox', array('javascriptEnabled' => true, 'version' => '3.6'));
   $cookie_crumbs = array("name" => "username",
           "value" => $username,
           "path" => "/",
           "secure" => false);
   
   $session->setCookie($cookie_crumbs);  
   $session->open(get_url('option=com_user&view=login'));
   $session->element("id", "username")->value(split_keys($username));
   $session->element('id','passwd')->value(split_keys($password));
   $session->element('id','passwd')->submit();
   return $session;
}

function element($using, $value)
{
    return get_session()->element($using, $value);
}

function fill_out_form($form, $elements)
{
    if ( is_string($form) ) {
        $session = get_session();
        $form    = $session->element('css selector', $form);
    }
    
    foreach($elements as $name => $value) {
        $form->element('css selector','*[name="'.$name.'"]')->value(split_keys($value));
    }
    return $form;
}

?>