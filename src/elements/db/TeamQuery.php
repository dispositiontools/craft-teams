<?php
namespace dispositiontools\teams\elements\db;

use craft\db\Query;
use craft\elements\db\ElementQuery;
use craft\helpers\Db;
use dispositiontools\teams\elements\team;

class TeamQuery extends ElementQuery
{
    public $teamTypeId;


    public function teamTypeId($value)
    {
        $this->teamTypeId = $value;

        return $this;
    }


    protected function beforePrepare(): bool
    {
        // join in the products table
        $this->joinElementTable('teams_teams');

        // select the price column
        $this->query->select([
            'teams_teams.teamTypeId',
            'teams_teams.fieldLayoutId',
            'teams_teams.description',
            'teams_teams.status',
            'teams_teams.authorId',

        ]);

        if ($this->teamTypeId) {
            $this->subQuery->andWhere(Db::parseParam('teams_teams.teamTypeId', $this->teamTypeId));
        }



        return parent::beforePrepare();
    }
}
