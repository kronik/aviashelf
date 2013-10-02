<?php
class StaffsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Employees';
    protected $_defaultOrder = 'lastname';
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');

    protected $_editDialog = array(
                                   'controllerUrl' => '/staff',
                                   'width' => 550,
                                   'height' => 490
                                   );
   
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Lastname'), 80));
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('Firstname'), 80));
        $this->_columns->add(new Kwf_Grid_Column('middlename', trlKwf('Middlename'), 90));
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
