<?php

if(!function_exists('fa_email'))
{
    /**
     * Send HTML e-mail
     * 
     * @param mixed  $to
     * @param string $subject
     * @param string $message
     * @param string $text
     * @param array  $attachments
     * @param array  $headers
     * 
     * @return bool
     */
    function fa_email($to, $subject, $message, $text = null, $from_email = null, $from_name = null,$attachments = array(), array $headers = array())
    {
        $headers[] = 'Content-Type: text/html; charset=UTF-8';
         
        // Hooking up our functions to WordPress filters 
        add_filter( 'wp_mail_from', function() use($from_email){ return $from_email ? $from_email : WP_FA_EMAIL_SENDER_EMAIL; });
        add_filter( 'wp_mail_from_name', function() use($from_name){  return $from_name ? $from_name : WP_FA_EMAIL_SENDER_NAME;  });

        return wp_mail($to, $subject, $message, $headers, $attachments);
    }
    
}

if(!function_exists('fa_email_template'))
{
    /**
     * Send a e-mail from template resource in common/email
     * 
     * @param string $name          name of file to render in common/email folder
     * @param mixed  $to            email to
     * @param string $subject       the subjec
     * @param array  $data          data for trigger, default array()
     * @param array  $attachments   attachments, default array()
     * @param array  $headers       custom headers default array()
     * 
     * @return bool
     */
    function fa_email_template($name, $to, $subject, array $data = array(), $attachments = array(), array $headers = array())
    {
        $message = fa_resource_render('common/email/'.$name, $data);
        $text    = stripslashes($message);
        return fa_email($to, $subject, $message, $text, $attachments, $headers);
    }
}

if(!function_exists('fa_email_trigger'))
{
    /**
     * Send a trigger e-mail
     * 
     * @param string $name          name of trigger to render
     * @param mixed  $to            email to
     * @param array  $vars          vars for trigger, default array()
     * @param string $subject       the subjec
     * @param string $from_email    email from, default of trigger
     * @param string $from_name     name from, default of trigger
     * @param array  $attachments   attachments, default array()
     * @param array  $headers       custom headers default array()
     * 
     * @return bool
     */
    function fa_email_trigger($name, $to, array $vars = array(),$subject = null, $from_email = null, $from_name = null, $attachments = array(), array $headers = array())
    {    
        $triggers_model = fa_get('triggers_email_model'); /*@var $triggers_model FastAdmin\models\TriggersEmailModel */
        $trigger        = $triggers_model->get_trigger_by_name($name);
        $trigger        = $triggers_model->parse_trigger($trigger, $vars);
                
        return fa_email($to, $trigger['subject'], $trigger['html'], $trigger['text'], $from_email, $from_name, $attachments, $headers);
    }
    
}