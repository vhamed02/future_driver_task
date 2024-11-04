<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241101235959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create User and Company tables';
    }

    public function up(Schema $schema): void
    {
        // Company table
        $this->addSql('CREATE TABLE `companies` (
                            id INT AUTO_INCREMENT NOT NULL, 
                            name VARCHAR(100) UNIQUE NOT NULL, 
                            PRIMARY KEY(id)
                       )');

        // User table
        $this->addSql('CREATE TABLE `users` (
                            id INT AUTO_INCREMENT NOT NULL, 
                            name VARCHAR(100) NOT NULL, 
                            role VARCHAR(50) NOT NULL, 
                            company_id INT DEFAULT NULL, 
                            INDEX IDX_COMPANY_ID (company_id), 
                            PRIMARY KEY(id)
                    )');
        $this->addSql('ALTER TABLE `users` ADD CONSTRAINT FK_COMPANY_ID FOREIGN KEY (company_id) REFERENCES `companies` (id)');
    }

    public function down(Schema $schema): void
    {
        // Drop the user table
        $this->addSql('DROP TABLE `user`');
        // Drop the company table
        $this->addSql('DROP TABLE `company`');
    }
}
