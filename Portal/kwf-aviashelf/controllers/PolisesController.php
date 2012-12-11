<?php
class PolisesController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_modelName = 'Polises';
    protected $_defaultOrder = 'id';
    protected $_paging = 30;
    protected $_buttons = array('add');
    protected $_editDialog = array(
                                   'controllerUrl' => '/polis',
                                   'width' => 450,
                                   'height' => 300
                                   );

    public function indexAction()
    {
        $this->view->ext('Polises');
    }
    
    protected function _initColumns()
    {
        $this->_filters = array('text' => array('type' => 'TextField'));

        $this->_columns->add(new Kwf_Grid_Column('Number', trlKwf('Number'), 195));
        $this->_columns->add(new Kwf_Grid_Column_Date('StartDate', trlKwf('Start Date'), 100));
        $this->_columns->add(new Kwf_Grid_Column_Date('EndDate', trlKwf('End Date'), 100));
    }
}
