<?php
    require_once 'GridEx.php';

class FlightgroupsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightgroups';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_paging = 10;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan')
        {
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/flightgroup',
                                       'width' => 550,
                                       'height' => 230
                                       );
        }
        else
        {
            $this->_buttons = array();
        }
                
        $this->_columns->add(new Kwf_Grid_Column('positionName', trlKwf('Position')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(300);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['flightId = ?'] = $this->_getParam('flightId');
        $ret['mainCrew = ?'] = TRUE;
        return $ret;
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    protected function _beforeDelete(Kwf_Model_Row_Interface $row)
    {
        $flightGroupsModel = Kwf_Model_Abstract::getInstance('Flightgroups');
        $flightGroupsSelect = $flightGroupsModel->select()->whereEquals('flightId', $row->flightId)->whereEquals('mainCrew', TRUE)->order('id');
        
        $flightMembers = $flightGroupsModel->getRows($flightGroupsSelect);
        
        $flightsModel = Kwf_Model_Abstract::getInstance('Flights');
        $flightsSelect = $flightsModel->select()->whereEquals('id', $row->flightId);
        
        $users = Kwf_Model_Abstract::getInstance('Employees');
        
        $flightRow = $flightsModel->getRow($flightsSelect);
        
        $flightRow->firstPilotName = '';
        $flightRow->secondPilotName = '';
        $flightRow->technicName = '';
        $flightRow->resquerName = '';
        $flightRow->checkPilotName = '';
        
        foreach ($flightMembers as $flightMember) {
            
            if ($flightMember->id == $row->id) {
                continue;
            }
            
            $userSelect = $users->select()->whereEquals('id', $flightMember->employeeId);
            $employee = $users->getRow($userSelect);
            
            if (($this->isContain('КВС', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->firstPilotName = (string)$employee;
                
                if ($employee->userId != NULL)
                {
                    $tasks = Kwf_Model_Abstract::getInstance('Tasks');
                    
                    $taskRow = $tasks->createRow();
                    
                    $taskRow->title = 'Выполнить полет: ' . $flightRow->number;
                    $taskRow->description = 'Выполнить полет: ' . $flightRow->number . ' ' . $flightRow->flightStartDate . ' ' . $flightRow->flightStartTime;
                    $taskRow->startDate = $flightRow->flightStartDate;
                    $taskRow->userId = $employee->userId;
                    $taskRow->status = 0;
                    
                    $taskRow->save();
                }
            }
            else if (($this->isContain('Второй пилот', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->secondPilotName = (string)$employee;
            }
            else if (($this->isContain('Пилот', $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->secondPilotName = (string)$employee;
            }
            else if (($this->isContain(trlKwf('Technic'), $flightMember->positionName)) && ($flightMember->mainCrew == TRUE))
            {
                $flightRow->technicName = (string)$employee;
            }
            else if ($this->isContain('Спасатель', $flightMember->positionName))
            {
                $flightRow->resquerName = (string)$employee;
            }
            else if (($this->isContain('Проверяющий', $flightMember->positionName)) ||
                     ($this->isContain('Инструктор', $flightMember->positionName)))
            {
                $flightRow->checkPilotName = (string)$employee;
            }
        }
        
        $flightRow->save();
    }
}
