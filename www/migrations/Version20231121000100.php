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
        $this->createUserTable($schema);
        $this->createInvoiceTable($schema);
        $this->createPaymentTable($schema);
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws SchemaException
     */
    private function createUserTable(Schema $schema): void
    {
        if (!$schema->hasTable('user')) {
            $table = $schema->createTable('user');

            $table->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
            $table->addColumn('email', 'string', ['notnull' => false, 'default' => null, 'length' => 180]);
            $table->addColumn('first_name', 'string', ['notnull' => true, 'length' => 50]);
            $table->addColumn('last_name', 'string', ['notnull' => true, 'length' => 50]);
            $table->addColumn('phone', 'string', ['notnull' => false, 'length' => 20, 'default' => null]);
            $table->addColumn('roles', 'json', ['notnull' => true]);
            $table->addColumn('password', 'string', ['notnull' => true]);
            $table->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);
            $table->addColumn('updated_at', 'datetime_immutable', ['notnull' => true]);
            $table->addColumn('is_active', 'boolean', ['unsigned' => true, 'notnull' => true, 'default' => 1]);
            $table->addColumn('is_deleted', 'boolean', ['unsigned' => true, 'notnull' => true, 'default' => 0]);

            $table->setPrimaryKey(['user_id']);
            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Таблица для хранения пользователей');

            $table->addUniqueIndex(['email'], 'email');
            $table->addUniqueIndex(['phone'], 'phone');
        }
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws SchemaException
     */
    private function createInvoiceTable(Schema $schema): void
    {
        if (!$schema->hasTable('invoice')) {
            $table = $schema->createTable('invoice');

            $table->addColumn('invoice_id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
            $table->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => true]);
            $table->addColumn('payment_id', 'integer', ['unsigned' => true, 'notnull' => false]);
            $table->addColumn('order', 'string', ['notnull' => false, 'length' => 255]);
            $table->addColumn('amount', 'decimal', ['unsigned' => true, 'notnull' => true, 'precision' => 20, 'scale' => 2]);
            $table->addColumn('status', 'smallint', ['unsigned' => true, 'notnull' => true]);
            $table->addColumn('data', 'json', ['notnull' => false, 'default' => null]);
            $table->addColumn('description', 'text', ['notnull' => false, 'default' => null]);
            $table->addColumn('is_active', 'boolean', ['unsigned' => true, 'notnull' => true, 'default' => 1]);
            $table->addColumn('is_deleted', 'boolean', ['unsigned' => true, 'notnull' => true, 'default' => 0]);
            $table->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);
            $table->addColumn('updated_at', 'datetime_immutable', ['notnull' => true]);

            $table->setPrimaryKey(['invoice_id'], 'invoice_id');
            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Таблица для хранения счетов');

            $table->addForeignKeyConstraint('user', ['user_id'], ['user_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_INVOICE_USER');
            $table->addForeignKeyConstraint('payment', ['payment_id'], ['payment_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_INVOICE_PAYMENT');
        }
    }

    /**
     * @param Schema $schema
     * @return void
     * @throws SchemaException
     */
    private function createPaymentTable(Schema $schema): void
    {
        if (!$schema->hasTable('payment')) {
            $table = $schema->createTable('payment');

            $table->addColumn('payment_id', 'integer', ['unsigned' => true, 'notnull' => true, 'autoincrement' => true]);
            $table->addColumn('user_id', 'integer', ['unsigned' => true, 'notnull' => true]);
            $table->addColumn('invoice_id', 'integer', ['unsigned' => true, 'notnull' => true]);
            $table->addColumn('status', 'string', ['notnull' => true, 'length' => 255]);
            $table->addColumn('description', 'text', ['notnull' => false, 'default' => null]);
            $table->addColumn('created_at', 'datetime_immutable', ['notnull' => true]);
            $table->addColumn('updated_at', 'datetime_immutable', ['notnull' => true]);
            $table->addColumn('is_active', 'boolean', ['unsigned' => true, 'notnull' => true, 'default' => 1]);
            $table->addColumn('is_deleted', 'boolean', ['unsigned' => true, 'notnull' => true, 'default' => 0]);

            $table->setPrimaryKey(['payment_id'], 'payment_id');
            $table->addOption('engine', 'InnoDB');
            $table->addOption('comment', 'Таблица для хранения платежей');

            $table->addUniqueIndex(['invoice_id'], 'invoice_id');

            $table->addForeignKeyConstraint('user', ['user_id'], ['user_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_PAYMENT_USER');
            $table->addForeignKeyConstraint('invoice', ['invoice_id'], ['invoice_id'], ['onDelete' => 'restrict', 'onUpdate' => 'restrict'], 'FK_PAYMENT_INVOICE');
        }
    }
}
