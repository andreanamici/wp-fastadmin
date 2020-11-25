<?php

if(!function_exists('fa_form_error'))
{
    /**
     * Render validation errors
     * 
     * @param string $field
     * @param bool   $return, default false
     * 
     * @return string
     */
    function fa_form_error($field, $return = false)
    {                
        $fa_form   = fa_get('form');
        $errorHTML = '';
        if($errors = $fa_form->get_errors($field))
        {
            $errorHTML.='<ul class="fa-form-field-errors">';
            foreach($errors as $error){
                $errorHTML.= '<li>'.$error.'</li>';
            }
            $errorHTML.='</ul>';
        }
        
        
        if($return)
        {
            return $errorHTML;
        }
        
        echo $errorHTML;
    }
}

if(!function_exists('fa_form_data'))
{
    /**
     * Render form field current data
     * 
     * @param string $field
     * @param mixed  $default, default null
     * @param bool   $return, default false
     * 
     * @return type
     */
    function fa_form_data($field, $default = null, $return = false)
    {
        $fa_form = fa_get('form');
        
        $value = $fa_form->get_data($field, $default);
        
        if($return)
        {
            return $value;
        }
        
        echo $value;
    }
}



if ( ! function_exists('fa_form_parse_attributes'))
{
    /**
    * Parse the form attributes
    *
    * Helper function used by some of the form helpers
    *
    * @param	array	$attributes	List of attributes
    * @param	array	$default	Default values
    * @return	string
    */
    function fa_form_parse_attributes($attributes, $default = array())
    {
        if (is_array($attributes))
        {
                foreach ($default as $key => $val)
                {
                        if (isset($attributes[$key]))
                        {
                                $default[$key] = $attributes[$key];
                                unset($attributes[$key]);
                        }
                }
                if (count($attributes) > 0)
                {
                        $default = array_merge($default, $attributes);
                }
        }
        $att = '';
        foreach ($default as $key => $val)
        {
                if ($key === 'value')
                {
                        $val = fa_html_escape($val);
                }
                elseif ($key === 'name' && ! strlen($default['name']))
                {
                        continue;
                }
                $att .= $key.'="'.$val.'" ';
        }
        return $att;
    }
}
