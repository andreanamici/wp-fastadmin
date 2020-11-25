<?php

namespace FastAdmin\actions\admin;

use FastAdmin\lib\classes\FastAdminActions;

class Dashboard extends AdminActions
{
    public function index()
    {
        $mainpages = $this->pages_main;
                
        $stats = $this->stats_model->get_dashboard_stats();
        
        return $this->render('dashboard', array(
            'title'         => 'Dashboard',
            'subtitle'      => 'Pannello di controllo',
            'stats'         => $stats,
            'mainpages'     => $mainpages
        ));       
    }
}