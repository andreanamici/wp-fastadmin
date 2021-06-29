<?php

namespace FastAdmin\actions\admin;

use FastAdmin\lib\classes\FastAdminActions;
use FastAdmin\lib\classes\FastAdminListTable;
use FastAdmin\models\MessagesModel;

class Example extends AdminActions
{
    public function index()
    {        
       $listing = $this->example_model->get_listing();
       
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
        $example = $this->example_model->get_record($id);
                
        if(!$example)
        {
            wp_die('Object not found: Example::'.$id);
            exit;
        }
        
        return $this->render('customers/view', array(
                   'id'              => $id,
                   'example'         => $example,
                   'breadcrumb'      => $this->get_breadcrumb(array('main','examples'))
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
     
    public function delete($id)
    {
        $example = $this->example_model->get_record($id);
        
        if(!$example || $example['deleted_datetime'])
        {
            fa_page_redirect_with_message('examples',array(),'warning','Example not available');
        }
        
        $this->example_model->delete($id);
        fa_page_redirect_with_message('examples',array(),'success','Example delete successfull');
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
                            'label' => 'Name',
                            'attrs' => array( 'placeholder' => 'Name' ,'class' => 'fa-input-medium'),
                            'rules' => array('required') 
                    ))
                    ->add_field('last_name', array( 
                            'label' => 'Lastname',
                            'attrs' => array( 'placeholder' => 'Last name' ,'class' => 'fa-input-medium'),
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