<?php
class TrainingQuestions extends Kwf_Model_Db
{
    protected $_table = 'trainingQuestions';
    protected $_toStringField = 'question';
    
    protected $_referenceMap = array(
        'Training' => array(
            'column'           => 'trainingId',
            'refModelClass'     => 'Trainings'
        ),
       'Picture' => array(
            'column'           => 'picture_id',
            'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
    
    protected $_dependentModels = array(
                                        'TrainingAnswers' => 'TrainingAnswers'
                                        );
}
