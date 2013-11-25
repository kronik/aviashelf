<?php
    require_once 'FormEx.php';

class PersonresultController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_modelName = 'PersonResults';
    protected $_permissions = array();
    protected $_paging = 0;
    protected $_buttons = NULL;

    protected function _initFields()
    {        
//        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
//        $employeesSelect = $employeesModel->select()
//        ->where(new Kwf_Model_Select_Expr_Sql("userId > 0 AND visible = 1"));
//        
//        $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
//        ->setValues($employeesModel)
//        ->setSelect($employeesSelect)
//        ->setWidth(200)
//        ->setShowNoSelection(true)
//        ->setAllowBlank(false);
    }
}
