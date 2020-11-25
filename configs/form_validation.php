<?php

/**
 * Form validation configurations
 */
return array(
    
    'default_data'   => $_POST,
    'default_locale' => 'it_IT',
    
    'before_validation' => function(\FastAdmin\lib\classes\FastAdminFormValidation $formvalidation){},
    'after_validation' => function($status){ !$status ?  fa_message_set('error', 'Attenzione, verificare i dati inseriti') : null; },
            
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
    ),
                
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
                'datetime_future'            => 'La data e ora deve essere successiva ad ora'
            )        
    ),
);