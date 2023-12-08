<?php

namespace Polls\DataObjects;

use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Member;

class PollSubmission extends DataObject{

    private static $has_one = [
        'Poll' => Poll::class,
        'Member' => Member::class,
        'Option' => PollOption::class
    ];

    public function summaryFields()
    {
        return [
            'Option.Title' => _t(__CLASS__ . '.CHOSENOPTION', 'Gewählte Option'),
            'Member.Name' => _t(__CLASS__ . '.SUBMITTEDBY', 'Eingereicht von')
        ];
    }
}
