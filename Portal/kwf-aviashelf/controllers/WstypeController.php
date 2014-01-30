<?php
class WstypeController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Wstypes';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_paging = 0;

    protected function _initFields()
    {        
        $this->_form->add(new Kwf_Form_Field_TextField('Name', trlKwf('Title')))
        ->setWidth(400)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('NameEn', trlKwf('English name')))
        ->setWidth(400);
        $this->_form->add(new Kwf_Form_Field_TextField('IATA', trlKwf('IATA')))
        ->setWidth(400);
        $this->_form->add(new Kwf_Form_Field_TextField('IKAO', trlKwf('IKAO')))
        ->setWidth(400);
        $this->_form->add(new Kwf_Form_Field_NumberField('Class', trlKwf('Class')))
        ->setWidth(400);
        
        $model = Kwf_Model_Abstract::getInstance('WsCategories');
        $select = $model->select();
        
        $this->_form->add(new Kwf_Form_Field_Select('TypeId', trlKwf('Type')))
        ->setValues($model)
        ->setSelect($select)
        ->setWidth(400);
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->twsId = 0;
        $row->Fixed = 1;
        $row->ZCode = 0;
        $row->ZMCode = 0;
        $row->Hidden = 0;
    }
}
