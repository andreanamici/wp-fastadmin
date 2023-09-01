<?php

namespace FastAdmin\lib\classes;

class FastAdminFormValidation extends FastAdminCore
{
    /**
     * List of all rules to apply for data
     * @var array
     */
    protected $rules_data;
    
    /**
     * List of all rules
     * @var array
     */
    protected $rules;
    
    /**
     * Array of data to validate
     * @var array
     */
    protected $data;
    
    /**
     * Array of validations errors
     * @var array
     */
    protected $errors;
    
    /**
     * Array of validation messages
     * @var array
     */
    public $errors_messages;
    
    /**
     * Callback method context
     * @var mixed
     */
    protected $callback_context;
    
    /**
     * Callback called before form validation
     * @var \Closure 
     */
    protected $before_validation;
    
    /**
     * Callback called after form validation
     * @var \Closure 
     */
    protected $after_validation;
    
    public function __construct()
    {
        $configs                 = include WP_FA_BASE_PATH_CONFIGS.'/form_validation.php';      
        $data                    = !empty($configs['default_data']) ? $configs['default_data'] : fa_flash_get('form_validation_data', []);  

        $this->data              = $data;
        $this->errors_messages   = $configs['rules_messages'];
        $this->rules             = isset($configs['rules'])                   ? $configs['rules']                   : array();
        
        $this->before_validation = isset($configs['before_validation'])       ? $configs['before_validation']       : array();
        $this->after_validation  = isset($configs['after_validation'])        ? $configs['after_validation']        : array();
        $this->callback_context  = fa_get('actions');
        $this->rules_data        = array();        
        $this->errors            = fa_flash_get('fa_form_errors', []);
    }
    
    
    /**
     * Add a rule to field
     * 
     * @param string $field      field to validate
     * @param mixed  $rules      rules, string separated by "|" or array or rules
     * @param string $error_msg  custom error message, default null
     * 
     * @return FastAdminFormValidation
     */
    public function add_rule($field, $rules, $error_msg = null)
    {        
        $this->rules_data[$field][] = $rules;
        
        if($error_msg)
        {
            $this->errors_messages[$rules] = $error_msg;
            $this->errors_messages[$field] = $error_msg;
        }
      
        return $this;
    }
    
    /**
     * Get all rules for field
     * 
     * @param string $field
     * 
     * @return array
     */
    public function get_rules($field)
    {
        return isset($this->rules_data[$field]) ? $this->rules_data[$field] : array();
    }
    
    /**
     * Get error message
     * @param string $rule
     * @return string
     */
    public function get_error_message($rule)
    {
        return !empty($this->errors_messages[$rule]) ? $this->errors_messages[$rule] : $this->errors_messages[''];
    }
    
    /**
     * Get all fields errors
     * @return string
     */
    public function get_errors($field = null)
    {
        return empty($field) ? $this->errors : (isset($this->errors[$field]) ? $this->errors[$field] : array());
    }

    public function get_errors_string(){
        $errors_messages = '';
        foreach($this->errors as $errors){
            foreach($errors as $error){
                $errors_messages.= '<br/> '.$error;
            }
        }
        return $errors_messages;
    }

    public function set_after_validation($after = null){
        $this->after_validation = $after;
        return $this;
    }

    /**
     * Set manual form errors
     * @return FastAdminFormValidation
     */
    public function set_errors(array $errors, $setMessage = true)
    {
        $this->errors = $errors;
        
        if($setMessage){
            fa_message_set('error', 'Si prega di verificare i dati inseriti prima di procedere');
        }

        return $this;
    }
    
    /**
     * Set an message error for rule
     * @param string $rule
     * @param string $message
     * return FastAdminFormValidation
     */
    public function set_error_message($rule, $message)
    {
        $this->errors_messages[$rule] = $message;
    }
    
    /**
     * Return current validated data
     * 
     * @return array
     */
    public function get_data($field = null, $default = null)
    {
        if($field){
            return array_key_exists($field, $this->data) ? $this->data[$field] : $default;
        }
        
        return $this->data;
    }
    
