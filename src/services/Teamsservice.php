<?php
/**
 * MicroInsight plugin for Craft CMS 3.x
 *
 * Create teams on elements
 *
 * @link      https://www.disposition.tools
 * @copyright Copyright (c) 2020 Disposition Tools
 */

namespace dispositiontools\teams\services;




use Craft;
use craft\base\Component;


use dispositiontools\teams\Teams;
use dispositiontools\teams\elements\Team as TeamElement;


use dispositiontools\teams\elements\Teammember as TeammemberElement;
use dispositiontools\teams\jobs\Sendinvite as SendinviteJob;

use craft\mail\Message;

/**
 * Quiz Service
 *
 * All of your plugin’s business logic should go in services, including saving data,
 * retrieving data, etc. They provide APIs that your controllers, template variables,
 * and other plugins can interact with.
 *
 * https://craftcms.com/docs/plugins/services
 *
 * @author    Disposition Tools
 * @package   Teams
 * @since     1.0.0
 */
class Teamsservice extends Component
{
    // Public Methods
    // =========================================================================

    /**
     * This function can literally be anything you want, and you can have as many service
     * functions as you want
     *
     * From any other plugin file, call it like this:
     *
     *     Teams::$plugin->teams->exampleService()
     *
     * @return mixed
     */
    public function exampleService()
    {
        $result = 'something';
        // Check our Plugin's settings for `someAttribute`
        if (Teams::$plugin->getSettings()->someAttribute) {
        }

        return $result;
    }


     // Teams::$plugin->teams->createTeammemberElement();
    public function createTeammemberElement()
    {
        $TeammemberElement = new TeammemberElement();
        $TeammemberElement->siteId = 2;
        $TeammemberElement->teamElementId = 781240;
        $TeammemberElement->status = "thing";
        $TeammemberElement->setScenario(TeammemberElement::SCENARIO_TEAMMEMBER);

        $success = Craft::$app->elements->saveElement( $TeammemberElement );
        
        if (!$success) {
            Craft::error('Couldn’t save the entry "'.$TeammemberElement->title.'"', __METHOD__);
        }

    }


    // Teams::$plugin->teams->getTeammemberById($teammemberId);
    public function getTeammemberById($teammemberId)
    {
      $query = TeammemberElement::find()->id($teammemberId)->siteId('*');
      return $query->one();
    }



    // Teams::$plugin->teams->getTeamById($teamId);
    public function getTeamById($teamId)
    {
      $query = TeamElement::find()->id($teamId)->siteId('*');
      return $query->one();
    }

    // Teams::$plugin->teams->getTeamMembersByTeamElementId( $teamElementId );
    public function getTeamMembersByTeamElementId($teamElementId)
    {
        $teamMembersQuery = TeammemberElement::find()->teamElementId($teamElementId) ;
        $teamMembers = $teamMembersQuery->all();
       return $teamMembers;
    }

    // Teams::$plugin->teams->deleteTeamMembersByTeamElementId( $teamElementId );
    public function deleteTeamMembersByTeamElementId($teamElementId)
    {
            $teamMembers = $this->getTeamMembersByTeamElementId($teamElementId);
        
            foreach($teamMembers as $TeammemberElement)
            {
                $success = Craft::$app->elements->deleteElement( $TeammemberElement, true );
            }
    }

    // Teams::$plugin->teams->sendMemberInvite($teamMemberId);
    public function sendMemberInvite($teamMemberId)
    {
        $query = TeammemberElement::find()->id($teamMemberId)->siteId('*');

        $TeammemberElement = $query->one();

        if(! $TeammemberElement)
        {
            return false;
        }

        $teamField = Craft::$app->fields->getFieldById( $TeammemberElement->fieldId );
        if(!$teamField)
        {
            return false;
            
        }   

        $element = Craft::$app->elements->getElementById($TeammemberElement->teamElementId, $TeammemberElement->elementType,  $TeammemberElement->siteId);
    
        // Which email shall we send? A user, or non user?

        if( $TeammemberElement->userId )
        {
            $messageSubject = $teamField->memberEmailSubject;
            $message =  nl2br($teamField->memberEmailPlainText);
            
            $teamMember = Craft::$app->users->getUserByUsernameOrEmail( $TeammemberElement->userId );
            
        }
        else{
            $messageSubject = $teamField->nonMemberEmailSubject;
            $message =  nl2br($teamField->nonMemberEmailPlainText);
            $teamMember = false;
        }

        $variables = [
            'field' => $teamField,
            'invite' => $TeammemberElement, 
            'user' => $teamMember,
            'element' => $element
        ];
        $view = \Craft::$app->getView(); 
        $emailTemplate = false;
        if($emailTemplate)
        {
            $subject            = $view->renderString($messageSubject, $variables, $view::TEMPLATE_MODE_SITE);
             $html               = $view->renderString($message, $variables , $view::TEMPLATE_MODE_SITE);
        }
        else{
            $subject            = $view->renderString($messageSubject, $variables, $view::TEMPLATE_MODE_SITE);
            $html               = $view->renderString($message, $variables , $view::TEMPLATE_MODE_SITE);
        }

       $sendEmailSuccess = $this->sendMail($html, $subject, $TeammemberElement->emailAddress, []);

       $TeammemberElement->dateInviteSent = date("Y-m-d H:i:s");
       $TeammemberElement->teamMemberStatus = "Invite sent";

       $success = Craft::$app->elements->saveElement( $TeammemberElement );
    }


