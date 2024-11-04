<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241104171931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this method is called when migrating up
        $this->addSql('CREATE TABLE company (id SERIAL NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE(name), PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        // this method is called when migrating down
        $this->addSql('DROP TABLE company');
    }
}
