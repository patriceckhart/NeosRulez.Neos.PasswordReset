<?php

declare(strict_types=1);

namespace Neos\Flow\Persistence\Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230915100521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on "mysql".'
        );

        $this->addSql('CREATE TABLE neosrulez_neos_passwordreset_domain_model_token (persistence_object_identifier VARCHAR(40) NOT NULL, user VARCHAR(40) DEFAULT NULL, token VARCHAR(255) NOT NULL, valid TINYINT(1) NOT NULL, created DATETIME NOT NULL, INDEX IDX_8732F9A38D93D649 (user), PRIMARY KEY(persistence_object_identifier)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE neosrulez_neos_passwordreset_domain_model_token ADD CONSTRAINT FK_8732F9A38D93D649 FOREIGN KEY (user) REFERENCES neos_neos_domain_model_user (persistence_object_identifier)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf(
            $this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on "mysql".'
        );

        $this->addSql('DROP TABLE neosrulez_neos_passwordreset_domain_model_token');
    }
}
