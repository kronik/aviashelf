<?php
class StaffgroupsController extends FlightgroupsController
{
    protected $_editDialog = array(
        'controllerUrl' => '/staffgroup',
        'width' => 550,
        'height' => 260
    );
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['mainCrew = ?'] = FALSE;
        return $ret;
    }
}
