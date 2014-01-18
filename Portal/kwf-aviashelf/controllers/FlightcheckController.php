<?php
class FlightcheckController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'Flightchecks';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_paging = 0;

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('title', 'Название'))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('times', 'Кол-во раз'))
        ->setWidth(400)
        ->setDefaultValue(1)
        ->setReadOnly(true)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('months', 'В месяцев'))
        ->setWidth(400)
        ->setDefaultValue(1)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextField('description', 'Описание'))
        ->setWidth(400)
        ->setAllowBlank(false);
    }
}
