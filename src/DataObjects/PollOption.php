<?php

namespace Polls\DataObjects;

use SilverStripe\ORM\DataObject;

class PollOption extends DataObject {
    private static $table_name = 'PollOption';

    private static $db = [
        'Title' => 'Varchar',
        'SortOrder' => 'Int'
    ];

    private static $has_one = [
        'Poll' => Poll::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
    ];

    private static $default_sort = 'SortOrder ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName([
            'PollID',
            'SortOrder',
        ]);

        return $fields;
    }
}
