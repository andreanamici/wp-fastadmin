<?php

namespace FastAdmin\actions;

use FastAdmin\lib\classes\FastAdminActions;

use FastAdmin\lib\classes\FastAdminForm;
use FastAdmin\lib\classes\FastAdminFormValidation;

/**
 * Business models heare
 */
use FastAdmin\models\ExampleModel;

class MainActions extends FastAdminActions
{
    /**
     * Form builder
     * 
     * @var FastAdminForm
     */
    protected $form;
    
    /**
     * Form validation library
     * 
     * @var FastAdminFormValidation
     */
    protected $form_validation;

    
    public function wp_init()
    {
        parent::wp_init();
        
        $this->form            = new FastAdminForm();
        $this->form_validation = new FastAdminFormValidation();
        
        $this->example_model                    = new ExampleModel();
        
        fa_set('form',$this->form);
        fa_set('form_validation', $this->form_validation);        
        fa_set('example_model', $this->example_model);
    }
}