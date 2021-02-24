<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223185623 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte ADD admin_system_id INT DEFAULT NULL, ADD agence_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF6526039622A97 FOREIGN KEY (admin_system_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('CREATE INDEX IDX_CFF6526039622A97 ON compte (admin_system_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CFF65260D725330D ON compte (agence_id)');
        $this->addSql('ALTER TABLE transaction ADD comptes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1DCED588B FOREIGN KEY (comptes_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_723705D1DCED588B ON transaction (comptes_id)');
        $this->addSql('ALTER TABLE user ADD agences_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6499917E4AB FOREIGN KEY (agences_id) REFERENCES agence (id)');
        $this->addSql('CREATE INDEX IDX_8D93D6499917E4AB ON user (agences_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF6526039622A97');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260D725330D');
        $this->addSql('DROP INDEX IDX_CFF6526039622A97 ON compte');
        $this->addSql('DROP INDEX UNIQ_CFF65260D725330D ON compte');
        $this->addSql('ALTER TABLE compte DROP admin_system_id, DROP agence_id');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1DCED588B');
        $this->addSql('DROP INDEX IDX_723705D1DCED588B ON transaction');
        $this->addSql('ALTER TABLE transaction DROP comptes_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499917E4AB');
        $this->addSql('DROP INDEX IDX_8D93D6499917E4AB ON user');
        $this->addSql('ALTER TABLE user DROP agences_id');
    }
}
