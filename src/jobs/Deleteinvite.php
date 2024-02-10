<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Create teams in CraftCMS
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams\jobs;

use dispositiontools\teams\Teams;

use Craft;
use craft\queue\BaseJob;

/**
 * Calculate job
 *
 * Jobs are run in separate process via a Queue of pending jobs. This allows
 * you to spin lengthy processing off into a separate PHP process that does not
 * block the main process.
 *
 *
 * The key/value pairs that you pass in to the job will set the public properties
 * for that object. Thus whatever you set 'someAttribute' to will cause the
 * public property $someAttribute to be set in the job.
 *
 * Passing in 'description' is optional, and only if you want to override the default
 * description.
 *
 * More info: https://github.com/yiisoft/yii2-queue
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class Deleteinvite extends BaseJob
{
    // Public Properties
    // =========================================================================

    /**
     * Some attribute
     *
     * @var string
     */
    public $teamMemberId = null;
    public $fieldId = null;
    public $elementId = null;


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
        if($this->elementId)
        {
            Teams::$plugin->teams->deleteTeamMembersByTeamElementId( $this->elementId );
        }
        
      
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
        return Craft::t('teams', 'Delete invitations');
    }
}
