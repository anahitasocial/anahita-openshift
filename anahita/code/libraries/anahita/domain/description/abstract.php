<?php

/** 
 * LICENSE: ##LICENSE##
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Description
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @copyright  2008 - 2010 rmdStudio Inc./Peerglobe Technology Inc
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @version    SVN: $Id$
 * @link       http://www.anahitapolis.com
 */

/**
 * Entity Description
 *
 * Contains properrty/aliases/classname and other information about an entity
 * 
 * @category   Anahita
 * @package    Anahita_Domain
 * @subpackage Description
 * @author     Arash Sanieyan <ash@anahitapolis.com>
 * @author     Rastin Mehr <rastin@anahitapolis.com>
 * @license    GNU GPLv3 <http://www.gnu.org/licenses/gpl-3.0.html>
 * @link       http://www.anahitapolis.com
 */
abstract class AnDomainDescriptionAbstract 
{
    /**
     * The abstract identifier
     * 
     * @var string
     */
    protected $_abstract_identifier;
    
	/**
	 * Property Description
	 * 
	 * @var array
	 */
	protected $_properties = array();

	/**
	 * The identity property
	 * 
	 * @var AnDomainAttributeScalar 
	 */
	protected $_identity_property;
		
	/**
	 * Type column
	 * 
	 * @var string 
	 */
	protected $_inheritance_column;	
	
	/**
	 * Inheritance column value
	 * 
	 * @var string
	 */
	protected $_inheritance_column_value;
	
	/**
	 * Property Alias
	 * 
	 * @var array
	 */
	protected $_alias;
	
	/**
	 * Key Properties
	 * 
	 * @var array
	 */
	protected $_keys = array();
	
	/**
	 * Entity identifier
	 * 
	 * @var KServiceIdentifier
	 */
	protected $_entity_identifier;
	
	/**
	 * An array of unique identifiers
	 * 
	 * @var array
	 */
	protected $_unique_identifiers;
	
	/**
	 * An associative array of class alias. This value is used when
	 * building the class heirarchy (tree) in the inheritance tree
	 * 
	 * @var array
	 */
	protected $_class_alias;
	
	/**
	 * Repository
	 * 
	 * @var AnDomainRepositoryAbstract
	 */
	protected $_repository;
	
	/**
	 * Retains an array of has relatinships
	 * 
	 * @var array
	 */
	static private $__has_relationships;
	
	/** 
     * Constructor.
     *
     * @param KConfig $config An optional KConfig object with configuration options.
     * 
     * @return void
     */ 
    public function __construct(KConfig $config)
    {
		$this->_initialize($config);
						
		if ( !empty($config->aliases) ) 
			foreach($config->aliases as $alias => $property)
				$this->setAlias($property, $alias);
		
		$this->_entity_identifier   = $config->entity_identifier;
		$this->_repository   = $config->repository;
		$this->_inheritance_column  = $config->inheritance_column;
		$this->_class_alias         = $config->class_alias;
		
		if ( !$this->_repository ) {
		    throw new AnDomainDescriptionException("repository [AnDomainRepositoryAbstract] option is required");
		}
		
		if ( is_string($config->inheritance_column) ) {
		    $config->inheritance_column = $this->_repository->getResources()->getColumn($config->inheritance_column);		   
		}

		$this->_inheritance_column      = $config->inheritance_column;		
		$this->_identity_property       = $config->identity_property;		
		$this->_entity_identifier       = $this->_repository->getIdentifier($config->entity_identifier);
		
		//an object can only be abstract if it's 
		//supports single table inheritance
		if ( $this->_inheritance_column ) {
		    $this->_abstract_identifier = $config->abstract_identifier;
		}
		
		//if there's no propoerty set
		//then set the properties automatically from the its columns
		if ( $config->auto_generate )
		{
		    $attributes = array();
		    $attributes[$this->_identity_property] = array('key'=>true);		    
		    $columns = $this->_repository->getResources()->main()->getColumns();
		    foreach($columns as $column) {
		        $attributes[KInflector::variablize($column->name)] = array('required'=>$column->required, 'column'=>$column, 'type'=>$column->type, 'default'=>$column->default);
		    }
		    $config['attributes'] = $attributes;
		}
	}
	
    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
	protected function _initialize(KConfig $config)
	{
	    $entity_identifier = $config->entity_identifier;
   	    	   
		$config->append(array(
		    'auto_generate'            => false,
			'identity_property'        => 'id',
			'aliases'	 		       => array()
		));		
	}	

