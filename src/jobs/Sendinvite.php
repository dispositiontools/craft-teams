<?php

namespace dispositiontools\teams\jobs;
use dispositiontools\teams\Teams;
use Craft;
use craft\queue\BaseJob;

/**
 * Sendinvite queue job
 */
class Sendinvite extends BaseJob
{
    public $teamMemberId = null;

    function execute($queue): void
    {
        // ...
        Teams::$plugin->teamsservice->sendMemberInvite($this->teamMemberId);
    }

    protected function defaultDescription(): ?string
    {
        return null;
    }
}
