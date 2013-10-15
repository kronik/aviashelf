<?php
class FlightfileController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightfiles';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('title', 'Наименование'))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('comment', 'Примечание'))
        ->setHeight(70)
        ->setWidth(400);
        
        $this->_form->add(new Kwf_Form_Field_File('File', trlKwf('File')))
        ->setShowPreview(true)
        ->setAllowOnlyImages(false);        
    }

    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->flightId = $this->_getParam('flightId');
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
    }
}
