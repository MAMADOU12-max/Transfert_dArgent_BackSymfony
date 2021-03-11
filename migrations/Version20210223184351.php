<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223184351 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE compte_user (compte_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_D5ABD2D5F2C56620 (compte_id), INDEX IDX_D5ABD2D5A76ED395 (user_id), PRIMARY KEY(compte_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compte_user ADD CONSTRAINT FK_D5ABD2D5F2C56620 FOREIGN KEY (compte_id) REFERENCES compte (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE compte_user ADD CONSTRAINT FK_D5ABD2D5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE depot ADD caissiers_id INT DEFAULT NULL, ADD comptes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBC56CF4096 FOREIGN KEY (caissiers_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCDCED588B FOREIGN KEY (comptes_id) REFERENCES compte (id)');
        $this->addSql('CREATE INDEX IDX_47948BBC56CF4096 ON depot (caissiers_id)');
        $this->addSql('CREATE INDEX IDX_47948BBCDCED588B ON depot (comptes_id)');
        $this->addSql('ALTER TABLE user ADD profils_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B9881AFB FOREIGN KEY (profils_id) REFERENCES profil (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649B9881AFB ON user (profils_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE compte_user');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBC56CF4096');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCDCED588B');
        $this->addSql('DROP INDEX IDX_47948BBC56CF4096 ON depot');
        $this->addSql('DROP INDEX IDX_47948BBCDCED588B ON depot');
        $this->addSql('ALTER TABLE depot DROP caissiers_id, DROP comptes_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B9881AFB');
        $this->addSql('DROP INDEX IDX_8D93D649B9881AFB ON user');
        $this->addSql('ALTER TABLE user DROP profils_id');
    }
}
