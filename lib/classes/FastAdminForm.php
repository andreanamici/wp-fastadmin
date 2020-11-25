<?php

namespace FastAdmin\lib\classes;

/**
 * FastAdmin forms generator and validator
 */
class FastAdminForm extends FastAdminFormValidation
{
    /**
     * Current form data
     * @var array
     */
    protected $form = array();
    
    /**
     * Current form method
     * @var string
     */
    protected $default_method = 'POST';
    
    /**
     * Template of form
     * @var string
     */
    protected $form_template = 'layout/forms/form';
    
    /**
     * Template of single field
     * @var string
     */
    protected $row_template = 'layout/forms/row';
    
    /**
     * Template of single field
     * @var string
     */
    protected $field_template = 'layout/forms/input';
    
    /**
     * Template of single action
     * @var string
     */
    protected $action_template = 'layout/forms/actions';
    
    /**
     * Current form initial data
     * 
     * @var form_data
     */
    protected $form_initial_data = array();
    
    public function __construct(array $form = array())
    {
        parent::__construct();
        
        $this->init($form);
    }
    
    /**
     * Initialize a new form
     * 
     * @param array $form form
     * 
     * @return FastAdminForm
     */
    public function init(array $form = array())
    {
        $form['method']  = isset($form['method']) ? $form['method'] : $this->default_method;
        $form['action']  = isset($form['action']) ? $form['action'] : '';
        
        $form['attrs']   = array_merge($form,array(
            'method' => $form['method'],
            'action' => $form['action'],
            'autocomplete'  => 'off',
            'class'  => 'fa-form'.(isset($form['attrs']['class'])  ? ' '.$form['class']  : ''),
            'id'     => isset($form['id'])     ? $form['id']     : 'fa-form-'. fa_get('actions')->get_page()
        ));
        
        $form['rows']    = array();
        
        $this->form = $form;
        
        if(!isset($form['actions']))
        {
            $this->add_action('submit','Invia', array(                
                    'input'  => 'input',
                    'type'   => 'submit'
            ));

            $this->add_action('reset', 'Annulla',array(                
                    'input'=> 'input',
                    'type' => 'reset'
            )); 
        }
        
            
        
        return $this;
    }
    
    /**
     * Get form row by name
     * 
     * @param string $name row name
     * 
     * @return array|null
     */
    public function get_row($name)
    {
        if(empty($this->form['rows'])){
            return null;
        }
        
        return isset($this->form['rows'][$name]) ? $this->form['rows'][$name] : null;
    }
    
    /**
     * Add a row to form
     * 
     * @param string $name          row input name
     * @param array  $info          row info, default array()
     * 
     * @return FastAdminForm
     */
    public function add_row($name,array $info = array())
    {        
        $row = array();
        
        if($this->get_row($name))
        {
            return $this;
        }
                
        $row  = array(
            'name'     => $name,
            'attrs'    => array(
                'class' => 'form-field'
            ),
            'fields'   => array(),
        );
        
        $row = array_merge($row, $info);
        
        $this->form['rows'][$name] = $row;
        
        return $this;
    }
    
    /**
     * Add a field to row
     * 
     * @param string $name      field name
     * @param array  $field     fields info
     * @param string $rowname   row name, default as fieldname
     * 
     * @return FastAdminForm
     */
    public function add_field($name,array $field, $rowname = null)
    {
        $rowname = $rowname ? $rowname : $name;
        
        if(!$this->get_row($rowname))
        {
            return $this->add_row($rowname)->add_field($name, $field, $rowname);
        }
        
        $field['type']   = isset($field['type']) ? $field['type'] : 'text';
        $field['attrs']  = array_merge(isset($field['attrs']) ? $field['attrs'] : array(), array(
                                        'type' => $field['type'],
                                        'name' => $name,
                                        'id'   => 'fa-form-field-'.$name
                                     ));  
        
        if(empty($this->form['rows'][$rowname]['fields']))
        {
            $this->form['rows'][$rowname]['fields'] = array();
        }
        
        if(!empty($field['rules']))
        {                        
            $this->add_rule($name, is_array($field['rules']) ? $field['rules'][0] : $field['rules'], isset($field['rules'][1]) ? $field['rules'][1] : null);
        }
        
        $this->form['rows'][$rowname]['fields'][$name] = $field;
        return $this;
    }
    
    /**
     * Add form action
     * 
     * @param string $name     action name
     * @param string $content  content
     * @param array  $action   info
     * 
     * @return FastAdminForm
     */
    public function add_action($name,$content, array $action)
    {
        if(empty($action['type']))
        {
            $action['type'] = $name;
        }
        
        if(empty($action['input']))
        {
            $action['input'] = 'input';
        }
        
        $action['content'] = $content;
        
        if(empty($this->form['actions'])){
            $this->form['actions'] = array();
        }
        
        $this->form['actions'][$name] = $action;
        return $this;
    }
    
    /**
     * Set initial data
     * 
     * @param array $data
     * 
     * @return FastAdminForm
     */
    public function set_initial_data(array $data)
    {
        $this->form_initial_data = $data;
        return $this;
    }
    
    /**
     * Return form initial data by field name
     * 
     * @param string $field     form field name
     * @param mixed  $default   default value
     * 
     * @return mixed
     */
    public function get_initial_data($field = null, $default = null)
    {
        if(!$field){
            return $this->form_initial_data;
        }
        
        if(array_key_exists($field, $this->form_initial_data)){
            return $this->form_initial_data[$field];
        }
        
        return $default;
    }
    
