<?php
class Objectives extends Kwf_Model_Db
{
    protected $_table = 'link_data';
    protected $_toStringField = 'value';
    
    protected $_dependentModels = array(
                                        'FlightObjectives' => 'FlightObjectives'
                                        );
}
