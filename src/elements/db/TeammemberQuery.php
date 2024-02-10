<?php
namespace dispositiontools\teams\elements\db;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use dispositiontools\teams\elements\Teammember;

class TeammemberQuery extends ElementQuery
{


    public $teamElementId;
    public $elementType;
    public $startDate;
    public $userId;
    public $endDate;
    public $isAdmin;
    public $isMember;
    public $notifications;
    public $notificationsStartDate;
    public $notificationsEndDate;
    public $isBillingAdmin;
    public $autoJoin;
    public $autoJoined;
    public $teamMemberStatus;
    public $emailAddress;
    public $fieldId;
    

        public function teamElementId($value)
        {
            $this->teamElementId = $value;
            return $this;
        }
        public function elementType($value)
        {
            $this->elementType = $value;
            return $this;
        }

        public function userId($value)
        {
            $this->userId = $value;
            return $this;
        }

        public function fieldId($value)
        {
            $this->fieldId = $value;
            return $this;
        }

        public function autoJoin($value)
        {
            $this->autoJoin = $value;
            return $this;
        }
        public function autoJoined($value)
        {
            $this->autoJoined = $value;
            return $this;
        }
        public function startDate($value)
        {
            $this->startDate = $value;
            return $this;
        }
        public function endDate($value)
        {
            $this->endDate = $value;
            return $this;
        }
        public function isAdmin($value)
        {
            $this->isAdmin = $value;
            return $this;
        }
        public function isMember($value)
        {
            $this->isMember = $value;
            return $this;
        }
        public function notifications($value)
        {
            $this->notifications = $value;
            return $this;
        }
        public function notificationsStartDate($value)
        {
            $this->notificationsStartDate = $value;
            return $this;
        }
        public function notificationsEndDate($value)
        {
            $this->notificationsEndDate = $value;
            return $this;
        }
        public function teamMemberStatus($value)
        {
            $this->teamMemberStatus = $value;
            return $this;
        }

        public function emailAddress($value)
        {
            $this->emailAddress = $value;
            return $this;
        }


    protected function beforePrepare(): bool
    {
        // join in the products table
        $this->joinElementTable('teams_members');

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
            'teams_members.elementType',
            'teams_members.userGroups',
            'teams_members.dateJoined',
            'teams_members.autoJoined',
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

         /* 
        if( $this->elementType ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.elementType', $this->elementType));
        }
        if( $this->startDate ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.startDate', $this->startDate));
        }
        if( $this->endDate ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.endDate', $this->endDate));
        }
        if( $this->isAdmin ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.isAdmin', $this->isAdmin));
        }
        if( $this->isMember ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.isMember', $this->isMember));
        }
        if( $this->notifications ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.notifications', $this->notifications));
        }
        if( $this->notificationsStartDate ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.notificationsStartDate', $this->notificationsStartDate));
        }
        if( $this->notificationsEndDate ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.notificationsEndDate', $this->notificationsEndDate));
        }
        if( $this->isBillingAdmin ){
            $this->subQuery->andWhere(Db::parseParam('teams_members.isBillingAdmin', $this->isBillingAdmin));
        }
         */


        return parent::beforePrepare();
    }
}
