# Changelog

Toutes les modifications notables de ce projet sont documentées dans ce fichier.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/)
et le projet respecte le [versionnage sémantique](https://semver.org/lang/fr/).

## [Non publié]

### Ajouté
- Stratégie de branches documentée dans le `CONTRIBUTING.md` (`main`, `develop`, `feature/*`, `hotfix/*`)
- Convention Conventional Commits, vérifiée par un hook `commit-msg` local et un workflow d'intégration continue
- Protection des branches `main` et `develop` : pull request obligatoire, force-push et suppression bloqués

---

## [1.0.0] - 2026-09-01

Première version stabilisée de l'API. Backend Symfony exposant les ressources
du projet CESIZen via API Platform, avec authentification JWT et déploiement
conteneurisé.

### Ajouté

**Authentification et comptes**
- Authentification par JWT (LexikJWTAuthenticationBundle) avec refresh token persisté (GesdinetJWTRefreshTokenBundle)
- Entités `Utilisateurs`, `RolesUtilisateurs`, `RefreshToken` et `RenitialisationMdp`
- Hachage des mots de passe et processeur dédié `UserPasswordProcessor`
- Flux complet de réinitialisation de mot de passe par courriel via `PasswordController`, avec jeton à usage unique
- Configuration du mailer pour l'envoi des courriels transactionnels

**Domaine métier**
- Entités `Ressource`, `Exercice` et `Commentaire`, avec leurs repositories
- Processeur `CommentaireProcessor` pour la création de commentaires
- Contrôleur d'administration des ressources
- Exposition des ressources via API Platform

**Base de données**
- Migrations Doctrine couvrant l'ensemble du schéma initial

**Infrastructure**
- `Dockerfile` et configuration Apache pour l'exécution de l'application
- `compose.yaml` orchestrant l'application et la base de données
- Séparation des configurations de développement et de production

### Sécurité

- Correction d'une élévation de privilèges permettant à un utilisateur de modifier ses propres rôles
- Restriction des opérations exposées sur la ressource `Utilisateurs` : lecture et écriture limitées aux propriétaires et aux administrateurs
- Restriction de l'accès à l'API de réinitialisation de mot de passe, auparavant exposée en lecture
- Ajout d'en-têtes de sécurité HTTP centralisés dans `SecurityHeadersSubscriber`
- Configuration explicite du CORS via NelmioCorsBundle, en remplacement du souscripteur maison
- Journalisation applicative configurée par Monolog
- Durcissement de la configuration de production : désactivation des outils de développement, restriction de la documentation API

### Modifié

- Unification de la gestion des en-têtes HTTP, auparavant dispersée entre plusieurs souscripteurs

### Supprimé

- Code mort dans les entités `Exercice`, `Ressource` et `Utilisateurs`
- Souscripteur CORS maison, remplacé par NelmioCorsBundle

---

[Non publié]: https://github.com/Xephir62/BACK_CESIZEN/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/Xephir62/BACK_CESIZEN/releases/tag/v1.0.0
