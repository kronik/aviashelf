<?php
class FlightcrewfilterController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Employees';

    protected function _initColumns()
    {            
        $this->_columns[] = new Kwf_Grid_Column('id');
        $this->_columns[] = new Kwf_Grid_Column('name');
    }
    
    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        
        if ($this->_getParam('flightId'))
        {
            $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
            $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $this->_getParam('flightId'))->whereEquals('mainCrew', TRUE);
            
            $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);

            $memberIds = array();
            
            foreach ($flightMembers as $flightMember) {
                array_push($memberIds, $flightMember->employeeId);
            }
            
            $ret->whereEquals('groupType', '1')->whereEquals('isOOO', false)->whereEquals('isAllowed', '1')
            ->where('id IN (?)', $memberIds);
        }
        
        return $ret;
    }
}
