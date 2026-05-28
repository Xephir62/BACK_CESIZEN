<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260518140500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes inspiration_seconde et respiration_seconde dans exercice';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exercice ADD inspiration_seconde INT DEFAULT NULL, ADD respiration_seconde INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exercice DROP inspiration_seconde, DROP respiration_seconde');
    }
}
