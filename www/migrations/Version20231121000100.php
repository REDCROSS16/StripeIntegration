<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\SchemaException;
use Doctrine\DBAL\Schema\Table;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial migration which creates basic database structure
 */
final class Version20231121000100 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return 'Initial migration which creates basic database structure';
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws SchemaException
     */
    public function up(Schema $schema): void
    {
//        $this->createMessengerMessagesTable($schema);
    }

    private function createUserTable(Schema $schema): void
    {
        if (!$schema->hasTable('user')) {
            $table = $schema->createTable('user');

            $table->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
            $table->addColumn('email', 'string', ['notnull' => false, 'default' => null, 'length' => 180]);
            $table->addColumn('login', 'string', ['notnull' => false, 'default' => null, 'length' => 180]);
            $table->addColumn('roles', 'json', ['notnull' => true]);
            $table->addColumn('password', 'string', ['notnull' => true]);
            $table->addColumn('first_name', 'string', ['notnull' => true, 'length' => 50]);
            $table->addColumn('last_name', 'string', ['notnull' => true, 'length' => 50]);
            $table->addColumn('patronymic', 'string', ['notnull' => false, 'length' => 50, 'default' => null]);
            $table->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);
            $table->addColumn('updated_at', 'datetime_immutable', ['notnull' => true]);

            $table->setPrimaryKey(['user_id']);
            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Таблица для хранения пользователей');

            $table->addForeignKeyConstraint('company', ['company_id'], ['company_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_USER_COMPANY');
            $table->addForeignKeyConstraint('country', ['country_id'], ['country_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_USER_COUNTRY');
            $table->addForeignKeyConstraint('region', ['region_id'], ['region_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_USER_REGION');
            $table->addForeignKeyConstraint('city', ['city_id'], ['city_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_USER_CITY');
            $table->addForeignKeyConstraint('user', ['parent_id'], ['user_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_USER_PARENT_USER');
        }
    }

    private function createUserCardTable(Schema $schema): void
    {
        if (!$schema->hasTable('user_card')) {
            $table = $schema->createTable('user_card');

            $table->addColumn('card_id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
            $table->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => true]);
            $table->addColumn('name', 'string', ['notnull' => true, 'length' => 100]);
            $table->addColumn('pan', 'integer', ['unsigned' => true, 'notnull' => true, 'length' => 16]);
            $table->addColumn('expiration', 'string', ['notnull' => true, 'length' => 5]);
            $table->addColumn('cvv', 'string', ['notnull' => true, 'length' => 4]);

            // , cvv, cAt, status, token

            $table->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);

            $table->setPrimaryKey(['contact_id'], 'contact_id');
            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Таблица для хранения контактов пользователей');

            $table->addIndex(['user_id'], 'user_id');
            $table->addIndex(['type_id'], 'type_id');
            $table->addUniqueIndex(['type_id', 'value'], 'type_id_value');

            $table->addForeignKeyConstraint('user', ['user_id'], ['user_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_USER_CONTACT_USER');
        }
    }
}