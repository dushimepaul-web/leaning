# FUTURE VIP SCHOOL — Système de Gestion Scolaire

Application de gestion scolaire complète (bulletins, emplois du temps, notes, finances) construite avec **CodeIgniter 3 + HMVC** et un **solveur Python OR-Tools** pour l'optimisation des emplois du temps.

---

## Prérequis

| Composant | Version | Rôle |
|---|---|---|
| **PHP** | 8.0+ | Serveur d'application |
| **MySQL** | 8.0+ / MariaDB 10.5+ | Base de données |
| **Apache** | 2.4+ (via WAMP) | Serveur web |
| **Python** | 3.10+ | Solveur OR-Tools |
| **pip** | — | Gestionnaire de paquets Python |
| **Composer** | — | Dépendances PHP (HMVC) |

---

## Installation

### 1. WAMP Server

Installer **WampServer 3.x** (Windows) ou tout serveur LAMP/WAMP :

- PHP 8.0+ avec extensions : `mysqli`, `json`, `mbstring`, `openssl`
- MySQL 8.0+
- Apache 2.4+

### 2. Base de données

```bash
# Créer la base
mysql -u root -e "CREATE DATABASE vip_school CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Importer le dump
mysql -u root vip_school < DB/vip_school.sql
```

### 3. PHP — Dépendances

```bash
composer install
```

Le projet utilise le framework **CodeIgniter 3** avec **HMVC** (Wiredesign Modular Extension).

### 4. Python — Solveur OR-Tools

Le module Horaires utilise un solveur Python pour générer les emplois du temps de manière optimale.

```bash
# Installer Python 3.10+ depuis python.org
# Vérifier l'installation :
python --version

# Installer OR-Tools :
pip install ortools
```

**Vérification** :
```bash
python -c "from ortools.sat.python import cp_model; print('OR-Tools OK')"
```

### 5. Configuration

#### 5.1. Chemins

Le fichier `application/config/config.php` doit contenir :

```php
$config['base_url'] = 'http://localhost/leaning/';
```

Le répertoire `FCPATH` est défini dans `index.php` (ligne 234) :
```php
define('FCPATH', dirname(__FILE__));
```

Le script Python se trouve dans : `python/generate_horaires.py`

Le contrôleur PHP (`Horaires.php`) détecte automatiquement l'exécutable Python aux emplacements suivants :
- `C:\Users\<user>\AppData\Local\Programs\Python\Python3XX\python.exe`
- `C:\Python3XX\python.exe`
- Fallback : `python` (doit être dans le PATH système)

#### 5.2. Connexion base de données

`application/config/database.php` :
```php
$db['default'] = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'vip_school',
    'dbdriver' => 'mysqli',
    // ...
];
```

#### 5.3. Permissions

```bash
# Dossier d'uploads (logo, photos)
chmod -R 775 assets/uploads/

# Dossier python (scripts temporaires)
chmod -R 775 python/
```

---

## Utilisation

### Connexion

URL : `http://localhost/leaning/`

