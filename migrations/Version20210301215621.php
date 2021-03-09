<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210301215621 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1788E566C');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D17EF8457A');
        $this->addSql('DROP INDEX IDX_723705D17EF8457A ON transaction');
        $this->addSql('DROP INDEX IDX_723705D1788E566C ON transaction');
        $this->addSql('ALTER TABLE transaction ADD retrait_user_id INT DEFAULT NULL, ADD deposer_user_id INT DEFAULT NULL, DROP retrait_id, DROP deposer_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D164A899FD FOREIGN KEY (retrait_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1CF82B0CA FOREIGN KEY (deposer_user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_723705D164A899FD ON transaction (retrait_user_id)');
        $this->addSql('CREATE INDEX IDX_723705D1CF82B0CA ON transaction (deposer_user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D164A899FD');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1CF82B0CA');
        $this->addSql('DROP INDEX IDX_723705D164A899FD ON transaction');
        $this->addSql('DROP INDEX IDX_723705D1CF82B0CA ON transaction');
        $this->addSql('ALTER TABLE transaction ADD retrait_id INT DEFAULT NULL, ADD deposer_id INT DEFAULT NULL, DROP retrait_user_id, DROP deposer_user_id');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1788E566C FOREIGN KEY (deposer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D17EF8457A FOREIGN KEY (retrait_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_723705D17EF8457A ON transaction (retrait_id)');
        $this->addSql('CREATE INDEX IDX_723705D1788E566C ON transaction (deposer_id)');
    }
}
