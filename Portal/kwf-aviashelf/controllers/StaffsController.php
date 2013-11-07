<?php
    require_once 'GridEx.php';

class StaffsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Employees';
    protected $_defaultOrder = 'lastname';
    protected $_paging = 100;
    protected $_buttons = array('add', 'delete');
    protected $_grouping = array('groupField' => 'subCompanyName');

    protected $_editDialog = array(
                                   'controllerUrl' => '/staff',
                                   'width' => 550,
                                   'height' => 490
                                   );
   
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Lastname'), 80));
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('Firstname'), 80));
        $this->_columns->add(new Kwf_Grid_Column('middlename', trlKwf('Middlename'), 90));
        $this->_columns->add(new Kwf_Grid_Column('subCompanyName', 'Подразделение', 100));
    }
    
    public function indexAction()
    {
        $this->view->ext('Staffs');
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['groupType = ?'] = 2;
        return $ret;
    }
}
