<?php
class ChecksdocsController extends ChecksController
{    
    protected $_modelName = 'Documents';
    protected $_defaultOrder = array('field' => 'ownerName', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'ownerName');
    protected $_paging = 0;
    protected $_buttons = array('xls');
    
    protected function _initColumns()
    {
        $this->_filters = array('ownerName' => array('type' => 'TextField'), 'endDate' => array('type' => 'DateRange'));
        $this->_queryFields = array('ownerName', 'typeName', 'number', 'gradeName', 'comment');
        
        $users = Kwf_Registry::get('userModel');

        if ($users->getAuthedUserRole() == 'user' || $users->getAuthedUserRole() == 'kws') {
            $this->_grouping = array('groupField' => 'typeName');
        }
        
        $this->_columns->add(new Kwf_Grid_Column('ownerName', 'ФИО'))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('typeName', 'Тип проверки'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('number', 'Номер документа'))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column_Date('startDate', trlKwf('Doc Start Date')));
        $this->_columns->add(new Kwf_Grid_Column_Date('endDate', trlKwf('Doc End Date')))->setRenderer('documentsCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('gradeName', 'Оценка'))->setWidth(100)->setRenderer('documentsCheckGrade');
        $this->_columns->add(new Kwf_Grid_Column('comment', trlKwf('Comment')))->setWidth(800);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'user' || $users->getAuthedUserRole() == 'kws') {
            
            $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
            $employeesSelect = $employeesModel->select()->whereEquals('userId', $users->getAuthedUserId());
            
            $employee = $employeesModel->getRow($employeesSelect);
            $employeeId = -1;
            
            if ($employee != NULL) {
                $employeeId = $employee->id;
            }
            
            $ret['ownerId = ?'] = $employeeId;
        }
        
        $ret['ownerName <> ?'] = 'NULL';
        $ret['isDocument = ?'] = '0';
        return $ret;
    }

}
