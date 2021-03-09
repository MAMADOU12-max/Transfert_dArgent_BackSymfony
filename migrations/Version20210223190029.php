<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223190029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction ADD retrait_id INT DEFAULT NULL, ADD deposer_id INT DEFAULT NULL, ADD recuperer_id INT DEFAULT NULL, ADD envoyer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D17EF8457A FOREIGN KEY (retrait_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1788E566C FOREIGN KEY (deposer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D128130887 FOREIGN KEY (recuperer_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D172ED896E FOREIGN KEY (envoyer_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_723705D17EF8457A ON transaction (retrait_id)');
        $this->addSql('CREATE INDEX IDX_723705D1788E566C ON transaction (deposer_id)');
        $this->addSql('CREATE INDEX IDX_723705D128130887 ON transaction (recuperer_id)');
        $this->addSql('CREATE INDEX IDX_723705D172ED896E ON transaction (envoyer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D17EF8457A');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1788E566C');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D128130887');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D172ED896E');
        $this->addSql('DROP INDEX IDX_723705D17EF8457A ON transaction');
        $this->addSql('DROP INDEX IDX_723705D1788E566C ON transaction');
        $this->addSql('DROP INDEX IDX_723705D128130887 ON transaction');
        $this->addSql('DROP INDEX IDX_723705D172ED896E ON transaction');
        $this->addSql('ALTER TABLE transaction DROP retrait_id, DROP deposer_id, DROP recuperer_id, DROP envoyer_id');
    }
}
