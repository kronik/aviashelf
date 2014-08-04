<?php
class BookController extends Kwf_Controller_Action_Auto_Form
{
    protected $_permissions = array('save', 'add');
    protected $_modelName = 'Books';
    
    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('description', trlKwf('Description')))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_File('File', trlKwf('File')))
        ->setShowPreview(true)
        ->setWidth(400)
        ->setAllowOnlyImages(false);
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->folderId = $this->_getParam('folderId');
    }
}
