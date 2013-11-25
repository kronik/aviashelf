<?php
class GroupTopics extends Kwf_Model_Db
{
    protected $_table = 'groupTopics';
    protected $_referenceMap = array(
        'TrainingGroup' => array(
            'column'            => 'groupId',
            'refModelClass'     => 'TrainingGroups',
        ),
        'Training' => array(
            'column'            => 'topicId',
            'refModelClass'     => 'Trainings',
        )
    );
}
