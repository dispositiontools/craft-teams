<?php

namespace dispositiontools\teams\migrations;

use Craft;
use craft\db\Migration;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Place installation code here...
        if ($this->createTables()) {
            //$this->createIndexes();
            $this->addForeignKeys();
            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
            //$this->insertDefaultData();
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        // Place uninstallation code here...
        $this->removeTables();
        return true;
    }

    // Protected Methods
    // =========================================================================

    /**
     * Creates the tables needed for the Records used by the plugin
     *
     * @return bool
     */
    protected function createTables(): bool
    {
        $tablesCreated = false;



        // teams table
            $tableSchema = Craft::$app->db->schema->getTableSchema('{{%teams_teams}}');
            if ($tableSchema === null) {
                $tablesCreated = true;
                $this->createTable(
                    '{{%teams_teams}}',
                    [
                        'id' => $this->primaryKey(),
                        'dateCreated' => $this->dateTime()->notNull(),
                        'dateUpdated' => $this->dateTime()->notNull(),
                        'uid' => $this->uid(),
                    // Custom columns in the table
                        'fieldLayoutId' => $this->integer()->defaultValue(NULL),
                      	'authorId' 	=> $this->integer()->defaultValue(NULL),
                      	'teamTypeId' 	=> $this->integer()->defaultValue(NULL),
                      	'description' 	=> $this->text()->defaultValue(NULL),
                        'status' 	=> $this->string()->defaultValue(NULL),
                        

                    ]
                );
            }


            // teams_members table
                $tableSchema = Craft::$app->db->schema->getTableSchema('{{%teams_members}}');
                if ($tableSchema === null) {
                    $tablesCreated = true;
                    $this->createTable(
                        '{{%teams_members}}',
                        [
                            'id' => $this->primaryKey(),
                            'dateCreated' => $this->dateTime()->notNull(),
                            'dateUpdated' => $this->dateTime()->notNull(),
                            'uid' => $this->uid(),
                        // Custom columns in the table
                            'fieldLayoutId' => $this->integer()->defaultValue(NULL),
                            'fieldId' => $this->integer()->defaultValue(NULL),
                            'dateInviteSent' => $this->dateTime()->defaultValue(NULL),
                            'dateInviteClicked' => $this->dateTime()->defaultValue(NULL),
                            'dateInviteAccepted' => $this->dateTime()->defaultValue(NULL),
                            'dateInviteDeclined' => $this->dateTime()->defaultValue(NULL),

                            
                            'dateJoined' => $this->dateTime()->defaultValue(NULL),
                            'dateLeft' => $this->dateTime()->defaultValue(NULL),
                            'dateUserCreated' => $this->dateTime()->defaultValue(NULL),
                            'autoJoined' => $this->boolean()->defaultValue(NULL),
                            'autoJoin' => $this->boolean()->defaultValue(NULL),
               
                            'teamMemberStatus' 	=> $this->string()->defaultValue(NULL),
                            'invitedByUserId' 	=> $this->integer()->defaultValue(NULL),
                            'teamElementId' 	=> $this->integer()->defaultValue(NULL),
                            'userId' 	=> $this->integer()->defaultValue(NULL),
                            'emailAddress' 	=> $this->string()->defaultValue(NULL),
                            'firstName' 	=> $this->string()->defaultValue(NULL),
                            'lastName' 	=> $this->string()->defaultValue(NULL),
                            'notes' 	=> $this->string()->defaultValue(NULL),
                            'userAccount' 	=> $this->string()->defaultValue(NULL),
                            'userGroups' 	=> $this->string()->defaultValue(NULL),
                            'teamElementType' 	=> $this->string()->defaultValue(NULL),
                            'startDate' 	=> $this->dateTime()->defaultValue(NULL),
                            'endDate' 	=> $this->dateTime()->defaultValue(NULL),
                            'isAdmin' => $this->boolean()->defaultValue(NULL),
                            'isMember' => $this->boolean()->defaultValue(NULL),
                            'notifications' => $this->boolean()->defaultValue(NULL),
                            'notificationsStartDate' 	=> $this->dateTime()->defaultValue(NULL),
                            'notificationsEndDate' 	=> $this->dateTime()->defaultValue(NULL),
                            'isBillingAdmin' => $this->boolean()->defaultValue(NULL),
                            

                        ]
                    );
                }


    // teams_types table
        $tableSchema = Craft::$app->db->schema->getTableSchema('{{%teams_types}}');
        if ($tableSchema === null) {
            $tablesCreated = true;
            $this->createTable(
                '{{%teams_types}}',
                [
                    'id' => $this->primaryKey(),
                    'dateCreated' => $this->dateTime()->notNull(),
                    'dateUpdated' => $this->dateTime()->notNull(),
                    'uid' => $this->uid(),
                // Custom columns in the table
                    'siteId' => $this->integer()->notNull(),

                    'enabled' 	=> $this->boolean()->defaultValue(NULL),
                    'archived' 	=> $this->boolean()->defaultValue(NULL),

                    'title' 	=> $this->string()->defaultValue(NULL),
                    'handle' 	=> $this->string()->defaultValue(NULL),
                    'description' 	=> $this->text()->defaultValue(NULL),

                    'fieldLayoutId' => $this->integer()->notNull(),
                ]
            );
        }





        return true;
    }


        /**
     * Creates the foreign keys needed for the Records used by the plugin
     *
     * @return void
     */
    protected function addForeignKeys(): void
    {


      // teams_teams table
      /*
          $this->addForeignKey(
              $this->db->getForeignKeyName('{{%teams_teams}}', 'siteId'),
              '{{%teams_teams}}',
              'siteId',
              '{{%sites}}',
              'id',
              'CASCADE',
              'CASCADE'
          );


    // teams_members table
        $this->addForeignKey(
            $this->db->getForeignKeyName('{{%teams_members}}', 'siteId'),
            '{{%teams_members}}',
            'siteId',
            '{{%sites}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

   */
    }


    /**
     * Removes the tables needed for the Records used by the plugin
     *
     * @return void
     */
    protected function removeTables(): void
    {
        // teams tables
        $this->dropTableIfExists('{{%teams_teams}}');
        $this->dropTableIfExists('{{%teams_members}}');
        $this->dropTableIfExists('{{%teams_types}}');


    }

}
