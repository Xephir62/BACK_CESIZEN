<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260519093000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Relation affectation: un utilisateur a un exercice; un exercice peut avoir plusieurs utilisateurs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE utilisateurs ADD exercice_id INT DEFAULT NULL, ADD INDEX IDX_497B315E73A26D7D (exercice_id)');

        // Transfert des affectations existantes: on garde le dernier exercice trouve pour chaque utilisateur.
        $this->addSql('UPDATE utilisateurs u SET exercice_id = (SELECT e.id FROM exercice e WHERE e.utilisateur_id = u.id ORDER BY e.id DESC LIMIT 1) WHERE EXISTS (SELECT 1 FROM exercice ex WHERE ex.utilisateur_id = u.id)');

        $this->addSql('ALTER TABLE utilisateurs ADD CONSTRAINT FK_497B315E73A26D7D FOREIGN KEY (exercice_id) REFERENCES exercice (id)');
        $this->addSql('ALTER TABLE exercice DROP FOREIGN KEY FK_463141DEFB88E14F');
        $this->addSql('DROP INDEX IDX_463141DEFB88E14F ON exercice');
        $this->addSql('ALTER TABLE exercice DROP utilisateur_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE exercice ADD utilisateur_id INT NOT NULL, ADD INDEX IDX_463141DEFB88E14F (utilisateur_id)');
        $this->addSql('UPDATE exercice e SET utilisateur_id = (SELECT u.id FROM utilisateurs u WHERE u.exercice_id = e.id ORDER BY u.id ASC LIMIT 1)');
        $this->addSql('ALTER TABLE exercice ADD CONSTRAINT FK_463141DEFB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs (id)');
        $this->addSql('ALTER TABLE utilisateurs DROP FOREIGN KEY FK_497B315E73A26D7D');
        $this->addSql('DROP INDEX IDX_497B315E73A26D7D ON utilisateurs');
        $this->addSql('ALTER TABLE utilisateurs DROP exercice_id');
    }
}
