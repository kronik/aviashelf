<?php
class LinkController extends Kwf_Controller_Action_Auto_Form
{
    protected $_buttons = array('save');
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Links';

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('name', trl('Title')))
        ->setAllowBlank(false)
        ->setWidth(300);
    }
}
