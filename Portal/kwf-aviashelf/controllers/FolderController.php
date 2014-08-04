<?php
class FolderController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Folders';
    
    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('description', trlKwf('Description')))
        ->setHeight(70)
        ->setWidth(400);
    }    
}
