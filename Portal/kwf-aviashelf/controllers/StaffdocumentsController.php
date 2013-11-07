<?php
    require_once 'GridEx.php';

class StaffdocumentsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'Documents';
    protected $_defaultOrder = array('field' => 'endDate', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('add', 'delete');
    protected $_editDialog = array(
        'controllerUrl' => '/staffdocument',
        'width' => 550,
        'height' => 400
    );

    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(150);
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number')));
        $this->_columns->add(new Kwf_Grid_Column_Date('startDate', trlKwf('Doc Start Date')));
        $this->_columns->add(new Kwf_Grid_Column_Date('endDate', trlKwf('Doc End Date')))->setRenderer('docCheckDate');
        $this->_columns->add(new Kwf_Grid_Column('gradeName', trlKwf('Note')))->setWidth(300)->setRenderer('checkGrade');
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['ownerId = ?'] = $this->_getParam('ownerId');
        return $ret;
    }
}
