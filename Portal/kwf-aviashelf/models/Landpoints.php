<?php
class Landpoints extends Kwf_Model_Db
{
    protected $_table = 'landPoints';
    protected $_toStringField = 'description';
    
    protected $_dependentModels = array(
                                        'FlightLandpoints' => 'FlightLandpoints'
                                        );
}
