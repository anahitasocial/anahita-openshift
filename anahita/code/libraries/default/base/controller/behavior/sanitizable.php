<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2011 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id: resource.php 11985 2012-01-12 10:53:20Z asanieyan $
 * @link       http://www.anahitapolis.com
 */

/**
 * Sanitizable Behavior. Provides an easy to use the KFilter to filter data
 *
 * @category   Anahita
 * @package    Lib_Base
 * @subpackage Controller_Behavior
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
class LibBaseControllerBehaviorSanitizable extends KControllerBehaviorAbstract
{
    /**
     * Sanitizes the value using the passed in fitler. The fitler can be Koowa filter
     * or KIdetifier or KFilterInterface
     *
     * @param mixed  $value  The value to sanitize
     * @param string $filter The filter to use to sanitize the value
     * 
     * @return mixed Return the sanitized value
     */
    public function sanitize($value, $filter = 'cmd')
    {
        if ( $value instanceof KConfig)
            $value = $value->toArray();
    
        if ( !strpos($filter, '.') )
        {
            $filter = 'koowa:filter.'.$filter;
        }
    
        return $this->getService($filter)->sanitize($value);
    }	
}