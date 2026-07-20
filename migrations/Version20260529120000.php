<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260529120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Seed des roles par defaut et creation d un compte administrateur de demo.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("INSERT INTO roles_utilisateurs (libelle) SELECT 'ROLE_USER' WHERE NOT EXISTS (SELECT 1 FROM roles_utilisateurs WHERE libelle = 'ROLE_USER')");
        $this->addSql("INSERT INTO roles_utilisateurs (libelle) SELECT 'ROLE_ADMIN' WHERE NOT EXISTS (SELECT 1 FROM roles_utilisateurs WHERE libelle = 'ROLE_ADMIN')");

        $this->addSql(<<<'SQL'
INSERT INTO utilisateurs (
    nom,
    prenom,
    telephone,
    email,
    mot_de_passe,
    pseudo,
    photo_profil,
    status_compte,
    date_creation,
    role_id,
    age,
    exercice_id
)
SELECT
    'Admin',
    'CesiZen',
    '0000000000',
    'admin@cesizen.local',
    '$2y$10$CwIoGA1Ly//Vpwi8JLJiQOcXtvLJeRFw1grP8NUHTQu4ATxdXuJMy',
    'admin',
    NULL,
    1,
    CURRENT_TIMESTAMP,
    r.id,
    30,
    NULL
FROM roles_utilisateurs r
WHERE r.libelle = 'ROLE_ADMIN'
  AND NOT EXISTS (
      SELECT 1
      FROM utilisateurs u
      WHERE u.email = 'admin@cesizen.local'
  )
SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM utilisateurs WHERE email = 'admin@cesizen.local'");
        $this->addSql("DELETE FROM roles_utilisateurs WHERE libelle IN ('ROLE_ADMIN', 'ROLE_USER')");
    }
}