	/**
	 * Return whether the entity is an abstract entity. Abstract entties can only be
	 * extended and there should be no instances of them in the database
	 *
	 * @return boolean
	 */
	public function isAbstract()
	{
	    return is_null($this->getInheritanceColumnValue()->getIdentifier());
	}
	
	/**
	 * Set a property description
	 * 
	 * @param  AnDomainPropertyAbstract $property The property to set
	 * 
	 * @return AnDomainDescriptionAbstract
	 */
	public function setProperty($property)
	{
		$this->_properties[$property->getName()] = $property;
		return $this;
	}
	
	/**
	 * Return a property or all the properties if name is null. if a property is not defined
	 * it will look in the parent descritpion to see if the property is 
	 * defined in them. If yes, it will add it the list of its properties
	 * 
	 * @param string  $name   The name of the property
	 *  
	 * @return AnDomainPropertyAbstract
	 */
	public function getProperty($name = null)
	{
		if ( is_null($name) ) {
			return $this->_properties;
		}

		$name     = pick($this->getAlias($name), $name);
		$property = null;
		
		if ( isset($this->_properties[$name]) )  
		{
		    $property = $this->_properties[$name];
		}
		elseif ( isset(self::$__has_relationships[$name]) ) 
		{
		    //check if any of the classes are the parent class
		    //if yes, then the relationship also propegates to the
		    //children
		    $classes = self::$__has_relationships[$name];
		    foreach($classes as $class => $instance) 
		    {
		        if ( is_subclass_of($this->_entity_identifier->classname, $class)  )
		        {
		            //clone the property 
		            $property = clone $instance;		            
                    $this->_properties[$name] = $property;
                    break;
		        }
		    }		    
		}
				
		return $property;
	}	
	
	/**
	 * Set an alias for a property
	 *
	 * @param string $property The property name
	 * @param string $alias    The alias for hte property
	 * 
	 * @return AnDomainDescriptionAbstract
	 */
	public function setAlias($property, $alias)
	{
		$this->_alias[$alias] = $property;
		return $this;
	}
	
	/**
	 * Return an alias for a property
	 *
	 * @return sting
	 */
	public function getAlias($property)
	{
	    $alias = null;
	    
	    if ( isset($this->_alias[$property]) ) 
	    {
	        $alias = $this->_alias[$property];
	    }
	    
	    return $alias;
	}

	/**
	 * Set a property as a key
	 *
	 * @param AnDomainPropertyKeyable $property The property to be used as the key
	 * 
	 * @return void
	 */
	public function setKey($property)
	{
		if ( $property->isSerializable() )
			$this->_keys[$property->getName()] = $property;
			
		//the proeprty as required
		$property->setRequired(true);
		
		return $this;
	}
	
	/**
	 * Return the entity repository
	 *
	 * @return AnDomainRepositoryAbstract
	 */
	public function getRepository()
	{
	    return $this->_repository;
	}
	
	/**
	 * Return an array of properties that uniquely define an entity
	 * 
	 * @return array
	 */
	public function getKeys()
	{
		return $this->_keys;		
	}
	
	/**
	 * Set the identity property (id)
	 *
	 * @param AnDomainAttributeScalar|string $property
	 * @return AnDomainDescriptionAbstract
	 */
	public function setIdentityProperty($property)
	{
		if ( is_string($property) ) {
			$property = $this->getProperty($property);
			if ( !$property )
				return;
		}
		
		$this->setKey($property);
		
		//don't allow direct write
		$property->setWriteAccess(AnDomain::ACCESS_PRIVATE);
		
		$this->_identity_property = $property;
				
		return $this;
	}
	
	/**
	 * Return an identity property that uniquely identifies an entity
	 * 
	 * @return AnDomainAttributeScalar
	 */
	public function getIdentityProperty()
	{
		if ( is_string($this->_identity_property) )
			$this->setIdentityProperty($this->_identity_property);
			
		return $this->_identity_property;
	}
	
