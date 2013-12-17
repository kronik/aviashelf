<?php
    require_once 'FormEx.php';

class GrouppersonController extends Kwf_Controller_Action_Auto_Form_Ex
{
    protected $_modelName = 'GroupPersons';
    protected $_permissions = array('add', 'xls', 'save');
    protected $_paging = 0;
    protected $_buttons = array ('xls');

    protected function _initFields()
    {        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()
        ->where(new Kwf_Model_Select_Expr_Sql("userId > 0 AND visible = 1 AND groupType = 1"));
        
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
        $m3 = Kwf_Model_Abstract::getInstance('Employees');
        
        $s = $m1->select()->whereEquals('id', $row->trainingGroupId);
        $prow = $m1->getRow($s);
        
        $row->trainingGroupName = (string)$prow;
        
        if ($prow->isTrial == true) {
            
            if ($this->isContain('Самоподготовка', $row->comment) == false) {
                $row->comment = $row->comment . ' (Самоподготовка)';
            }
        }
                
        $s = $m3->select()->whereEquals('id', $row->employeeId);
        $prow = $m3->getRow($s);
        
        $row->employeeName = (string)$prow;
    }
    
    protected function isContain($what, $where)
    {
        return stripos($where, $what) !== false;
    }
    
    public function jsonDeleteAction()
    {
        // TODO: Add for valid delete operation
        
        
        
//        $row = $this->_form->getRow();
//        
//        if ($row->currentScore != NULL && $row->currentScore > 0) {
//            throw new Kwf_Exception_Client('Нельзя удалить сотрудника, который уже прошел тест.');
//        }
        
        parent::jsonDeleteAction();
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->trainingGroupId = $this->_getParam('groupId');
        
        $resultsModel = Kwf_Model_Abstract::getInstance('GroupPersons');
        $resultsSelect = $resultsModel->select()->whereEquals('trainingGroupId', $row->trainingGroupId)->whereEquals('employeeId', $row->employeeId);

        $prow = $resultsModel->getRow($resultsSelect);
        
        if ($prow != NULL) {
            throw new Kwf_Exception_Client('Этот сотрудник уже включен в группу.');
        }

        $this->updateReferences($row);
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }
    
    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $groupPersonController = new TrainingHelper();

        $groupModel = Kwf_Model_Abstract::getInstance('TrainingGroups');
        $groupSelect = $groupModel->select()->whereEquals('id', $row->trainingGroupId);
        $groupRow = $groupModel->getRow($groupSelect);

        $groupPersonController->createQuestionsSet($groupRow, $row);
    }
    
    protected function _fillTheXlsFile($xls, $firstSheet)
    {
        $row = $this->_form->getRow();
        
        $this->_progressBar = new Zend_ProgressBar(new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum')),
                                                   0, 100);
        $reporter = new Reporter ();
        
        $xls = PHPExcel_IOFactory::load("./templates/training_result_template.xls");
        
        $xls->setActiveSheetIndex(0);
        $firstSheet = $xls->getActiveSheet();
        
        $reporter->exportTrainingResultsToXls($xls, $firstSheet, $row, $this->_progressBar);
        
        $this->_progressBar->finish();
        
        return $xls;
    }
}
