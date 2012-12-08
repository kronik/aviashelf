<?php
class LinkDataEntryController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'LinkData';

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('name', trl('Title')))
            ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextField('value', trl('Value')))
        ->setWidth(300);
        $this->_form->add(new Kwf_Form_Field_TextArea('desc', trl('Description')))
            ->setWidth(300);
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->link_id = $this->_getParam('link_id');
    }
}
