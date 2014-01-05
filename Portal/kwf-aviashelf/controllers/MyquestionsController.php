<?php
    require_once 'GridEx.php';

class MyquestionsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'TrainingContentQuestions';
    protected $_defaultOrder = array('field' => 'number', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array();

//    public function indexAction()
//    {
//        $this->view->ext('Myquestions');
//    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number')))->setWidth(100);
    }
    
    protected function _getWhere()
    {
//        $users = Kwf_Registry::get('userModel');
//        
//        $employeesModel = Kwf_Model_Abstract::getInstance('Employees');
//        $employeesSelect = $employeesModel->select()->whereEquals('userId', $users->getAuthedUserId());
//        
//        $employee = $employeesModel->getRow($employeesSelect);
//        
//        $resultsModel = Kwf_Model_Abstract::getInstance('TrainingResults');
//        
//        if ($employee != NULL)
//        {
//            $resultsSelect = $resultsModel->select()
//            ->where(new Kwf_Model_Select_Expr_Sql("employeeId = " . $employee->id
//                                                  . " AND currentScore = 0 AND trainingGroupId = " . $this->_getParam('groupId')));
//            
//            $result = $resultsModel->getRow($resultsSelect);
//            
//            if ($result == NULL) {
//                //Kwf_Util_Redirect::redirect('/myresults');
//                throw new Kwf_Exception_Client('Тест окончен.');
//            }
//        }
        
        $ret = parent::_getWhere();

        $ret['resultId = ?'] = $this->_getParam('personResultId');
        $ret['topicId != ?'] = 0;
        
        return $ret;
    }
}