    /**
     * Return if form is submitted
     * 
     * @return 
     */
    public function is_submitted()
    {
        return $this->form['method'] == $_SERVER['REQUEST_METHOD'] || array_key_exists('submit', $this->data);
    }
    
    /**
     * Return form submitted and validated data
     * 
     * @param string $field     field to search, default all
     * @param mixed  $default   default value, default null
     * 
     * @return array
     */
    public function get_data($field = null, $default = null)
    {
        $data = parent::get_data($field, $default);
        return $data;
    }
    
    /**
     * Render form and return HTML content
     * 
     * @return string
     */
    public function render(array $options = array())
    {        
        $form_template   = isset($options['template'])        ? $options['template']        : 'common/' . $this->form_template;
        $row_template    = isset($options['row_template'])    ? $options['template']        : 'common/' . $this->row_template;
        $field_template  = isset($options['field_template'])  ? $options['field_template']  : 'common/' . $this->field_template;
        $action_template = isset($options['action_template']) ? $options['action_template'] : 'common/' . $this->action_template;
        
        $this->form['attrs_string'] = fa_form_parse_attributes($this->form['attrs']);
        
        foreach($this->form['rows'] as $rowname => $row)
        {
            $row['template']     = $row_template;
            $row['attrs_string'] = isset($row['attrs']) ? fa_form_parse_attributes($row['attrs']) : '';
            
            if(!empty($row['fields']))
            {
                foreach($row['fields'] as $fieldname => $field)
                {
                    $field['label'] = isset($field['label']) ? $field['label'] : array();
                    
                    if($field['label'])
                    {
                        if(is_string($field['label']))
                        {
                            $field['label'] = array('content' => $field['label']);
                        }
                        
                        $label_attrs        = isset($field['label']['attrs']) ? $field['label']['attrs'] : array();
                        $label_attrs['for'] = isset($label_attrs['for']) ? $label_attrs['for'] : $field['attrs']['id'];
                        
                        $field['label']['attrs']        = $label_attrs;
                        $field['label']['attrs_string'] = fa_form_parse_attributes($label_attrs);
                    }
                    
                    $field['errors']           = fa_form_error($fieldname,true);
                    $field['template']         = $field_template;
                    
                    $field_attrs               = isset($field['attrs']) ? $field['attrs'] : array();
                    
                    if(!empty($field['rules']))
                    {
                        $rulesArray = $this->_parse_rules($field['rules']);
                        
                        foreach($rulesArray as $rule)
                        {
                            if($rule['name'] == 'required')
                            {
                                $field_attrs['required'] = true;
                            }
                        }
                    }
                    
                    if($field['errors'])
                    {
                        $field_attrs['class'] = isset($field_attrs['class']) ? $field_attrs['class'].' fa-has-error' : 'fa-has-error';
                    }
                    
                    $field_attrs['value'] = fa_form_data($fieldname, $this->get_data($fieldname,$this->get_initial_data($fieldname)), true);
                    $field['selected']    = null;
                    
                    if(!empty($field['options']) && in_array($field['type'], array('select','radio')))
                    {
                        foreach($field['options'] as $key => $text)
                        {
                            $label_option_attr = !empty($field['label_option_attrs']) ? $field['label_option_attrs'] : array();
                            $field['label_option_attrs_string'] = fa_form_parse_attributes($label_option_attr);
                            
                            if((!isset($field_attrs['value']) || isset($field_attrs['value']) && $field_attrs['value'] == '') && isset($field['default_value']) && $field['default_value'] == $key){
                                $field['selected'] = $key;
                            }else if($field_attrs['value'] == $key){
                                $field['selected'] = $key;
                            }
                        }                        
                    }
                    
                    $field['value'] =  $field_attrs['value'];

                    if($field['attrs']['type'] == 'textarea' || in_array($field['type'], array('select','radio'))){
                        unset($field_attrs['value']);
                    }
                    
                    if(!empty($field['before']) && is_callable($field['before']))
                    {
                        $field['before'] = call_user_func_array($field['before'], array($this));
                    }
                    
                    if(!empty($field['after']) && is_callable($field['after']))
                    {
                        $field['after'] = call_user_func_array($field['after'], array($this));
                    }
                    
                    $field['attrs_string']     = fa_form_parse_attributes($field_attrs);
                    
                    $row['fields'][$fieldname] = $field;
                }
            }
            
            $this->form['rows'][$rowname] = $row;
        }
                        
        foreach($this->form['actions'] as $name => $action)
        {
            $action_attrs    = isset($action['attrs']) ? $action['attrs'] : array();
            $action['template'] = $action_template;
            $action['attrs'] = array_merge($action_attrs, array(
                'type'   => $action['type'],
                'name'   => $name,
                'class'  => 'button '.($action['type'] == 'submit' ? 'button-primary' : 'button-secondary'),
                'value'  => isset($action['value']) ? $action['value'] : $action['content']
            ));
            
            $action['attrs_string'] = fa_form_parse_attributes($action['attrs']);
            $this->form['actions'][$name] = $action;
        }
        
        return fa_resource_render($form_template, array(
                    'form'              => $this->form,
                    'errors'            => $this->errors
               ));
    }    
}