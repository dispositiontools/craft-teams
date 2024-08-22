<?php

namespace dispositiontools\teams\elements\db;

use Craft;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use dispositiontools\teams\elements\Teammember;
/**
 * Teammember query
 */
class TeammemberQuery extends ElementQuery
{

    public ?int $teamElementId = null;
    public ?string $teamElementType = null;
    public ?DateTime $startDate = null;
    public ?int $userId = null;
    public ?DateTime $endDate = null;
    public ?bool $isAdmin = null;
    public ?bool $isMember = null;
    public ?bool $notifications = null;
    public ?DateTime $notificationsStartDate = null;
    public ?DateTime $notificationsEndDate = null;
    public ?bool $isBillingAdmin = null;
    public ?bool $autoJoin = null;
    public ?bool $autoJoined = null;
    public ?string $teamMemberStatus = null;
    public ?string $emailAddress = null;
    public ?int $fieldId = null;
    

    public function teamElementId($value): self
    {
        $this->teamElementId = $value;
        return $this;
    }

    
    public function teamElementType(?string $value = null): self 
    {
        $this->elementType = $value;
        return $this;
    }

    public function userId($value): self
    {
        $this->userId = $value;
        return $this;
    }

    public function fieldId($value): self
    {
        $this->fieldId = $value;
        return $this;
    }

    public function autoJoin($value): self
    {
        $this->autoJoin = $value;
        return $this;
    }
    public function autoJoined($value):self
    {
        $this->autoJoined = $value;
        return $this;
    }
    public function startDate($value):self
    {
        $this->startDate = $value;
        return $this;
    }
    public function endDate($value):self
    {
        $this->endDate = $value;
        return $this;
    }
    public function isAdmin($value):self
    {
        $this->isAdmin = $value;
        return $this;
    }
    public function isMember($value):self
    {
        $this->isMember = $value;
        return $this;
    }
    public function notifications($value):self
    {
        $this->notifications = $value;
        return $this;
    }
    public function notificationsStartDate($value):self
    {
        $this->notificationsStartDate = $value;
        return $this;
    }
    public function notificationsEndDate($value):self
    {
        $this->notificationsEndDate = $value;
        return $this;
    }
    public function teamMemberStatus($value):self
    {
        $this->teamMemberStatus = $value;
        return $this;
    }

    public function emailAddress($value):self
    {
        $this->emailAddress = $value;
        return $this;
    }






    protected function beforePrepare(): bool
    {
        // todo: join the `teams_members` table
        $this->joinElementTable('teams_members');

        // apply any custom query params

        // select the price column
        $this->query->select([
            'teams_members.teamMemberStatus',
            'teams_members.teamElementId',
            'teams_members.fieldId',
            'teams_members.emailAddress',
            'teams_members.userId',
            'teams_members.firstName',
            'teams_members.lastName',
            'teams_members.notes',
            'teams_members.teamElementType',
            'teams_members.userGroups',
            'teams_members.dateJoined',
            'teams_members.dateLeft',
            'teams_members.autoJoin',
            'teams_members.startDate',
            'teams_members.endDate',
            'teams_members.isAdmin',
            'teams_members.isMember',
            'teams_members.notifications',
            'teams_members.notificationsStartDate',
            'teams_members.notificationsEndDate',
            'teams_members.isBillingAdmin',
        ]);

        if( $this->teamElementId ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.teamElementId', $this->teamElementId));
        } 

        if( $this->userId ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.userId', $this->userId));
        } 

        if( $this->teamMemberStatus ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.teamMemberStatus', $this->teamMemberStatus));
        } 

        if( $this->autoJoin ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.autoJoin', $this->autoJoin));
        } 

        if( $this->emailAddress ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.emailAddress', $this->emailAddress));
        } 

        if( $this->fieldId ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.fieldId', $this->fieldId));
        } 

        return parent::beforePrepare();
    }
}