Identifiant par défaut : `admin` / Mot de passe : `admin` (ou configuré lors de l'installation).

### Modules principaux

| Module | URL | Description |
|---|---|---|
| **Tableau de bord** | `/Dashboard` | Vue d'ensemble |
| **Élèves** | `/Etudiants` | Inscription, liste, fiches |
| **Classes** | `/Classes` | Gestion des classes, matières, périodes |
| **Notes** | `/Notes` | Évaluations, bulletins, fiches de points |
| **Enseignants** | `/Enseignants` | Liste, programmes, emploi du temps |
| **Emploi du temps** | `/Horaires` | Génération et affichage des grilles |
| **Disponibilités** | `/Disponibilites` | Gestion des disponibilités enseignants |
| **Finances** | `/Frais` / `/Paiement_recu` | Frais scolaires, reçus |
| **Paramètres** | `/Parametres` | Configuration globale |
| **Capacité enseignants** | `/Enseignants/Programmes/capacite` | Dashboard capacité |

---

## Module Emploi du Temps (Horaires)

### Génération

1. Aller sur **Horaires** → `http://localhost/leaning/Horaires`
2. Cliquer sur **"Diagnostiquer"** pour vérifier les contraintes
3. Cliquer sur **"Régénérer"** pour lancer la génération

**Flux technique** :
```
Utilisateur → Bouton "Régénérer"
    ↓
PHP: preflight() — validation préalable (capacité, indisponibilités)
    ↓
PHP: run_python_solver()
    ├─ Détecte l'exécutable Python
    ├─ Écrit le payload dans un fichier temporaire
    ├─ Appelle : python generate_horaires.py < input.json > output.json
    ├─ Lit le résultat JSON
    └─ Si échec → retourne une erreur (pas de fallback PHP)
    ↓
PHP: TRUNCATE horaires + INSERT batch
    ↓
Utilisateur voit la grille
```

### Résultat attendu

| Métrique | Valeur |
|---|---|
| Sessions placées | 240/240 (100%) |
| Conflits prof | 0 |
| Conflits classe | 0 |
| Taux regroupement | ~80% |
| Temps résolution | ~60 secondes |

### Contraintes

**Dures** (obligatoires) :
- 1 enseignant = 1 cours/créneau/jour
- 1 classe = 1 cours/créneau/jour
- Respect des indisponibilités enseignants
- Respect des limites quotidiennes par matière
- Créneaux fixes non déplaçables

**Soft** (optimisation) :
- Sessions multi-heures sur le même jour (créneaux consécutifs)
- Priorité aux enseignants les plus contraints (marge faible)

### Capacité enseignants

La page `Enseignants/Programmes/capacite` affiche un dashboard de capacité :

| Statut | Condition | Action |
|---|---|---|
| **OK** | Marge ≥ 3 | Aucune |
| **Tendu** | Marge = 1-2 | Ajouter un jour disponible |
| **Marge zéro** | Marge = 0 | Ajouter au moins 1 jour |
| **IMPOSSIBLE** | Marge < 0 | Ajouter N jours |

### Diagnostic intelligent

Le bouton **"Diagnostiquer"** affiche :
1. Tableau de capacité de chaque enseignant
2. Problèmes bloquants (erreurs empêchant la génération)
3. Avertissements (cas limites, marge 0)
4. Solutions proposées

### Plus de fallback PHP

Le solveur Python est le **seul moteur de génération**. Si Python échoue (timeout, erreur, Python non trouvé), une erreur est retournée à l'utilisateur avec le détail du problème.

### Logs

Les logs Python sont tracés dans `C:\wamp64\logs\php_error.log` avec le préfixe `[PYTHON-SOLVER]` :

```
[PYTHON-SOLVER] Debut — script=C:\...\python\generate_horaires.py — file_exists=OUI
[PYTHON-SOLVER] Python exe: C:\...\python.exe
[PYTHON-SOLVER] SUCCES — 240/240 sessions en 62.3s
```

---

## Module Notes & Bulletins

### Saisie des notes

1. **Notes** → Évaluations → Créer une évaluation (type, barème, période)
2. **Notes** → Grille de notes → Saisir les notes par classe
3. **Notes** → Bulletins → Générer/imprimer les bulletins

### Modes d'affichage

| Mode | Condition | Colonnes |
|---|---|---|
| **Mode B** | Compétences OU Ressources actives | TJ / COMP / RESS / TOT |
| **Mode A** | Seulement Examen actif | TJ / EX / TOT |
| **Défaut** | Aucune catégorie active | TJ / TOT |

### Impression

- **Bulletin** : `/Notes/Bulletins/export/{id_classe}`
- **Fiche de points** : `/Notes/Fiches/export/{id_classe}`
- **Fiche élève par cours** : `/Notes/Fiches/export/{id_classe}?matiere={id}&periode=`

---

## Structure du projet

```
leaning/
├── application/
│   ├── config/           # Configuration CI3
│   ├── modules/
│   │   ├── Horaires/     # Emploi du temps
│   │   │   ├── controllers/Horaires.php
│   │   │   ├── models/Horaires_model.php
│   │   │   ├── libraries/HorairesGenerator.php  # Preflight uniquement
│   │   │   └── views/index.php
│   │   ├── Notes/        # Notes & bulletins
│   │   ├── Classes/      # Gestion classes
│   │   ├── Enseignants/  # Enseignants + capacité
│   │   ├── Etudiants/    # Élèves
│   │   ├── Disponibilites/ # Dispos enseignants
│   │   └── Parametres/   # Configuration
│   └── views/            # Vues communes (Header, Footer)
├── assets/
│   ├── css/
│   ├── js/
│   │   └── api.js        # Routes API JS
│   └── vendor/           # Librairies JS (SheetJS, etc.)
├── python/
│   ├── generate_horaires.py  # Solveur CP-SAT OR-Tools (moteur unique)
│   ├── test_horaires.py      # Tests unitaires (15 tests)
│   └── test_real_data.py     # Test intégration données réelles
├── DB/
│   └── vip_school.sql    # Dump de la base
├── fonctionnement.md     # Documentation technique détaillée
├── contributing.md       # Guide de contribution
└── README.md             # Ce fichier
```

---

## Routes API

### Horaires

| Route | Méthode | Description |
|---|---|---|
| `api/horaires` | GET | Liste tous les horaires |
| `api/horaires/{uuid}` | GET | Détail d'un horaire |
| `api/horaires/create` | POST | Ajouter un horaire |
| `api/horaires/{uuid}/update` | PUT | Modifier un horaire |
| `api/horaires/{uuid}/delete` | DELETE | Supprimer (soft delete) |
| `api/horaires/generer` | POST | Générer l'emploi du temps |
| `api/horaires/generations` | GET | Lister les générations |

### Enseignants

| Route | Méthode | Description |
|---|---|---|
| `api/matieres_classes/teacher_capacity` | GET | Capacité de tous les enseignants |

---

## Tests

### Tests unitaires Python

```bash
python python/test_horaires.py
```

15 tests couvrant : placements, contraintes, Priorité enseignants, validation.

### Test d'intégration

```bash
python python/test_real_data.py
```

Test avec les données réelles de la base (240 sessions, 6 classes, ~15 enseignants).

---

## Résolution de problèmes

### Le solveur Python ne s'exécute pas

**Symptôme** : les logs montrent `file_exists=NON` ou fallback vers HorairesGenerator.

**Solutions** :
1. Vérifier que Python est installé : `python --version`
2. Vérifier OR-Tools : `pip show ortools`
3. Vérifier le path dans les logs : `[PYTHON-SOLVER] Python exe: ...`
4. Redémarrer Apache après modification du code PHP
5. Vérifier `C:\wamp64\logs\php_error.log` pour les erreurs détaillées

### Génération échoue (conflits)

**Symptôme** : erreurs "conflit enseignant" ou "cours non placé".

**Solutions** :
1. Cliquer sur "Diagnostiquer" pour identifier le problème
2. Vérifier les disponibilités enseignants (`/Disponibilites`)
3. Vérifier la page Capacité (`/Enseignants/Programmes/capacite`)
4. Ajouter des jours disponibles aux enseignants en surcharge

### Pages non accessibles

**Solutions** :
1. Vérifier que WAMP (Apache + MySQL) est démarré
2. Vérifier `config/base_url` correspond à votre URL
3. Vérifier les permissions sur `assets/uploads/`
4. Vider le cache du navigateur

---

## Technologiques

- **Backend** : PHP 8.0, CodeIgniter 3 (HMVC)
- **Base de données** : MySQL 9.1 (utf8mb4)
- **Frontend** : Bootstrap 5, DataTables, SweetAlert2
- **Optimisation** : Python 3.14, Google OR-Tools (CP-SAT solver)
- **Export** : SheetJS (Excel), impression CSS
- **Serveur** : WampServer 3.x (Apache 2.4)
