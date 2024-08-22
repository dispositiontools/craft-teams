<?php

namespace dispositiontools\teams\elements\conditions;

use Craft;
use craft\elements\conditions\ElementCondition;

/**
 * Teammember condition
 */
class TeammemberCondition extends ElementCondition
{
    protected function conditionRuleTypes(): array
    {
        return array_merge(parent::conditionRuleTypes(), [
            // ...
        ]);
    }
}
