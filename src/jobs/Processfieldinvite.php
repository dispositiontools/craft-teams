<?php

namespace dispositiontools\teams\jobs;

use Craft;
use craft\queue\BaseJob;

/**
 * Processfieldinvite queue job
 */
class Processfieldinvite extends BaseJob
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