    // Teams::$plugin->teams->processNewUser( $userId );
    public function processNewUser($userId)
    {
    
        $connectedUser = Craft::$app->users->getUserById($userId);
        if(!$connectedUser)
        {
            return false;
        }
        // find any invites open for this user
 
        $query = TeammemberElement::find()->emailAddress($connectedUser->email)->autoJoin(1)->teamMemberStatus("invited")->siteId('*');
        $teammemberElements = $query->all();
        // then loop through them and get them in there!
        foreach($teammemberElements as $TeammemberElement)
        {
                $TeammemberElement->userId = $connectedUser->id;
                $TeammemberElement->autoJoined = 1;
                $TeammemberElement->dateJoined = date("Y-m-d H:i:s");
                $TeammemberElement->teamMemberStatus = "active";

                    // check if they are in the right group?

                // get groups
                $currentUserGroups = $connectedUser->getGroups();
                $newGroupIds = json_decode($TeammemberElement->userGroups, true);
             
                foreach($currentUserGroups as $currentUserGroup)
                {
                    if(!in_array($currentUserGroup->id,$newGroupIds ))
                    {
                        $newGroupIds[]=$currentUserGroup->id;
                    }
                }
                // now assign those groups to the user;
                Craft::$app->users->assignUserToGroups($connectedUser->id, $newGroupIds);
          
                $TeammemberElement->setScenario(TeammemberElement::SCENARIO_TEAMMEMBER);
                $success = Craft::$app->elements->saveElement( $TeammemberElement );
                
        }

        return true;


    }

    // Teams::$plugin->teams->myTeamElementIds( $options );
    public function myTeamElementIds($options = null)
    {

        
        $userId = false;
        $fieldId = false;
        $teamMemberStatus = "active";
        if($options)
        {
            if( array_key_exists( 'userId', $options ) )
            {
                $userId = $options['userId'];
            }
            if( array_key_exists('fieldId', $options ) )
            {
                $fieldId = $options['fieldId'];
            }
            if( array_key_exists('teamMemberStatus', $options ) )
            {
                $teamMemberStatus = $options['teamMemberStatus'];
            }
        }

        
        if($userId)
        {
            $user = Craft::$app->users->getUserById($userId);
            $userId = $user->id;
        }
        else
        {
            $user = Craft::$app->getUser()->getIdentity();
            
            $userId = $user->id;
        }
        if(!$user)
        {
            return false;
        }

        $query = TeammemberElement::find()->siteId('*')->userId($userId)->teamMemberStatus($teamMemberStatus);
        if($fieldId)
        {
            $query->fieldId( $fieldId );
        }
        $teammemberElements = $query->all();
        
        $elementIds = [];
        foreach($teammemberElements as $teammemberElement)
        {
            if( !in_array($teammemberElement->teamElementId, $elementIds) )
            {
                $elementIds[] = $teammemberElement->teamElementId;
            }
        }

        return $elementIds;
    }


