<?php
    require_once 'GridEx.php';

class WorksController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Works';
    protected $_defaultOrder = array('field' => 'month', 'direction' => 'DESC');
    protected $_paging = 400;
    protected $_grouping = array('groupField' => 'year');
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
                                     'controllerUrl' => '/work',
                                     'width' => 550,
                                     'height' => 260,
                                     'type' => 'WindowFormEx'
                                   );

    public function indexAction()
    {
        parent::indexAction();
        
        $this->view->ext('Works');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');

        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'power')
        {
            if ($users->getAuthedUserRole() != 'admin') {
                unset($this->_buttons ['delete']);
            }

            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        }
        else
        {
            $this->_buttons = array();
        }
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column('monthName', 'Месяц', 100));
        $this->_columns->add(new Kwf_Grid_Column('year', 'Год', 100));
        $this->_columns->add(new Kwf_Grid_Column('comment', 'Комментарий', 200));
    }
    
    protected function _beforeDelete(Kwf_Model_Row_Interface $row) {
        $db = Zend_Registry::get('db');
                
        $db->delete('employeeWorks', array('workId = ?' => $row->id));
    }
}
