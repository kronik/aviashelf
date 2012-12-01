<?php
    class DictionaryController extends Kwf_Controller_Action_Auto_Grid
    {
        protected $_modelName = 'Dictionary';
        protected $_defaultOrder = 'value';
        protected $_paging = 0;
        protected $_buttons = array('add', 'delete');
        protected $_editDialog = array(
                                       'controllerUrl' => '/DictionaryEntry',
                                       'width' => 450,
                                       'height' => 200
                                       );
        
        protected function _initColumns()
        {
            $this->_columns->add(new Kwf_Grid_Column_Date('value', trl('Title')));
            $this->_columns->add(new Kwf_Grid_Column('desc', trl('Description')));
        }
        
        protected function _getWhere()
        {
            $ret = parent::_getWhere();
            $ret['name = ?'] = $this->_getParam('name');
            return $ret;
        }
    }