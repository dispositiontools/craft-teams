<?php

namespace dispositiontools\teams\jobs;

use Craft;
use craft\queue\BaseJob;

/**
 * Processnewmember queue job
 */
class Processnewmember extends BaseJob
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