    // Teams::$plugin->teams->processElementTeamInvites( $elementId, $elementType, $siteId, $fieldHandle );
    public function processElementTeamInvites($elementId = null, $elementType = null,  $siteId = null, $fieldHandle = null)
    {
        
        if(!$elementId){
            return false;
        }

        if(!$fieldHandle){
            return false;
        }

        if(!$elementType){
            return false;
        }

        if(!$siteId){
            $siteId = '*';
        }

        $element = Craft::$app->elements->getElementById($elementId, $elementType,  $siteId);
        if (!$element)
        {
            //ray("no element");
            return false;
        }

        $teamMembersRaw = $element->getFieldValue($fieldHandle);
        //ray($teamMembersRaw);
        if(!$teamMembersRaw)
        {
            return false;
        }
       

        // get all current team members to check if they have been made before
        $currentTeamMembers = $this->getTeamMembersByTeamElementId($elementId);

        $currentTeamEmailsAddresses = [];
        foreach($currentTeamMembers as $currentTeamMember)
        {
            if(isset($currentTeamMember->emailAddress))
            {
                $currentTeamEmailsAddresses[] = $currentTeamMember->emailAddress;
            }
            
        }

        // get field data
        $teamField = Craft::$app->fields->getFieldByHandle($fieldHandle);

        $queue = Craft::$app->getQueue();

        $resaveEntry = false;

        // loop through new table data
        foreach( $teamMembersRaw as $key => $teamMembersRow ){
            
            // create new team members
            if( isset($teamMembersRow['emailAddress']) && !in_array( $teamMembersRow['emailAddress'], $currentTeamEmailsAddresses ) && isset($teamMembersRow['inviteStatus']) && $teamMembersRow['inviteStatus'] != "nothing" )
            {
   
                $TeammemberElement = new TeammemberElement();
                $TeammemberElement->siteId = $element->siteId;
                $TeammemberElement->teamElementId = $element->id;
                $TeammemberElement->fieldId = $teamField->id;
                $TeammemberElement->elementType = get_class($element);
                
                $TeammemberElement->emailAddress = $teamMembersRow['emailAddress'];
                $TeammemberElement->firstName = $teamMembersRow['firstName'];
                $TeammemberElement->lastName = $teamMembersRow['lastName'];
                $TeammemberElement->notes = $teamMembersRow['note'];
                $TeammemberElement->autoJoin = $teamMembersRow['autoJoin'];
                $TeammemberElement->isAdmin = $teamMembersRow['isAdmin'];
                $TeammemberElement->isMember = $teamMembersRow['isMember'];
                
                $TeammemberElement->userGroups = json_encode($teamField['userGroups']);

                $TeammemberElement->teamMemberStatus = "invited";

                $currentUser = Craft::$app->getUser()->getIdentity();

                if($currentUser)
                {
                    $TeammemberElement->invitedByUserId = $currentUser->id;
                }
                
                // find out if this email address is already a user.
                $connectedUser = Craft::$app->users->getUserByUsernameOrEmail( $teamMembersRow['emailAddress'] );


                if($connectedUser)
                {
                     // if so then connect them


                    $TeammemberElement->userId = $connectedUser->id;

                    if($TeammemberElement->autoJoin)
                    {
                        $TeammemberElement->autoJoined = 1;
                        $TeammemberElement->dateJoined = date("Y-m-d H:i:s");
                        $TeammemberElement->teamMemberStatus = "active";

                         // check if they are in the right group?

                        // get groups
                        $currentUserGroups = $connectedUser->getGroups();
                        $newGroupIds = json_decode($TeammemberElement->userGroups, true);
                        foreach($currentUserGroups as $currentUserGroup)
                        {
                            if(!in_array($currentUserGroup->id,$newGroupIds ))
                            {
                                $newGroupIds[]=$currentUserGroup->id;
                            }
                        }

                        // now assign those groups to the user;
                        Craft::$app->users->assignUserToGroups($connectedUser->id, $newGroupIds);
                    }
                    

                   



                }
                else
                {
                        // if not then create an invite and save all the details


                }
               
                $TeammemberElement->setScenario(TeammemberElement::SCENARIO_TEAMMEMBER);

                $success = Craft::$app->elements->saveElement( $TeammemberElement );
                
                if (!$success) {
                    Craft::error('Couldn’t save the team member invite "'.$TeammemberElement->title.'"', __METHOD__);
                }
                else
                {
                    $resaveEntry = true;
                }

                 // send invites if needed
                if($teamMembersRow['inviteStatus'] == "send" ){
                    $sendInviteDescription = "Send team member invitation for ".$teamMembersRow['emailAddress']. " - team member id: ". $TeammemberElement->id;
                    $jobId = $queue->push(new SendinviteJob([
                        'description' => $sendInviteDescription ,
                        'teamMemberId' => $TeammemberElement->id,
                    ]));

                }

                unset($teamMembersRaw[$key]);

            }
            elseif( isset($teamMembersRow['emailAddress']) && in_array( $teamMembersRow['emailAddress'], $currentTeamEmailsAddresses ) )
            {
                unset($teamMembersRaw[$key]);
                $resaveEntry = true;
            }
        }

        if($resaveEntry)
        {
             // re-save element with no table data - this can be an issue if someone is currently efiting things - but hey, 
            $element->setFieldValue( $fieldHandle, $teamMembersRaw );
            $success = Craft::$app->elements->saveElement($element);
            if (!$success) {
                Craft::error('Couldn’t save the Team entry "'.$element->title.'"', __METHOD__);
            }
        }

       

       

        //

    }



    // Protected Methods
      // =========================================================================

      /**
       * @param $html
       * @param $subject
       * @param null $mail
       * @param array $attachments
       * @return bool
       */
      private function sendMail($html, $subject, $mail = null, array $attachments = array()): bool
      {
          $settings = Craft::$app->systemSettings->getSettings('email');
          $message = new Message();

          $message->setFrom([$settings['fromEmail'] => $settings['fromName']]);
          $message->setTo($mail);
          $message->setSubject($subject);
          $message->setHtmlBody($html);
          if (!empty($attachments) && \is_array($attachments)) {

              foreach ($attachments as $fileId) {
                  if ($file = Craft::$app->assets->getAssetById((int)$fileId)) {
                      $message->attach($this->getFolderPath() . '/' . $file->filename, array(
                          'fileName' => $file->title . '.' . $file->getExtension()
                      ));
                  }
              }
          }

          return Craft::$app->mailer->send($message);
      }
    
}
