<?php
class MembersController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Members';
    protected $_defaultOrder = 'lastname';
    protected $_paging = 20;
    protected $_buttons = array('add');

    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('lastname', trlKwf('Lastname'), 140));
        $this->_columns->add(new Kwf_Grid_Column('firstname', trlKwf('Firstname')));
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('visible', trlKwf('Active'), 40));
    }
}
