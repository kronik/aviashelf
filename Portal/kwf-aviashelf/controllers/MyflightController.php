<?php
class MyflightController extends FlightController
{
    protected $_permissions = array('save', 'xls');
    protected $_buttons = array ('save', 'xls');
    
    protected function _initFields()
    {
        parent::_initFields();
    }
}
