<?php
class DictionariesController extends Kwf_Controller_Action_Auto_Grid
{
        protected $_modelName = 'Dictionaries';
        protected $_defaultOrder = 'name';
        protected $_paging = 20;
        protected $_buttons = array('add', 'save');
    
        public function indexAction()
        {
            $this->view->ext('Dictionaries');
        }

        protected function _initColumns()
        {
            $this->_filters = array('name' => array('type' => 'TextField'));
            $this->_columns->add(new Kwf_Grid_Column('name', trl('Title'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        }
}