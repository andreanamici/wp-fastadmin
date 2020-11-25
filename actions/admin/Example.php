<?php

namespace FastAdmin\actions\admin;

use FastAdmin\lib\classes\FastAdminActions;
use FastAdmin\lib\classes\FastAdminListTable;
use FastAdmin\models\MessagesModel;

class Example extends AdminActions
{
    public function index()
    {        
       $listing = $this->customers_model->get_listing();
       
       return $this->render('example/index', array(
                    'listing'          => $listing,   
                    'title_actions'    => array(
                        array('name' => 'Example','href' => $this->get_action_path('slug')),
                    ),
                    'breadcrumb' => $this->get_breadcrumb(array('main'))
              ));
    }
    
     
    public function view($id)
    {
        $customer = $this->customers_model->get_record($id);
                
        if(!$customer)
        {
            wp_die('Non è possibile trovare il cliente indicato!');
            exit;
        }

        $customer['can_send_medical_certificate_reminder'] = $this->customers_model->can_send_medical_certificate_reminder($id);
        
        $products_listing = $this->customers_products_model->get_customers_products_listing($id);
        $payments_listing = $this->payments_model->get_payments_listing($id);
        $messages_listing = $this->messages_model->get_messages_listing($id);
        $files_listing = $this->customers_files_model->get_customers_files_listing($id);
        $appountments_listing = $this->appointments_model->get_appointments_listing($id);
        
        return $this->render('customers/view', array(
                   'id' => $id,
                   'page'   => $this->page,
                   'customer'         => $customer,
                   'products_listing' => $products_listing,
                   'payments_listing' => $payments_listing,
                   'messages_listing' => $messages_listing,
                   'files_listing' => $files_listing,
                   'appointments_listing' => $appountments_listing,
                   'breadcrumb'       => $this->get_breadcrumb(array('main','customers'))
               ));
    }
    
    public function add()
    {
        return $this->_add_edit();
    }
    
    
    public function edit($customer_id = null)
    {
        return $this->_add_edit($customer_id);
    }
    
    public function select()
    {
        $nextpage = isset($_GET['next']) ? $_GET['next'] : null;
        $pageinfo = fa_get('resources')->get_page($nextpage);
        
        if(!$pageinfo)
        {
            wp_die('Pagina fornita non valida: '.$nextpage);
            exit;
        }
        
        $params = array('page' => $nextpage);
        
        foreach($_GET as $key => $value){
            if(!in_array($key, array('page','next'))){
                $params[$key] = $value;
            }
        }
        
        $customers = $this->customers_model->get_records_dropdown();
        
        if(empty($customers) || count($customers) == 1)
        {
            fa_page_redirect_with_message('customers', array(), 'warning', 'Attenzione non sono presenti Clienti attivi da poter selezionare!');
        }
        
        $required = true;
        
        switch($nextpage)
        {
            case 'fa_appointments_add':
                $required = false;
                $customers[0] = 'test';
            break;
        }
        
        return $this->render('customers/select',array(
                        'params'      => $params ,
                        'next'        => $pageinfo['real_slug'],
                        'breadcrumb'  => $this->get_breadcrumb(array('customers')),
                        'required'    => $required,
                        'customers'   => $customers
               ));
    }
    
     
    public function delete($id)
    {
        $customer = $this->customers_model->get_record($id);
        
        if(!$customer || $customer['deleted_datetime'])
        {
            fa_page_redirect_with_message('customers',array(),'warning','Questo cliente non è disponibile');
        }
        
        $this->customers_model->delete($id);
        fa_page_redirect_with_message('customers',array(),'success','Cliente eliminato correttamente');
    }    
    
    
    public function send_medical_certificate_reminder($id)
    {
        $customer  = $this->customers_model->get_record($id);
        
        if(!$customer || $customer['deleted_datetime'])
        {
            fa_page_redirect_with_message('customers',array(),'warning','Questo cliente non è disponibile');
        }
        
        $trigger_name = 'customer_medical_certificate_reminder';
        $vars         = $customer;
        
        $send         = false;
        $where        = null;
        $msg          = array();
        
        $trigger      = $this->triggers_email_model->get_trigger_by_name($trigger_name);
        $trigger      = $this->triggers_email_model->parse_trigger($trigger,$vars);
        
        if($customer['phone']) //Try to send an sms
        {
            $send  = fa_sms_send($customer['phone'], $trigger['text']);
            
            if($send)
            {
                $this->messages_model->add_sms(array(
                    'plain_text'    => $trigger['text'],
                    'to'            => $customer['phone'],
                    'from'          => WP_FA_SMS_SENDER,
                    'customer_id'   => $id
                ));
            }
        }
        
        if(!$send && $customer['email']) //Try to send an e-mail
        {
            $send  = fa_email($customer['email'],$trigger['subject'],$trigger['html']);
            
            if($send)
            {
                $this->messages_model->add_email(array(
                    'plain_text'    => $trigger['text'],
                    'html'          => $trigger['html'],
                    'subject'       => $trigger['subject'],
                    'to'            => $customer['email'],
                    'from'          => WP_FA_EMAIL_SENDER_EMAIL,
                    'customer_id'   => $id
                ));
            }
        }
        
        if($send)
        {
            $customer['medical_certificate_reminder_datetime'] = fa_date_now();
            $this->customers_model->save($customer);
        }
        
        $page   = isset($_REQUEST['next']) ? $_REQUEST['next'] : $to['menu_slug'];
        $params = array('id' => $id);
        
        fa_page_redirect_with_message($page, $params, $send ? 'success' : 'error', $send ? 'Il cliente è stata avvisato correttamente via '.$msg['type'] : 'Non è stato possibile inviare nessun avviso al cliente, ne sms ne e-mail');
    }   
    
