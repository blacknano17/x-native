<?php

#[\AllowDynamicProperties]
class mainController extends mainApi
{
    public $core;
    public $view;
    public $dbc;
    public $dbc2;

    function __construct()
    {
        parent::__construct();
        $this->view = new View;
    }
}
