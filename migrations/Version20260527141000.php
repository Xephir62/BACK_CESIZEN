<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260527141000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la table ressource (titre, description, image, date_creation)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE ressource (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, date_creation DATETIME NOT NULL COMMENT \"(DC2Type:datetime_immutable)\", PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE ressource');
    }
}
