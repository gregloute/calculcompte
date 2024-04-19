<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240415165444 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE logo_transaction (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, fichier VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE transaction ADD logo_id INT NOT NULL');
        $this->addSql('ALTER TABLE transaction ADD CONSTRAINT FK_723705D1F98F144A FOREIGN KEY (logo_id) REFERENCES logo_transaction (id)');
        $this->addSql('CREATE INDEX IDX_723705D1F98F144A ON transaction (logo_id)');
        $this->addSql("INSERT INTO `logo_transaction` (`id`, `nom`, `fichier`) VALUES (1, 'Défaut', 'default.png')");
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transaction DROP FOREIGN KEY FK_723705D1F98F144A');
        $this->addSql('DROP TABLE logo_transaction');
        $this->addSql('DROP INDEX IDX_723705D1F98F144A ON transaction');
        $this->addSql('ALTER TABLE transaction DROP logo_id');
    }
}
