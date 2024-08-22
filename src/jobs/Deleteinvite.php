<?php

namespace dispositiontools\teams\jobs;

use Craft;
use craft\queue\BaseJob;

/**
 * Deleteinvite queue job
 */
class Deleteinvite extends BaseJob
{
    function execute($queue): void
    {
        // ...
    }

    protected function defaultDescription(): ?string
    {
        return null;
    }
}
