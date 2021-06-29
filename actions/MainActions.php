<?php

namespace FastAdmin\actions;

use FastAdmin\lib\classes\FastAdminActions;

use FastAdmin\lib\classes\FastAdminForm;
use FastAdmin\lib\classes\FastAdminFormValidation;

/**
 * Business models heare
 */
use FastAdmin\models\LeadsModel;
use FastAdmin\models\CustomersModel;
use FastAdmin\models\MessagesModel;
use FastAdmin\models\ProductsModel;
use FastAdmin\models\CustomersProductsModel;
use FastAdmin\models\PaymentsModel;
use FastAdmin\models\StatsModel;
use FastAdmin\models\AppointmentsModel;
use FastAdmin\models\CustomersFilesModel;
use FastAdmin\models\TriggersEmailModel;
use FastAdmin\models\CashCounterModel;
use FastAdmin\models\CashCounterOperationsModel;
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