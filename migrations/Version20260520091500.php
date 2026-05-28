<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260520091500 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de lage utilisateur et de la tranche d age des exercices';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateurs ADD age INT DEFAULT NULL');
        $this->addSql('ALTER TABLE exercice ADD age_min INT DEFAULT NULL, ADD age_max INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exercice DROP age_min, DROP age_max');
        $this->addSql('ALTER TABLE utilisateurs DROP age');
    }
}