	/**
	 * Return the type column value 
	 *
	 * @return AnDomainDescriptionInheritance
	 */
	public function getInheritanceColumnValue()
	{
	    if ( !isset($this->_inheritance_column_value) )
	    {
	        $classname = $this->getEntityIdentifier()->classname;
	        $classes   = get_parents($classname, 'AnDomainEntity');
	        $classes[] = $classname;
	        //remove the first class because it's parent and
	        //in STI (single table inheritane) we don't store the 
	        //orginal enitty class
	        array_shift($classes);
	        foreach($classes as $key => $class)
	        {
	            if ( isset($this->_class_alias[$class]) ) 
	            {
	                $class = ucfirst($this->_class_alias[$class]);
	            }
	            if ( empty($class) )
	                unset($classes[$key]);
	            else 
	            {
	                $classes[$key] = $class;
	            }
	        }
	        
	        //mak sure ther are no repeating classes. This could happen
	        //if some of the entities are extending the default
	        $classes = array_unique($classes);
	        
	        $identifier = clone $this->getEntityIdentifier();
	        $identifier->application = null;
	        $abstract   = (string) $identifier == $this->_abstract_identifier;
	        $this->_inheritance_column_value = new AnDomainDescriptionInheritance($classes, $abstract ? null : $identifier);	        
	    }
	    
	    return $this->_inheritance_column_value;
	}
	
	/**
	 * Return the type column that discriminates an entity
	 * 
	 * @return string
	 */
	public function getInheritanceColumn()
	{
		return $this->_inheritance_column;
	}
	
    /**
     * Return the entity class name
     *
     * @return KServiceIdentifier
     */
    public function getEntityIdentifier()
    {
        return $this->_entity_identifier;
    }
    
    /**
     * An array of string identifiers that uniquely idenitifies an entity such as class name, parent
     * classes and the entity service identifier
     *
     * @return array
     */
    public function getUniqueIdentifiers()
    {
        if ( !isset($this->_unique_identifiers) )
        {
            $classname = $this->getEntityIdentifier()->classname;
            $this->_unique_identifiers = get_parents($classname, 'AnDomainEntity');
            if ( strpos($classname, 'AnDomainEntity') !== 0 )
                $this->_unique_identifiers[] = $classname;
            $this->_unique_identifiers[] = (string) $this->getEntityIdentifier();
        }
        return $this->_unique_identifiers;
    }
    
    /**
     * Set an attribute or attributes property. It is possible to set an set of attributes by
     * passing an array as the name
     *
     * @param string $name    The name of the attributes or an array of name/option value
     * @param array  $config  The attribute options
     * 
     * @return AnDomainAttributeAbstract
     */
    public function setAttribute($name, $config = array())
    {        
        $attributes = is_string($name) ? array($name=>$config) : $name;
        $property   = null;
        foreach($attributes as $name => $config)
        {
            if ( is_numeric($name) ) 
            {
                $name    = $config;
                $config  = array();
            } 
            else if ( is_string($config) ) 
            {
                $config  = array('column'=>$config);
            }
            
            $config['name']   	    = pick($this->getAlias($name), $name);
            $config['description']  = $this;
            $property = AnDomainProperty::setAttribute(new KConfig($config));
        }
        return $property;
    }
    
    /**
     * Set a relationship or relationships property. It is possible to set an set of relationships by
     * passing an array as the name
     *
     * @param string $name    The name of the relationshp or an array of name/option value
     * @param array  $config  The relationship options
     *
     * @return AnDomainAttributeAbstract
     */
    public function setRelationship($name, $config = array())
    {
        $relationships = is_string($name) ? array($name=>$config) : $name;
        settype($relationships, 'array');
        $property = null;
        
        foreach($relationships as $name => $config)
        {
            if ( is_numeric($name) )
            {
                $name    = $config;
                $config  = array();
            }
            
            $config['name']   	    = pick($this->getAlias($name), $name);
            $config['description']  = $this;
            $property = AnDomainProperty::setRelationship(new KConfig($config));
            
            if ( $property instanceof AnDomainRelationshipOnetomany ) 
            {
                //if the entity is abstract then
                //keep track of all the has relationships
                //this relationship will propgate through children 
                if ( $this->isAbstract() ) {
                    self::$__has_relationships[$property->getName()][$this->_entity_identifier->classname] = $property;
                }
            }
        }
        return $property;
    }
    
    /**
     * Return all the  properties of type attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        $attributes = array();
    
        foreach($this->getProperty() as $name => $property)
            if ( $property->isAttribute() )
            $attributes[$name] = $property;
    
        return $attributes;
    }

    /**
     * Returns an array of relationships
     *
     * @return array
     */
    public function getRelationships()
    {
        $relationships = array();
    
        foreach($this->getProperty() as $name => $property)
            if ( $property->isRelationship() )
            $relationships[$name] = $property;
    
        return $relationships;
    }    
}