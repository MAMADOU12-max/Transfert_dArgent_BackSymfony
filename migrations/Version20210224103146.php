<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224103146 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D128130887');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D172ED896E');
        $this->addSql('DROP INDEX IDX_723705D128130887 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D172ED896E ON transaction');
        $this->addSql('ALTER TABLE transaction DROP recuperer_id, DROP envoyer_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD recuperer_id INT DEFAULT NULL, ADD envoyer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D128130887 FOREIGN KEY (recuperer_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D172ED896E FOREIGN KEY (envoyer_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_723705D128130887 ON transaction (recuperer_id)');
        $this->addSql('CREATE INDEX IDX_723705D172ED896E ON transaction (envoyer_id)');
    }
}
