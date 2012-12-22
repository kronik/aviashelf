<?php
class PolisController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Polises';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_paging = 0;

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('Number', trlKwf('Number')))
        ->setWidth(400)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_DateField('StartDate', trlKwf('Start Date')))
        ->setWidth(400)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_DateField('EndDate', trlKwf('End Date')))
        ->setWidth(400)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('Ammount', trlKwf('Ammount')))
        ->setWidth(400)
        ->setAllowBlank(false);
        $this->_form->add(new Kwf_Form_Field_TextField('Comment', trlKwf('Comment')))
        ->setWidth(400);
        
        $model = Kwf_Model_Abstract::getInstance('Companies');
        $select = $model->select()->whereEquals('Hidden', '0');
        
        $this->_form->add(new Kwf_Form_Field_Select('CompanyId', trlKwf('Company')))
        ->setValues($model)
        ->setSelect($select)
        ->setWidth(400);
    }
}
