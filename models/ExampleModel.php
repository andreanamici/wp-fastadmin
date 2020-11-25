<?php

namespace FastAdmin\models;

use FastAdmin\lib\classes\FastAdminListTable;

class ExampleModel extends BaseModel
{
    public function __construct()
    {
        parent::__construct(array(
            'table_name'         => '<db_table_name>',
            'field_id'           => '<db_primary_key>',
            'field_creation'     => '<db_field_creation_datetime>',
            'field_deleted'      => '<db_field_deleted_datetime>',
            'field_label_sql'    => 'CONCAT(field1," ",field2) as label',
            'field_label'        => 'label',
        ));
    }
    
    public function get_customer_by_lead_id($lead_id)
    {
        return $this->get_record_by(array('lead_id' => $lead_id));
    }
    
    public function get_listing()
    {
        $listing = new FastAdminListTable(array(
                            'model'   => $this,
                            'orderby' => 'last_name',
                            'order'   => 'ASC',
                            'columns' => array(
                                '<field_id>' => array(
                                    'title' => 'ID',
                                    'sortable' => true,
                                    'actions' => function($item){
                                        $actions         = array();
                                        $actions['view'] = sprintf('<a href="%s">View item</a>', fa_action_url('<slug>',array('id' => $item['ID'])));
                                        return $actions;
                                    }  
                                ),

                                'field' => array(
                                    'title'      => 'Name',
                                    'sortable'   => true,
                                    'filter'     => array(
                                        'type' => 'text',
                                        'placeholder' => 'Search By',
                                    )
                                ),
                                        
                               'field2' => array(
                                    'title' => 'Cert. Med.',
                                    'sortable'   => true,
                                    'modifier'   => function($value){
                                        return $value ? 'Si' : 'No';
                                    },
                                    'filter' => array('type' => 'select', 'options' => array(0=> 'No',1=>'Si')),
                                ),

                                $this->field_creation=> array(
                                    'title'      => 'Date and Time',
                                    'sortable'   => true,
                                    'filter' => array(
                                        'type'         => 'between', 'input_type' => 'date', 'class' => 'fa-datepicker' ,'placeholder' => array('from' => 'Dal', 'to' => 'Al'),
                                        'fieldname'    => 'DATE('.$this->field_creation.')',
                                        'filter_value' => function($value){
                                                return array('from' => $value['from'] ? fa_date_to_sql($value['from']) : null, 'to' => $value['to'] ? fa_date_to_sql($value['to']) : null);
                                        },
                                    ),
                                    'modifier'   => function($value){
                                        return date('d/m/Y H:i',strtotime($value));
                                    }
                                ),
                                        
                                FastAdminListTable::COLUMN_ACTIONS => array(
                                    'content' => function($item){
                                        $actions = array();
                                        $actions['action1'] = sprintf('<a href="%s"  title="Do action" class="button-secondary">Action</a>',fa_action_path('slug',array('id' => $item['ID'])), $item['field']);
                                        return implode('&nbsp;',$actions);
                                    }
                                )
                            )
                      ));
       
       $listing->prepare_items();
       
       return $listing;
    }
}