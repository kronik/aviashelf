<?php
class CompaniesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Companies';
    protected $_defaultOrder = 'Name';
    protected $_paging = 30;
    protected $_buttons = array('add');
    protected $_editDialog = array(
                                   'controllerUrl' => '/company',
                                   'width' => 450,
                                   'height' => 350
                                   );

    public function indexAction()
    {
        $this->view->ext('Companies');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));
        $this->_columns->add(new Kwf_Grid_Column('Name', trlKwf('Title'), 400));
    }
}
