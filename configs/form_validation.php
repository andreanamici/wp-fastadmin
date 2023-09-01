<?php

/**
 * Form validation configurations
 */
return array(
    
    'default_data'   => $_POST,
    'default_locale' => 'en_US',
    
    'before_validation' => function(\FastAdmin\lib\classes\FastAdminFormValidation $formvalidation){},
    'after_validation'  => function($status){ !$status ?  fa_message_set('error', 'Attenzione, verificare i dati inseriti') : null; },
            
    'rules' => array(
        'required'                 => function($value){ return strlen($value) > 0; },
        'email'                    => function($value){ return filter_var($value,FILTER_VALIDATE_EMAIL) !== false; },
        'min_length'               => function($value, $length){ return strlen(trim($value)) >= $length; },
        'max_length'               => function($value, $length){ return strlen(trim($value)) <= $length; },
        'greater_than'             => function($value, $criteria){ return filter_var($value, FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT) !== false && $value > $criteria; },
        'greater_than_field'       => function($value, $input_name){ $criteria = filter_var(fa_get('form')->get_data($input_name), FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT); return $value > $criteria; },
        'greater_equal_than'       => function($value, $criteria){ return filter_var($value, FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT) !== false && $value >= $criteria; },
        'greater_equal_than_field' => function($value, $input_name){ $criteria = filter_var(fa_get('form')->get_data($input_name), FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT); return $value >= $criteria; },
        'less_than'                => function($value, $criteria){ return filter_var($value, FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT) !== false && $value < $criteria; },
        'less_than_field'          => function($value, $input_name){ $criteria = filter_var(fa_get('form')->get_data($input_name), FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT); return $value > $criteria; },
        'less_equal_than'          => function($value, $criteria){ return filter_var($value, FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT) !== false && $value <= $criteria; },
        'less_equal_than_field'    => function($value, $input_name){ $criteria = filter_var(fa_get('form')->get_data($input_name), FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT); return $value <= $criteria; },
        'number'                   => function($value){ return filter_var($value, FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT) !== false; },
        'amount'                   => function($value){ return filter_var($value, FILTER_VALIDATE_INT|FILTER_VALIDATE_FLOAT) !== false; },
        'phone'                    => function($value){ return preg_match('/[0-9]+/',$value); },
        'vatnumber'                => function($value){ return preg_match('/[0-9]{11}/',$value); },
        'taxcode'                  => function($value){ return preg_match('/[0-9]{16}/',$value); },
        'date'                     => function($value){ return fa_date_is_valid(fa_date_to_sql($value)); },
        'date_gt_today'            => function($value){ return fa_date_is_valid(fa_date_to_sql($value)) && strtotime(fa_date_to_sql($value).' 00:00:00') > time(); },
        'date_gte_today'           => function($value){ return fa_date_is_valid(fa_date_to_sql($value)) && strtotime(fa_date_to_sql($value).' '.date("H:i:s")) >= strtotime(date('Y-m-d').' 00:00:00'); },
        'date_range'               => function($value, $input_name){ return fa_date_is_valid(fa_date_to_sql($value)) && strtotime(fa_date_to_sql($value).' 00:00:00') >= strtotime(fa_date_to_sql(fa_get('form')->get_data($input_name)).' 00:00:00'); },
        'datetime'                 => function($value){ return fa_datetime_to_sql($value); },
        'datetime_future'          => function($value){ return fa_datetime_to_sql($value) && strtotime(fa_datetime_to_sql($value)) > time(); },
        'valid_name'               => function($value){ return preg_match('/^[A-z\s\']+$/', $value); }
    ),

    // Using translations
    //
    // 'rules_messages' =>  array(
    //     ''                           =>  _f('This field is not valid', 'Form validation'),
    //     'required'                   =>  _f('This field is required', 'Form validation'),
    //     'email'                      =>  _f('E-mail is not valid', 'Form validation'),
    //     'min_length'                 =>  _f('Min length is %2$s characters', 'Form validation'),
    //     'max_length'                 =>  _f('Max length is %2$s characters', 'Form validation'),
    //     'greater_than'               =>  _f('Field must be greater than %2$s'),
    //     'greater_than_field'         =>  _f('Field must be greater or equal than "%3$s"', 'Form validation'),
    //     'greater_equal_than'         =>  _f('Field must be greater or equal than %2$s', 'Form validation'),
    //     'greater_equal_than_field'   =>  _f('Field must be greater or equal than "%3$s"', 'Form validation'),
    //     'less_than'                  =>  _f('Field must be lower than %2$s', 'Form validation'),
    //     'less_than_field'            =>  _f('Field must be lower than "%3$s"', 'Form validation'),
    //     'less_equal_than'            =>  _f('Field must be lower or equalt than %2$s', 'Form validation'),
    //     'less_equal_than_field'      =>  _f('Field must be lower or equalt than "%3$s"', 'Form validation'),
    //     'number'                     =>  _f('This field must be numeric', 'Form validation'),
    //     'amount'                     =>  _f('Amunt not valid, separate digits by "."', 'Form validation'),
    //     'phone'                      =>  _f('Phone number not valid', 'Form validation'),
    //     'vatnumber'                  =>  _f('Vat number not valid', 'Form validation'),
    //     'date'                       =>  _f('Date is not valid', 'Form validation'),
    //     'date_gte_today'             =>  _f('Date must be greater or equal than today', 'Form validation'),
    //     'date_gt_today'              =>  _f('Date must be greater than today', 'Form validation'),
    //     'date_range'                 =>  _f('Time interval not valid', 'Form validation'),
    //     'taxcode'                    =>  _f('Tax code not valid', 'Form validation'),
    //     'datetime'                   =>  _f('Date time is not valid', 'Form validation'),
    //     'datetime_future'            =>  _f('Date time must be in the future', 'Form validation'),
    //     'valid_name'                 =>  _f('Name not valid', 'Form validation'),
    // )

    'rules_messages' => array(
            
            'it_IT' => array(
                ''                           => 'campo non valido',
                'required'                   => 'Questo campo è obbligatorio',
                'email'                      => 'L\'indirizzo e-mail non è valido',
                'min_length'                 => 'La lunghezza minima richiesta è di %2$s caratteri',
                'max_length'                 => 'La lunghezza massima è di %2$s caratteri',
                'greater_than'               => 'Il valore deve essere maggiore di %2$s',
                'greater_than_field'         => 'Il valore deve essere maggiore al campo "%3$s"',
                'greater_equal_than'         => 'Il valore deve essere maggiore o uguale a %2$s',
                'greater_equal_than_field'   => 'Il valore deve essere maggiore o uguale al campo "%3$s"',
                'less_than'                  => 'Il valore deve essere minore di %2$s',
                'less_than_field'            => 'Il valore deve essere minore del campo "%3$s"',
                'less_equal_than'            => 'Il valore deve essere minore o uguale a %2$s',
                'less_equal_than_field'      => 'Il valore deve essere minore o uguale al campo "%3$s"',
                'number'                     => 'Questo campo deve essere numerico',
                'amount'                     => 'Importo non valido, dividere i centesimi con il "."',
                'phone'                      => 'Numero di telefono non valido',
                'vatnumber'                  => 'Partita IVA non valida',
                'date'                       => 'La data non è valida',
                'date_gte_today'             => 'La data deve essere successiva o uguale ad oggi',
                'date_gt_today'              => 'La data deve essere successiva ad oggi',
                'date_range'                 => 'Intervallo temporale non valido',
                'taxcode'                    => 'Codice fiscale non valido',
                'datetime'                   => 'Data ora non valida',
                'datetime_future'            => 'La data e ora deve essere successiva ad ora',
                'valid_name'                 => 'Questo valore non è valido'
            ),
            'en_US' => array(
                ''                           => 'field not valid',
                'required'                   => 'Field is required',
                'email'                      => 'Mail address not valid',
                'min_length'                 => 'Min length is %2$s characters',
                'max_length'                 => 'Max length is %2$s characters',
                'greater_than'               => 'Value must be greater than %2$s',
                'greater_than_field'         => 'Value must be greater than "%3$s"',
                'greater_equal_than'         => 'Value must be greater or equal than %2$s',
                'greater_equal_than_field'   => 'Value must be greater or equal than  "%3$s"',
                'less_than'                  => 'Value must be less than %2$s',
                'less_than_field'            => 'Value must be less than "%3$s"',
                'less_equal_than'            => 'Value must be less or equal than %2$s',
                'less_equal_than_field'      => 'Value must be less or equal than "%3$s"',
                'number'                     => 'This field must be numeric',
                'amount'                     => 'Value not valid, must be a valid currency',
                'phone'                      => 'Phone number invalid',
                'vatnumber'                  => 'Vat number invalid',
                'date'                       => 'Date not valid',
                'date_gte_today'             => 'Date must be greater or equal than today',
                'date_gt_today'              => 'Date must be greater than today',
                'date_range'                 => 'Date range not valid',
                'taxcode'                    => 'Fiscal code not valid',
                'datetime'                   => 'Date time is invalid',
                'datetime_future'            => 'Date time must be greater than now',
                'valid_name'                 => 'This value is not valid'
            )        
    ),
);