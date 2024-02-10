<?php
/**
 * Teams plugin for Craft CMS 3.x
 *
 * Create teams on elements
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams\variables;

use dispositiontools\teams\Teams;

use Craft;

/**
 * MicroInsight Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.microInsight }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class TeamsVariable
{
    // Public Methods
    // =========================================================================


    // {{ craft.teams.myTeamElementIds() }}
    public function myTeamElementIds($optional = null)
    {
        return Teams::$plugin->teams->myTeamElementIds( $optional );
    }

    // {{ craft.teams.getAccessByElementId() }}
    public function getAccessByElementId($elementId)
    {

    }


    // {{ craft.teams.teamMembers($elementId) }}
    public function teamMembers($elementId)
    {
        return Teams::$plugin->teams->getTeamMembersByTeamElementId( $elementId );
    }


    public function myInvites()
    {

    }
 
}
