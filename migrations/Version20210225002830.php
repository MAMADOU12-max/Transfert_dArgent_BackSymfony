<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225002830 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF6526039622A97');
        $this->addSql('DROP INDEX IDX_CFF6526039622A97 ON compte');
        $this->addSql('ALTER TABLE compte DROP admin_system_id');
        $this->addSql('ALTER TABLE depot CHANGE date_depot date_depot DATE NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte ADD admin_system_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF6526039622A97 FOREIGN KEY (admin_system_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_CFF6526039622A97 ON compte (admin_system_id)');
        $this->addSql('ALTER TABLE depot CHANGE date_depot date_depot DATE DEFAULT NULL');
    }
}
