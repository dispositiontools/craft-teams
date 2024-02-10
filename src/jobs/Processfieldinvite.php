<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Create teams
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2024 Disposition Tools
 */

namespace dispositiontools\teams\jobs;

use dispositiontools\teams\Teams;

use Craft;
use craft\queue\BaseJob;

/**
 * Send job
 *
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class Processfieldinvite extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * elementId
     *
     * @var int
     */
    public $elementId = null;


    public $siteId = null;

    public $elementType = null;
    /**
     * fieldHandle
     *
     * @var string
     */
    public $fieldHandle = null;

    /**
     * cpEditUrl
     *
     * @var string
     */
    public $cpEditUrl = null;

    /**
     * elementTitle
     *
     * @var string
     */
    public $elementTitle = null;

    // Public Methods
    // =========================================================================

    /**
     * When the Queue is ready to run your job, it will call this method.
     * You don't need any steps or any other special logic handling, just do the
     * jobs that needs to be done here.
     *
     * More info: https://github.com/yiisoft/yii2-queue
     */
    public function execute($queue)
    {
        // Do work here
        //$elementId, $elementType, $siteId, $fieldHandle
        Teams::$plugin->teams->processElementTeamInvites( $this->elementId, $this->elementType, $this->siteId, $this->fieldHandle );

    }

    // Protected Methods
    // =========================================================================

    /**
     * Returns a default description for [[getDescription()]], if [[description]] isn’t set.
     *
     * @return string The default task description
     */
    protected function defaultDescription(): string
    {
        return Craft::t('teams', 'Process field invites for element');
    }
}
