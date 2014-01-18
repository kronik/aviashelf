<?php
    
require_once 'FormEx.php';
  
//class FlightplanController extends Kwf_Controller_Action_Auto_Form

class FlightplanController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_permissions = array('save', 'add', 'xls');
    protected $_modelName = 'Flightplans';
    protected $_buttons = array ('xls');
    
    public function indexAction()
    {
        parent::indexAction();
        $this->view->xtype = 'kwf.autoformex';
    }

    protected function _initFields()
    {
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() == 'admin' || $users->getAuthedUserRole() == 'plan' || $users->getAuthedUserRole() == 'power')
        {
            $this->_form->add(new Kwf_Form_Field_DateField('planDate', trlKwf('Date')))->setAllowBlank(false);

            $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
            $employeesSelect = $employeesModel->select()->where(new Kwf_Model_Select_Expr_Sql('visible = 1 AND groupType = 2'))->order('listPosition');
            
            $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Responsible')))
            ->setValues($employeesModel)
            ->setSelect($employeesSelect)
            ->setWidth(400)
            ->setShowNoSelection(true)
            ->setAllowBlank(true);
            
            $this->_form->add(new Kwf_Form_Field_TextArea('comment', trlKwf('Additional info')))
            ->setHeight(70)
            ->setWidth(400);            
        }
        else
        {
            $this->_form->add(new Kwf_Form_Field_ShowField('planDate', trlKwf('Date')))
            ->setWidth(400);

            $this->_form->add(new Kwf_Form_Field_ShowField('employeeName', trlKwf('Responsible')))
            ->setWidth(400);

            $this->_form->add(new Kwf_Form_Field_ShowField('comment', trlKwf('Additional info')))
            ->setHeight(70)
            ->setWidth(400);            
        }
    }
        
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {        
        if ($row->employeeId != NULL)
        {
            $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
            $employeesSelect = $employeesModel->select()->whereEquals('id', $row->employeeId);
            
            $prow = $employeesModel->getRow($employeesSelect);
            $row->employeeName = (string)$prow;
        }

        return $row;
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _beforeDelete(Kwf_Model_Row_Interface $row) {
        $db = Zend_Registry::get('db');
        
        $db->delete('planerStates', array('planId = ?' => $row->id));
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();
        
        $this->_progressBar = new Zend_ProgressBar(new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                                                   0, 100);
        $reporter = new Reporter ();
        $reporter->exportFlightPlanToXls($xls, $firstSheet, $row, $this->_progressBar);
        
        $this->_progressBar->finish();
    }
}
