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

class MainActions extends FastAdminActions
{
    /**
     * Leads Model
     * 
     * @var LeadsModel
     */
    protected $leads_model;
    
    /**
     * Customers model
     * 
     * @var CustomersModel
     */
    protected $customers_model;
    
    /**
     * Messages model
     * 
     * @var MessagesModel
     */
    protected $messages_model;
    
    /**
     * Products model
     * 
     * @var ProductsModel
     */
    protected $products_model;
    
    /**
     * Products Customers model
     * 
     * @var CustomersProductsModel 
     */
    protected $customers_products_model;
    
    
    /**
     * Products Customers Files model
     * 
     * @var CustomersFilesModel
     */
    protected $customers_files_model;
    
    /**
     * Payments Model
     * 
     * @var PaymentsModel 
     */
    protected $payments_model;
    
    /**
     * Appointments Model
     * 
     * @var AppointmentsModel
     */
    protected $appointments_model;
    
    /**
     * Stats Model
     * 
     * @var StatsModel
     */
    protected $stats_model;
    
    /**
     * TriggersEmailModel Model
     * 
     * @var TriggersEmailModel 
     */
    protected $triggers_email_model;
    
    /**
     * CashCounterModel Model
     * 
     * @var CashCounterModel
     */
    protected $cashcounter_model;
    
     /**
     * CashCounterOperationsModel Model
     * 
     * @var CashCounterOperationsModel
     */
    protected $cashcounter_operations_model;
    
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
        
        $this->leads_model                    = new LeadsModel();
        $this->customers_model                = new CustomersModel();
        $this->messages_model                 = new MessagesModel();
        $this->products_model                 = new ProductsModel();
        $this->payments_model                 = new PaymentsModel();
        $this->customers_products_model       = new CustomersProductsModel();
        $this->stats_model                    = new StatsModel();
        $this->appointments_model             = new AppointmentsModel();
        $this->customers_files_model          = new CustomersFilesModel();
        $this->triggers_email_model           = new TriggersEmailModel();
        $this->cashcounter_model              = new CashCounterModel();
        $this->cashcounter_operations_model   = new CashCounterOperationsModel();
        
        fa_set('form',$this->form);
        fa_set('form_validation', $this->form_validation);        
        fa_set('leads_model', $this->leads_model);
        fa_set('customers_model', $this->customers_model);
        fa_set('messages_model', $this->messages_model);
        fa_set('products_model', $this->products_model);
        fa_set('payments_model', $this->payments_model);
        fa_set('customers_products_model',$this->customers_products_model);
        fa_set('customers_files_model', $this->customers_files_model);
        fa_set('stats_model', $this->stats_model);
        fa_set('appointments_model', $this->appointments_model);
        fa_set('triggers_email_model', $this->triggers_email_model);
        fa_set('cashcounter_model', $this->cashcounter_model);
        fa_set('cashcounter_operations_model', $this->cashcounter_operations_model);
    }
    
    /**
     * Send sms or e-mail to a customer by triggers email text and html content
     * 
     * @param string $name
     * @param array  $customer
     * @param array  $vars
     * 
     * @return string
     */
    protected function _send_sms_or_email_by_trigger($name, array $customer, array $vars = array())
    {
        $send = false;
        
        if($customer['phone']){
            $send = fa_sms_trigger($name,$customer['phone'], $vars) ? 'sms' : '';
        }
        
        if(!$send && $customer['email']){
            $send = fa_email_trigger($name, $customer['email'], $vars) ? 'e-mail' : '';
        }

        return $send;
    }
}