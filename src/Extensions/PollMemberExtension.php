<?php

namespace Polls\Extensions;

use Polls\DataObjects\PollSubmission;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;

class PollMemberExtension extends DataExtension{
    private static $has_many = [
        'PollSubmissions' => PollSubmission::class
    ];

    public function updateCMSFields(FieldList $fields)
    {
        parent::updateCMSFields($fields);

        $fields->removeByName([
            'PollSubmissions'
        ]);
    }
}
