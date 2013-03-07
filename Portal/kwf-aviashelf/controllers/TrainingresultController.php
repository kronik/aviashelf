<?php
class TrainingresultController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingResults';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('visible', '1');
        
        $this->_form->add(new Kwf_Form_Field_Select('employeeId', trlKwf('Employee')))
        ->setValues($employeesModel)
        ->setSelect($employeesSelect)
        ->setWidth(200)
        ->setShowNoSelection(true)
        ->setAllowBlank(false);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row)
    {
        $m1 = Kwf_Model_Abstract::getInstance('TrainingGroups');
        $m2 = Kwf_Model_Abstract::getInstance('Trainings');
        $m3 = Kwf_Model_Abstract::getInstance('Employees');
        
        $row->trainingGroupId = $this->_getParam('groupId');
        
        $s = $m1->select()->whereEquals('id', $row->trainingGroupId);
        $prow = $m1->getRow($s);
        
        $row->trainingGroupName = (string)$prow;
        
        $row->trainingId = $prow->trainingId;
        
        $s = $m2->select()->whereEquals('id', $row->trainingId);
        $prow = $m2->getRow($s);
        
        $row->trainingName = (string)$prow;
        
        $s = $m3->select()->whereEquals('id', $row->employeeId);
        $prow = $m3->getRow($s);
        
        $row->employeeName = (string)$prow;
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
}
