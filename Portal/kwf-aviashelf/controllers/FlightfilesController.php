<?php
require_once 'GridEx.php';
class FlightfilesController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Flightfiles';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = NULL;

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'admin' ||
            $users->getAuthedUserRole() == 'power' || $users->getAuthedUserRole() == 'kws') {
            
            $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
            
            $this->_editDialog = array(
                                       'controllerUrl' => '/flightfile',
                                       'width' => 550,
                                       'height' => 270
                                       );
        } else {
            $this->_buttons = array();
        }

        $this->_columns->add(new Kwf_Grid_Column('title', 'Наименование'));
        $this->_columns->add(new Kwf_Grid_Column('comment', 'Примечание'))->setWidth(500);
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['flightId = ?'] = $this->_getParam('flightId');
        return $ret;
    }
}
