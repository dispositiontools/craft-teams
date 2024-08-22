<?php

namespace dispositiontools\teams\services;
use dispositiontools\teams\Teams;
use Craft;
use yii\base\Component;

/**
 * Teams Variable Service service
 */
class TeamsVariableService extends Component
{


        // {{ craft.teams.myTeamElementIds() }}
        public function myTeamElementIds($optional = null)
        {
            return Teams::$plugin->teamsservice->myTeamElementIds( $optional );
        }
    
        // {{ craft.teams.getAccessByElementId() }}
        public function getAccessByElementId($teamElementId, $options = null)
        {
           return Teams::$plugin->teamsservice->getAccessByElementId( $teamElementId, $options );
        }
    
    
        // {{ craft.teams.teamMembers($elementId) }}
        public function teamMembers($elementId)
        {
            return Teams::$plugin->teamsservice->getTeamMembersByTeamElementId( $elementId );
        }
    
    
        public function myInvites()
        {
    
        }
 
        
}
