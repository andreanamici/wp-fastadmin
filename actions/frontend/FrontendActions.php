<?php

namespace FastAdmin\actions\frontend;

use FastAdmin\actions\MainActions;

class FrontendActions extends MainActions
{
    
   public function __construct()
   {
       parent::__construct();
       $this->set_layout('frontend');
   }
    
}