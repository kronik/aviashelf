<?php
class Planerstates extends Kwf_Model_Db
{
    protected $_table = 'planerStates';
    protected $_referenceMap = array(
                                     'Plane' => array(
                                                      'column'           => 'planeId',
                                                      'refModelClass'     => 'Wstypes',
                                                      ),
                                     'Landpoint' => array(
                                                      'column'           => 'landpointId',
                                                      'refModelClass'     => 'Landpoints',
                                     ),
                                     'Type' => array(
                                                     'column'           => 'typeId',
                                                     'refModelClass'     => 'Linkdata',
                                     )
    );
}
