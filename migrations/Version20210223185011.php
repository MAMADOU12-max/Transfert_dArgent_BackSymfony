<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223185011 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE compte_user');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compte_user (compte_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D5ABD2D5F2C56620 (compte_id), INDEX IDX_D5ABD2D5A76ED395 (user_id), PRIMARY KEY(compte_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE compte_user ADD CONSTRAINT FK_D5ABD2D5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE compte_user ADD CONSTRAINT FK_D5ABD2D5F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
    }
}
