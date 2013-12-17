<?php
class TrainingtrialgroupController extends Kwf_Controller_Action_Auto_Form
{
    protected $_modelName = 'TrainingGroups';
    protected $_permissions = array('save', 'add');
    protected $_paging = 0;

    protected function _initFields()
    {
        $tabs = $this->_form->add(new Kwf_Form_Container_Tabs());
        $tabs->setBorder(true);
        $tabs->setActiveTab(0);
        
        // **** General Info
        $tab = $tabs->add();
        $tab->setTitle(trlKwf('General Info'));
        
        $tab->fields->add(new Kwf_Form_Field_TextField('number', 'Номер'))
        ->setWidth(500)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_TextField('title', trlKwf('Title')))
        ->setWidth(500)
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_DateField('startDate', trlKwf('Start Date')))
        ->setAllowBlank(false);
        
        $tab->fields->add(new Kwf_Form_Field_DateField('endDate', trlKwf('End Date')))
        ->setAllowBlank(false);
        
//        $tab->fields->add(new Kwf_Form_Field_NumberField('questions', trlKwf('Questions in session')))
//        ->setWidth(500)
//        ->setAllowBlank(true);
        
        $tab->fields->add(new Kwf_Form_Field_Checkbox('isDifGrade', trlKwf('Grade')));
        
        $tab = $tabs->add();
        $tab->setTitle('Дисциплины');
        
        $objectivesModel = Kwf_Model_Abstract::getInstance('Trainings');
        $objectivesSelect = $objectivesModel->select()->order('title');
        
        $multifields = new Kwf_Form_Field_MultiFields('GroupTopics');
        $multifields->setMinEntries(0);
        $multifields->fields->add(new Kwf_Form_Field_Select('topicId', 'Дисциплина'))
        ->setValues($objectivesModel)
        ->setSelect($objectivesSelect)
        ->setWidth(400)
        ->setAllowBlank(false);
        
        $multifields->fields->add(new Kwf_Form_Field_NumberField('questions', 'Вопросов'))
        ->setWidth(150)
        ->setAllowBlank(false);

        $tab->fields->add($multifields);
    }
    
    protected function updateReferences(Kwf_Model_Row_Interface $row) {
        
        if ($row->questions == NULL) {
            $row->questions = 0;
        }
        
        $row->trainingId = 0;
        $row->isTrial = true;
    }
    
    protected function _beforeInsert(Kwf_Model_Row_Interface $row)
    {
        $row->trainingId = $this->_getParam('trainingId');

        $this->updateReferences($row);
    }
    
    protected function _afterInsert(Kwf_Model_Row_Interface $row)
    {
        $groupPersonController = new TrainingHelper();
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()
        ->where(new Kwf_Model_Select_Expr_Sql("userId > 0 AND visible = 1 AND groupType = 1"));
        
        $employees = $employeesModel->getRows($employeesSelect);
        
        $groupPersonModel = Kwf_Model_Abstract::getInstance('GroupPersons');
        
        foreach ($employees as $employee) {
            $groupPersonRow = $groupPersonModel->createRow();
            
            $groupPersonRow->employeeId = $employee->id;
            $groupPersonRow->employeeName = (string)$employee;
            
            $groupPersonRow->trainingGroupId = $row->id;
            $groupPersonRow->trainingGroupName = (string)$row;
            
            if ($row->isTrial == true) {
                $groupPersonRow->comment = ' (Самоподготовка)';
            }
            
            $groupPersonRow->save();
            
            $groupPersonController->createQuestionsSet($row, $groupPersonRow);
        }
    }
    
    protected function _beforeSave(Kwf_Model_Row_Interface $row)
    {
        $this->updateReferences($row);
    }

}
