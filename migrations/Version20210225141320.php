<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210225141320 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1DCED588B');
        $this->addSql('DROP INDEX IDX_723705D1DCED588B ON transaction');
        $this->addSql('ALTER TABLE transaction ADD compte_retrait_id INT DEFAULT NULL, CHANGE comptes_id compte_envoie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D181D20A4F FOREIGN KEY (compte_envoie_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1B6EC9AC4 FOREIGN KEY (compte_retrait_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_723705D181D20A4F ON transaction (compte_envoie_id)');
        $this->addSql('CREATE INDEX IDX_723705D1B6EC9AC4 ON transaction (compte_retrait_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D181D20A4F');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1B6EC9AC4');
        $this->addSql('DROP INDEX IDX_723705D181D20A4F ON transaction');
        $this->addSql('DROP INDEX IDX_723705D1B6EC9AC4 ON transaction');
        $this->addSql('ALTER TABLE transaction ADD comptes_id INT DEFAULT NULL, DROP compte_envoie_id, DROP compte_retrait_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1DCED588B FOREIGN KEY (comptes_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_723705D1DCED588B ON transaction (comptes_id)');
    }
}
