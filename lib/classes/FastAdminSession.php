<?php

namespace FastAdmin\lib\classes;

class FastAdminSession extends FastAdminCore
{
    const FLASH_KEY_NAME = '_fa_flash_data';
    
    public function wp_init()
    {
        $this->_session_start();

        add_action('wp_logout', array($this,'_session_destroy'),1);
        
        // add_action('wp_login', array($this,'_session_destroy'),1);
    }
        
    
    /**
     * Set a value to session
     * 
     * @param string $key       key
     * @param string $value     value
     * 
     * @return $this
     */
    public function set($key, $value)
    {
        $_SESSION[$key] = $value;
        return $this;
    }
    
    /**
     * Retrive a value from session
     * 
     * @param string $key       key
     * @param mixed  $default   default value, default false
     * 
     * @return mixed
     */
    public function get($key, $default = false)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    /**
     * Remove a value from session
     * 
     * @param string $key       key
     * 
     * @return bool
     */
    public function remove($key)
    {
        unset($_SESSION[$key]);
        return true;
    }

    /**
     * Retrive all session information
     */
    public function all()
    {
        return $_SESSION;
    }
    
    /**
     * Return flash value and unset from session after fetch
     * 
     * @param string $key       key to fetch
     * @param mixed  $default   default value, default false
     * 
     * @return mixed
     */
    public function flash_get($key, $default = false)
    {
        $res = isset($_SESSION[self::FLASH_KEY_NAME][$key]) ? $_SESSION[self::FLASH_KEY_NAME][$key] : $default;
        unset($_SESSION[self::FLASH_KEY_NAME][$key]);
        
        return $res;
    }
    
    /**
     * Set flash value
     * 
     * @param string $key       key
     * @param mixed  $value     value
     * 
     * @return mixed
     */
    public function flash_set($key,$value)
    {
       $_SESSION[self::FLASH_KEY_NAME][$key] = $value;
       return $this;
    }

    /**
     * Check if exists flash value
     * 
     * @param string $key       key to fetch
     * 
     * @return mixed
     */
    public function flash_has($key)
    {
        return isset($_SESSION[self::FLASH_KEY_NAME][$key]);
    }
    
    /**
     * Start a session data for plugin
     */
    public function _session_start()
    {
        @session_name(WP_FA_SESSION_NAME);
        @session_set_cookie_params(0, '/', $_SERVER['SERVER_NAME']);
        
        if(!session_id()) {
           @session_start();
        }

        return $this;
    }
    
    /**
     * Destroy session registered
     */
    public function _session_destroy()
    {
        @session_destroy();
        @setcookie(WP_FA_SESSION_NAME, '', time(), '/');
        return $this;
    }
}