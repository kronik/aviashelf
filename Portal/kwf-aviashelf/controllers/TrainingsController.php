<?php
class TrainingsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Trainings';
    protected $_defaultOrder = array('field' => 'title', 'direction' => 'ASC');
    protected $_paging = 0;
    protected $_buttons = array('add');
    protected $_editDialog = array(
        'controllerUrl' => '/training',
        'width' => 800,
        'height' => 560
    );

    public function indexAction()
    {
        $this->view->ext('Trainings');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        
        $this->_columns->add(new Kwf_Grid_Column_Button('edit'));
        $this->_columns->add(new Kwf_Grid_Column('number', trlKwf('Number')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('title', trlKwf('Title')))->setWidth(100);
        $this->_columns->add(new Kwf_Grid_Column('description', trlKwf('Description')))->setWidth(800);
    }
}
