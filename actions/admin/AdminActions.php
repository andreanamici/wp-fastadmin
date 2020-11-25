<?php

namespace FastAdmin\actions\admin;

use FastAdmin\actions\MainActions;

class AdminActions extends MainActions
{
    public function __construct()
    {
        parent::__construct();
        $this->set_layout('admin');
    }
}