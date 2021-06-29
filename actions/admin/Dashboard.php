<?php

namespace FastAdmin\actions\admin;

class Dashboard extends AdminActions
{
    public function index()
    {                        
        return $this->render('dashboard', array(
            'title'         => 'Dashboard',
            'subtitle'      => WP_FA_PLUGIN_NAME,
        ));       
    }
}