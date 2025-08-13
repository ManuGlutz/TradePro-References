<?php

declare(strict_types=1);

namespace Sylius\ReferenceLinking\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250101000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create app_reference_linking_policy table';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('app_reference_linking_policy');
        $table->addColumn('reference_type', 'string', ['length' => 64]);
        $table->addColumn('target_level', 'string', ['length' => 10]);
        $table->setPrimaryKey(['reference_type']);
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('app_reference_linking_policy');
    }
}

