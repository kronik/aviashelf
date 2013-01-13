<?php
class CheckresultsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Checkresults';
    protected $_defaultOrder = array('field' => 'employeeName', 'direction' => 'ASC');
    protected $_grouping = array('groupField' => 'employeeName');
    protected $_paging = 0;
    protected $_buttons = array('xls');

    /*
    protected $_editDialog = array(
        'controllerUrl' => '/checkresult',
        'width' => 550,
        'height' => 300
    );
    */
    
    protected function _initColumns()
    {
        $this->updateChecksResults();

        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Date('checkDate', trlKwf('Date')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('employeeName', trlKwf('Employee')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('typeName', trlKwf('Type')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title')))->setWidth(200);
        $this->_columns->add(new Kwf_Grid_Column('description', trlKwf('Description')))->setWidth(800);
    }
    
    protected function insertNewRow($checkType, $employeeId, $employeeName, $title, $typeId, $typeName, $description)
    {
        $m = Kwf_Model_Abstract::getInstance('Checkresults');

        $row = $m->createRow();
        
        $row->checkType = $checkType;
        $row->checkDate = date('Y-m-d');
        $row->employeeId = $employeeId;
        $row->employeeName = $employeeName;
        $row->title = $title;
        $row->typeId = $typeId;
        $row->typeName = $typeName;
        $row->description = $description;
        
        $row->save();
    }
    
    protected function updateChecksResults()
    {        
        $cheksModel = Kwf_Model_Abstract::getInstance('Checks');
        $cheksSelect = $cheksModel->select();
        
        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
        $employeesSelect = $employeesModel->select()->whereEquals('visible', '1');
        
        $checksrows = $cheksModel->getRows($cheksSelect);
        $employees = $employeesModel->getRows($employeesSelect);
        
        $documentsModel = Kwf_Model_Abstract::getInstance('Documents');
        $documentsSelect = $documentsModel->select();
        
        # Clear previous checks:
        $checkResultsModel = Kwf_Model_Abstract::getInstance('Checkresults');
        $checkResultsModel->deleteRows($checkResultsModel->select()->whereEquals('checkDate', date('Y-m-d')));
        
        foreach ($employees as $employee)
        {
            foreach ($checksrows as $row)
            {
                if ($row->checkType == 'doc')
                {
                    $sqlExpr = '`typeId` = ' . $row->typeId . ' AND `ownerId` = ' . $employee->id;
                    
                    if ($row->field == 'startDate')
                    {
                        if ($row->value == NULL || $row->value == 0)
                        {
                            $sqlExpr = $sqlExpr . ' AND (`' . $row->field . '` < CURDATE())';
                        }
                        else
                        {
                            $sqlExpr = $sqlExpr . ' AND (DATE_ADD(`' . $row->field . '`, INTERVAL ' . $row->value . ' DAY) > CURDATE())';
                        }
                    }
                    else if ($row->field == 'endDate')
                    {
                        if ($row->value == NULL || $row->value == 0)
                        {
                            $sqlExpr = $sqlExpr . ' AND (`' . $row->field . '` > CURDATE())';
                        }
                        else
                        {
                            $sqlExpr = $sqlExpr . ' AND (DATE_ADD(`' . $row->field . '`, INTERVAL ' . $row->value . ' DAY) < CURDATE())';
                        }
                    }
                    
                    $documentsSelect = $documentsModel->select()->where(new Kwf_Model_Select_Expr_Sql($sqlExpr));
                    $documents = $documentsModel->getRows($documentsSelect);
                    
                    if (count($documents) == 0)
                    {
                        $this->insertNewRow('doc', $employee->id, (string)$employee,
                                            $row->title, $row->typeId, $row->typeName,
                                            'Проверка: ' . $row->title . ' - Отсутствует или просрочен документ: ' . $row->typeName);
                    }
                }
                else if ($row->checkType == 'flight')
                {
                    if ($row->field == 'startDate')
                    {
                    }
                }
                else if ($row->checkType == 'training')
                {
                }
            }
        }
    }

    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['checkDate = ?'] = date('Y-m-d');
        return $ret;
    }
}
