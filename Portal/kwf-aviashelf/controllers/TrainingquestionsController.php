<?php
    require_once 'GridEx.php';

class TrainingquestionsController extends Kwf_Controller_Action_Auto_Grid_Ex
{
    protected $_modelName = 'TrainingQuestions';
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'ASC');
    protected $_paging = 100;
    protected $_buttons = array('add', 'delete', 'xls');
    protected $_editDialog = array(
        'controllerUrl' => '/trainingquestion',
        'width' => 800,
        'height' => 560
    );

    public function indexAction()
    {
        parent::indexAction();
        
        $users = Kwf_Registry::get('userModel');
        
        if ($users->getAuthedUserRole() != 'admin') {
            
            unset($this->_buttons ['delete']);
        }

        $this->view->ext('Trainingquestions');
    }
    
    protected function _initColumns()
    {
        parent::_initColumns();
        
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('question', trlKwf('Question')))->setWidth(700);
    }
    
    protected function _getWhere()
    {
        $ret = parent::_getWhere();
        $ret['trainingId = ?'] = $this->_getParam('trainingId');
        return $ret;
    }
}