    /**
     * Set current data to validate
     * 
     * @param array $data associative data to validate (default is $_POST)
     * 
     * @return FastAdminFormValidation
     */
    public function set_data($data)
    {
        $this->data = $data;
        return $this;
    }
    
    /**
     * Set callback context
     * 
     * @param mixed $context string or object reference
     * 
     * @return FastAdminFormValidation
     */
    public function set_callback_context($context)
    {
        $this->callback_context = $context;
        return $this;
    }
    
    /**
     * Run validation
     * @param type $data
     */
    public function validate(array $data = array())
    {
        $data = !empty($data) ? $data : $this->data;
//        if(empty($this->rules_data) || empty($data) || empty($this->rules))
//        {
//            return false;
//        }

        fa_flash_set('form_validation_data', $data);
        
        $this->errors = array();
        
        if($this->before_validation && is_callable($this->before_validation))
        {
           call_user_func_array($this->before_validation, array($this));
        }

        foreach($this->rules_data as $field => $rules)
        {                              
            $rulesArray = $this->_parse_rules($rules);
            
            if(!empty($rulesArray))
            {            
                $is_required = false;
                
                foreach($rulesArray as $rule)
                {
                    if($rule['name'] == 'required'){
                        $is_required = true;
                    }
                }

                $value = array_key_exists($field, $data) ? $data[$field] : null;

                if(!$is_required && $value === '')
                {
                    continue;
                }
                
                foreach($rulesArray as $rule)
                {
                    if(empty($rule['name']))
                    {
                        continue;
                    }

                    if(!empty($rule['name']) && !is_callable($rule['callable']))
                    {
                        wp_die('Rule "'.$rule['name'].'" is not valid, is not presente in configs file and is not a valid callable!');
                    }
                    
                    $rule['args'] = array_merge(array($value), $rule['args']);
                    
                    if(!call_user_func_array($rule['callable'], $rule['args']))
                    {
                        $errorMessage           = $this->get_error_message($rule['name']);
                        $this->errors[$field][] = isset($this->errors_messages[$field]) ? $this->errors_messages[$field] : $this->_format_error($errorMessage, $rule['args']);
                        break;
                    }
                }
            }
        }
        
        $is_valid = count($this->errors) == 0;
        
        if($this->after_validation && is_callable($this->after_validation))
        {
           call_user_func_array($this->after_validation, array($is_valid, $this->errors));
        }
        
        fa_flash_set('fa_form_errors', $this->errors);

        return $is_valid;
    }
    
    /**
     * Parse rules from string/array
     * 
     * @param mixed $rules string rules or array or rules
     * 
     * @return array
     */
    protected function _parse_rules(array $rules)
    {
       $parsedRules = array();
       
       foreach($rules as $rule)
       {
            $rulesArray = is_string($rule) ? explode("|", $rule) : $rule;

            if(!is_array($rules))
            {
               return false; 
            }

            $rules = array();
            
            foreach($rulesArray as $ruleString)
            {
                $rule    = '';
                $args    = array();
                $context = $this;
                
                switch(true)
                {
                    case preg_match('/callback\_([A-z\_]+)\[(.*?)\]/', $ruleString, $matches):
                        $rule    = $matches[1];
                        $args    = explode(',',$matches[2]);
                        $context = $this->callback_context;
                    break;
                    case preg_match('/callback\_([A-z\_]+)/', $ruleString, $matches): 
                        $rule    = $matches[1];
                        $context = $this->callback_context;
                    break;
                    case preg_match('/([A-z\_]+)\[(.*?)\]/', $ruleString, $matches): 
                         $rule = $matches[1];
                         $args = explode(',',$matches[2]);
                    break;
                    case preg_match('/([A-z\_]+)/', $ruleString, $matches): 
                         $rule = $matches[1];
                    break;
                }
                
                $parsedRules[] = array(
                    'name'     => $rule,
                    'callable' => isset($this->rules[$rule]) ? $this->rules[$rule] : array($context, $rule),
                    'args'     => $args,
                    'context'  => $this->callback_context,
                );
            }
       }
       
       return $parsedRules;
    }
    
    protected function _format_error($message, $args)
    {
        return vsprintf($message, $args);
    }

}