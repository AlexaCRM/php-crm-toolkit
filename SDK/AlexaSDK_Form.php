<?php

/* LiveID authentication class is not supported */

if (!class_exists("AlexaSDK_Form")) :
    
    
    class AlexaSDK_Form extends AlexaSDK_Abstract{
    
        protected $conn;
        
        protected $entity;
    
        /**  @var String entityLogicalName this is how Dynamics refers to this Form */
	protected $formLogicalName = NULL;
	/** @var String entityDisplayName the field to use to display the entity's Name */
	protected $formDisplayName = NULL;
        
        /**  @var String entityLogicalName this is how Dynamics refers to this Entity */
        protected $entityLogicalName = NULL;
	/** @var String entityDisplayName the field to use to display the entity's Name */
	protected $entityDisplayName = NULL;
    
        /**
	 * 
	 * @param AlexaSDK $auth Connection to the Dynamics CRM server - should be active already.
	 * @param String $_logicalName Allows constructing arbritrary Entities by setting the EntityLogicalName directly
	 */
        function __construct(AlexaSDK $auth, AlexaSDK_Entity $entity, $_formLogicalName = NULL) {
            
                if (self::$debugMode){ echo "Creating object".get_class($this); }
                
                $this->conn = $auth;
                
                $this->entity = $entity;
            
                /* If a new LogicalName was passed, set it in this Entity */
		if ($_formLogicalName != NULL && $_formLogicalName != $this->formLogicalName) {
			/* If this value was already set, don't allow changing it. */
			if ($this->formLogicalName != NULL) {
				throw new Exception('Cannot override the Form Logical Name on a strongly typed Form');
			}
			/* Set the Logical Name */
			$this->formLogicalName = $_formLogicalName;
		}
                /* Check we have a Logical Name for the Entity */
		if ($this->formLogicalName == NULL) {
			throw new Execption('Cannot instantiate an abstract Entity - specify the Logical Name');
		}
                
                
        }

    }
    
    
endif;