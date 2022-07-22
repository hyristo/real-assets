<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Module
 *
 * @author Anselmo
 */
class Module {
    public $ID = null;
    public $TEXT = "";
    public $URL = null;
    public $Modules = array();

    /**
     * Module constructor
     *
     * @param 
     */
    function __construct($text, $url = null, $id = null)
    {
            $this->ID = $id;
            $this->TEXT = $text;
            $this->URL = $url;
    }
}
