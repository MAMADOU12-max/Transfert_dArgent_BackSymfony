<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210311185039 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agence (id INT AUTO_INCREMENT NOT NULL, nom_agence VARCHAR(255) NOT NULL, adress_agence VARCHAR(255) NOT NULL, disabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_64C19AA9E40E05FE (nom_agence), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom_complet VARCHAR(255) NOT NULL, phone INT NOT NULL, identity_number INT DEFAULT NULL, code_transaction VARCHAR(255) DEFAULT NULL, action VARCHAR(255) NOT NULL, montant INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commissions (id INT AUTO_INCREMENT NOT NULL, ttc INT NOT NULL, frais_etat INT NOT NULL, frais_system INT NOT NULL, frais_envoie INT NOT NULL, frais_retrait INT NOT NULL, active TINYINT(1) NOT NULL, archivage TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte (id INT AUTO_INCREMENT NOT NULL, users_id INT DEFAULT NULL, agence_id INT DEFAULT NULL, solde INT NOT NULL, identifiant_compte INT NOT NULL, disabled TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_CFF652602B0F2F04 (identifiant_compte), INDEX IDX_CFF6526067B3B43D (users_id), UNIQUE INDEX UNIQ_CFF65260D725330D (agence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE depot (id INT AUTO_INCREMENT NOT NULL, caissiers_id INT DEFAULT NULL, comptes_id INT DEFAULT NULL, date_depot DATE NOT NULL, montant_de_depot INT NOT NULL, archivage TINYINT(1) NOT NULL, INDEX IDX_47948BBC56CF4096 (caissiers_id), INDEX IDX_47948BBCDCED588B (comptes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE profil (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, archivage TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE summarize_transaction (id INT AUTO_INCREMENT NOT NULL, montant INT NOT NULL, compte INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tarifs (id INT AUTO_INCREMENT NOT NULL, borne_inferieur INT NOT NULL, borne_superieur INT NOT NULL, frais INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transaction (id INT AUTO_INCREMENT NOT NULL, recuperer_id INT DEFAULT NULL, envoyer_id INT DEFAULT NULL, compte_envoie_id INT DEFAULT NULL, compte_retrait_id INT DEFAULT NULL, retrait_user_id INT DEFAULT NULL, deposer_user_id INT DEFAULT NULL, montant INT NOT NULL, date_depot DATE NOT NULL, date_retrait DATE DEFAULT NULL, date_annulation DATE DEFAULT NULL, ttc INT NOT NULL, frais_etat INT NOT NULL, frais_system INT NOT NULL, frais_envoie INT NOT NULL, frais_retrait INT NOT NULL, code_transaction VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, INDEX IDX_723705D128130887 (recuperer_id), INDEX IDX_723705D172ED896E (envoyer_id), INDEX IDX_723705D181D20A4F (compte_envoie_id), INDEX IDX_723705D1B6EC9AC4 (compte_retrait_id), INDEX IDX_723705D164A899FD (retrait_user_id), INDEX IDX_723705D1CF82B0CA (deposer_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, profils_id INT DEFAULT NULL, agence_id INT DEFAULT NULL, username VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, phone INT DEFAULT NULL, identity_num INT NOT NULL, avatar LONGBLOB DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, archivage TINYINT(1) NOT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), INDEX IDX_8D93D649B9881AFB (profils_id), INDEX IDX_8D93D649D725330D (agence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF6526067B3B43D FOREIGN KEY (users_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE compte ADD CONSTRAINT FK_CFF65260D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBC56CF4096 FOREIGN KEY (caissiers_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE depot ADD CONSTRAINT FK_47948BBCDCED588B FOREIGN KEY (comptes_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D128130887 FOREIGN KEY (recuperer_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D172ED896E FOREIGN KEY (envoyer_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D181D20A4F FOREIGN KEY (compte_envoie_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1B6EC9AC4 FOREIGN KEY (compte_retrait_id) REFERENCES compte (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D164A899FD FOREIGN KEY (retrait_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1CF82B0CA FOREIGN KEY (deposer_user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B9881AFB FOREIGN KEY (profils_id) REFERENCES profil (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D725330D FOREIGN KEY (agence_id) REFERENCES agence (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF65260D725330D');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D725330D');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D128130887');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D172ED896E');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBCDCED588B');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D181D20A4F');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1B6EC9AC4');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649B9881AFB');
        $this->addSql('ALTER TABLE compte DROP FOREIGN KEY FK_CFF6526067B3B43D');
        $this->addSql('ALTER TABLE depot DROP FOREIGN KEY FK_47948BBC56CF4096');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D164A899FD');
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1CF82B0CA');
        $this->addSql('DROP TABLE agence');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE commissions');
        $this->addSql('DROP TABLE compte');
        $this->addSql('DROP TABLE depot');
        $this->addSql('DROP TABLE profil');
        $this->addSql('DROP TABLE summarize_transaction');
        $this->addSql('DROP TABLE tarifs');
        $this->addSql('DROP TABLE transaction');
        $this->addSql('DROP TABLE user');
    }
}
