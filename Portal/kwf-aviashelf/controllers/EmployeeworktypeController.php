<?php
class EmployeeworktypeController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'EmployeeWorkTypes';
    protected $_permissions = array('save', 'add');
    protected $_buttons = array('save');
    protected $_paging = 0;

    protected function _initFields()
    {
        $this->_form->add(new Kwf_Form_Field_TextField('value', 'Название'))
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_NumberField('pos', '№ в списках'))
        ->setWidth(400)
        ->setAllowBlank(true);
        

        
//        $this->_form->add(new Kwf_Form_Field_Select('workTime', 'Норма (чч:мм))'))
//        ->setValues(array('00:00:00' => '00:00',
//                          '06:00:00' => '06:00',
//                          '06:12:00' => '06:12',
//                          '07:00:00' => '07:00',
//                          '07:12:00' => '07:12',
//                          '07:15:00' => '07:15',
//                          '08:00:00' => '08:00'))
//        ->setWidth(400)
//        ->setDefaultValue('00:00:00')
//        ->setAllowBlank(false);
        
        $this->_form->add(new Kwf_Form_Field_TextArea('desc', trlKwf('Description')))
        ->setWidth(400)
        ->setHeight(70);
        
        $this->_form->add(new Kwf_Form_Field_Checkbox('needTime', 'Учитывать время'));
    }
}