    protected function _add_edit($customer_id = null)
    {
         $customer = array();
         
         if($customer_id)
         {
             $customer = $this->customers_model->get_record($customer_id);
             
             if(!$customer || $customer['deleted_datetime'])
             {
                 wp_die('Cliente non disponibile');
             }
         }
         
         $customer['dob'] = isset($customer['dob']) ? fa_date_user('IT_DATETIME', $customer['dob']) : null;
         
         $this->form->init(array(
             
                            'id'            => 'fa-products',
                            'method'        => 'POST',
                            'action'        => '',
             
         ))->set_initial_data($customer);
         
         $this->form->add_field('name', array( 
                            'label' => 'Nome',
                            'attrs' => array( 'placeholder' => 'Nome cliente' ,'class' => 'fa-input-medium'),
                            'rules' => array('required') 
                    ))
                    ->add_field('last_name', array( 
                            'label' => 'Cognome',
                            'attrs' => array( 'placeholder' => 'Cognome cliente' ,'class' => 'fa-input-medium'),
                            'rules' => array('required') 
                    ))
                    ->add_field('gender', array( 
                            'label'   => 'Sesso',
                            'type'    => 'radio',
                            'options' => array(  'm' => 'Uomo','w' => 'Donna' ),
                            'rules'   => array('required') 
                    ))
                    ->add_field('has_medical_certificate', array( 
                            'label'   => 'Ha portato il certificato medico?',
                            'type'    => 'radio',
                            'default_value' => 0,
                            'options' => array(  1 => 'Si',0 => 'No' ),
                    ))
                    ->add_field('email', array( 
                            'label' => 'E-mail',
                            'type'  => 'email',
                            'attrs' => array( 'placeholder' => 'Indirizzo e-mail del cliente' ,'class' => 'fa-input-medium'),
                            'rules' => array('email') 
                    ))
                    ->add_field('phone', array( 
                            'label' => 'Telefono',
                            'type'  => 'phone',
                            'attrs' => array( 'placeholder' => 'Recapito telefonico','class' => 'fa-input-medium' ),
                            'rules' => array('phone') 
                    ))
                    ->add_field('dob', array( 
                            'label' => 'Data di nascita',
                            'attrs' => array( 'placeholder' => 'Quando è nato il cliente?','class' => 'fa-input-medium fa-datepicker' ),
                            'rules' => array('date') 
                    ))
         ;
         
         if($this->form->is_submitted() && $this->form->validate())
         {  
            $customer                = $this->form->get_data();
            $customer['customer_id'] = $customer_id;
            $customer['dob']         = !empty($customer['dob']) ? fa_date_to_sql($customer['dob']) : null; 
            
            $id = $this->customers_model->save($customer);

            return fa_page_redirect_with_message('customers_view', array('id' => $id), $id ? 'success' : 'error', $id ? 'Operazione effettuata con successo' : 'Operazione fallita');
         }
        
         return $this->render('customers/add_edit', array(
                   'customer'   => $customer,
                   'form'       => $this->form->render(),
                   'breadcrumb' => $this->get_breadcrumb(array('main','customers'))
               )); 
    }
}