<?php
class TrainingContentQuestions extends Kwf_Model_Db
{
    protected $_table = 'trainingContentQuestions';
    protected $_toStringField = 'number';
    
    protected $_referenceMap = array(
       'PersonResult' => array(
             'column'           => 'resultId',
             'refModelClass'     => 'PersonResults'
       ),
       'Picture' => array(
             'column'           => 'picture_id',
             'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
    
    protected $_dependentModels = array(
                                        'TrainingContentAnswers' => 'TrainingContentAnswers'
                                        );
}
