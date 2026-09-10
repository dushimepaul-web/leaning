# Fonctionnement — Système de notation & bulletins

Ce document décrit la logique métier implémentée dans l'application. Il est **mis à jour à chaque modification** des règles.

## 1. Le TJ (Total de Jours / points max d'une matière)

- Le **TJ** d'une matière par période = `matieres_classes.note_max_matiere` (poids de la matière pour la classe).
- Si `note_max_matiere` n'est pas renseigné (0 ou null) → TJ = 0.

> Règle confirmée par l'utilisateur : **TJ = note_max_matiere directement** — `nb_heures_par_semaine` et `facteur_points_heure` ne sont pas utilisés dans le calcul du TJ.

## 2. Types d'évaluation et catégories

L'enum réelle dans la table `evaluations` est : `('interrogation','devoir','ressource','competance','examen')`.

### 2.1. Mapping unifié (TJ / COMP / RESS / EX)

| Type d'évaluation | Catégorie | Colonne bulletin |
|---|---|---|
| `interrogation` | **TJ** | TJ |
| `devoir` | **TJ** | TJ |
| `competance` | **COMP** | COMP |
| `ressource` | **RESS** | RESS |
| `examen` | **EX** | EX (colonnes spécifiques) |

### 2.2. Détection dynamique des catégories

`_detecter_categories($id_classe)` interroge la table `evaluations` pour déterminer les types **réellement présents** pour une classe donnée. Trois modes d'affichage en résultent :

| Mode | Condition | Colonnes par bloc | TJ comprend |
|---|---|---|---|
| **Mode B** | `competance` OU `ressource` présent | TJ / COMP / RESS / TOT (4 colonnes) | interrogation + devoir |
| **Mode A** | `examen` présent, PAS de competance/ressource | TJ / EX / TOT (3 colonnes) | interrogation + devoir |
| **Défaut** | Aucun des trois | TJ / TOT (2 colonnes) | interrogation + devoir |

> La colonne EX est **neutralisée** (remise à 0) en Mode B. Les colonnes COMP/RESS sont neutralisées en Mode A.

### 2.3. Pourcentages de répartition

- **Ressources à l'examen (%)** = `pourcentage_ressources_examen` (défaut **60**)
- **Compétences à l'examen (%)** = `pourcentage_competences_examen` (défaut **40**)

La répartition des maxima :
```
MAX RESS = TJ × pourcentage_ressources_examen / 100
MAX COMP = TJ × pourcentage_competences_examen / 100
MAX EX   = TJ (règle conservée par l'utilisateur)
MAX TOT  = TJ + MAX COMP + MAX RESS
```

- **Somme toujours = 100 %** pour COMP/RESS : la page Paramètres auto-complète automatiquement l'autre champ (`100 − valeur saisie`).
- Exemple : TJ = 180, RESS 60 % → MAX RESS = 108 ; COMP 40 % → MAX COMP = 72.

### 2.4. Neutralisation en cascade

Dans `get_bulletin_complet()` et `_build_result()` :
- **Mode B** : `ex = 0` pour chaque matière, chaque période, et les maxima.
- **Mode A** : `comp = 0` et `ress = 0` pour chaque matière, chaque période, et les maxima.
- **Défaut** : `comp = 0`, `ress = 0` et `ex = 0`.

### 2.5. Activation / désactivation

- Les activations sont gérées **par classe** via les champs de la table `classes` : `ressources_active`, `competences_active`.
- **Garde-fou** : au moins une catégorie doit rester active — impossible de désactiver les deux à la fois.

### 2.6. Comportement à la désactivation

Quand on désactive une catégorie dans Paramètres :

- **Désactiver Compétences** → « Compétences à l'examen (%) » passe à **0** et « Ressources à l'examen (%) » à **100**.
- **Désactiver Ressources** → « Ressources à l'examen (%) » passe à **0** et « Compétences à l'examen (%) » à **100**.
- La catégorie active **absorbe tout le TJ** : `MAX EX = TJ` (100 %), l'autre catégorie = 0.
- **Total max toujours = 2 × TJ** (TJ + EX = 2 × TJ), que les deux catégories soient actives ou non.

### 2.7. Priorité classe → global

Les activations et pourcentages peuvent être **surchargés par classe** (page Classes, champs de la table `classes` : `ressources_active`, `competences_active`, `ressources_pourcentage`, `competences_pourcentage`).

- Si la classe a une valeur renseignée → on utilise la valeur de la classe.
- Sinon → on utilise le paramètre global (les « à l'examen (%) » pour les pourcentages).

## 4. Affichage des bulletins et fiches

### 4.1. Deux catégories actives

Colonnes par bloc : **TJ | RESS | COMP | TOT** (4 colonnes, colspan = 4)

### 4.2. Une seule catégorie active

Colonnes par bloc : **TJ | EX | TOT** (3 colonnes, colspan = 3)

- **EX = TJ** (la catégorie active vaut 100 % du TJ).
- La colonne EX affiche la somme comp + ress (l'une des deux étant à 0).
- Le design ne change pas : seuls le nombre de colonnes et les en-têtes changent.

### 4.3. Largeur des colonnes

- Toutes les tables de bulletins/fiches utilisent **`table-layout: fixed`** → les colonnes se répartissent la largeur **également**, que le bloc ait 3 ou 4 colonnes.
- Seule la colonne **BRANCHE** (matière) garde sa largeur fixe (130–180 px selon la vue).

### 4.4. Lignes adaptatives

Toutes les lignes s'adaptent au nombre de colonnes :
- Matières, Conduite, Totaux, Pourcentage, Mention, Place, Religion, Signatures (PARENTS / TITULAIRE).

Exemple de la ligne Signatures : `PARENTS` occupe la 1re cellule du bloc MAXIMA → seules `colSpan-1` cellules vides sont ajoutées après (pas `colSpan`), pour éviter une colonne excédentaire.

## 5. Flux des données

```
Page Paramètres (settings) ──> table parametres (globaux)
Page Classes (override)    ──> table classes (par classe)

Bulletins_model::get_bulletin_complet
  ├─ _detecter_categories($id_classe)      ← interroge evaluations.type
  │    → mode_b (competance|ressource) / mode_a (examen) / défaut
  ├─ _get_notes_aggregated()               ← CASE WHEN par type + AS alias
  │    → map[id_etudiant][id_matiere][periode] = {tj, comp, ress, ex}
  ├─ _get_conduite_map()                   ← lit points_conduite par étudiant/période
  │    → map[id_etudiant][periode] = {points_initial, points_retires, points}
  ├─ _build_result()                       ← assemble eleves + notes + conduite
  ├─ _get_maxima()                         ← TJ, COMP%, RESS%, EX = TJ
  └─ Neutralisation selon le mode :
       Mode B → ex = 0  |  Mode A → comp=0, ress=0  |  Défaut → comp=0, ress=0, ex=0

API (api_bulletin_complet)
  ├─ Renvoie JSON: classe, periodes, matieres, eleves, maxima, mode flags
  └─ Rangs calculés côté PHP par moyenne décroissante

Vues (frontend JS)
  ├─ bulletins.php           ← interactif, chargé en AJAX depuis api/bulletins/complet
  │    ├─ cumulMode = trimestre choisi (cumul depuis T1) ou all
  │    ├─ cdVal() = points_conduite[periode].points (défaut 60)
  │    └─ Lignes: Matières → Conduite → Totaux → Pourcentage → Mention → Place → Religion → Signatures
  ├─ print_bulletins.php     ← impression bulletin (HTML statique)
  └─ print_fiches_v2.php     ← impression fiche de points
```

## 6. Report d'année & décision de passage (Clôture)

La clôture d'année (`Classes → Annees → Clôture & Report`) décide pour chaque étudiant sa classe de destination pour l'année suivante.

### 6.1. Règles automatiques

- **Seuil de moyenne (%)** (`seuil_moyenne`, défaut 50) : si la **moyenne annuelle du bulletin** est inférieure au seuil → **Ajourné**.
- **Seuil matière (%)** (`seuil_matiere`, défaut 50) : par matière, % = notes ÷ (note_max_matiere × 6) × 100. Une matière sous le seuil = **échec**.
- **Max de repêchage** (`max_repechage`, défaut 3) : nombre maximal d'échecs toléré.

**Décision :**
1. Moyenne < seuil → **Ajourné** (redouble).
2. Sinon, 0 échec → **Admis** (passe en classe supérieure).
3. Sinon, échecs ≤ Max de repêchage → **Repêchage**.
4. Sinon (plus de repêchage) → **Ajourné**.

### 6.2. Repêchage = décision MANUELLE

- Le **Repêchage n'est PAS promu automatiquement** : l'élève reste dans sa classe par défaut.
- Le passage en classe supérieure d'un élève en repêchage se fait **manuellement** par l'administrateur.

### 6.3. Sans bulletin = REDOUBLE toujours

- Un étudiant **sans bulletin** (inscrit mais aucune note saisie) est considéré **non en ordre** → il **redouble toujours**.
- Le paramètre « Comportement sans bulletin » a été **supprimé** : plus d'option « passer ».

### 6.4. Procédure dans l'application

1. Choix de l'année source et de l'année cible.
2. Bouton **« Voir l'aperçu des élèves »** → affiche un tableau avec la décision de chaque élève et un menu déroulant pour ajuster la **classe destination** (surtout pour les élèves en repêchage).
3. Ajustements manuels possibles pour chaque élève (rester, classe suivante, sortant).
4. Bouton **« Confirmer le report »** → applique la clôture et reporte les inscriptions selon l'aperçu ajusté.

## 7. Paramètres supprimés (nettoyage)

Ces paramètres ont été **supprimés de la base et du code** car obsolètes / redondants :

| Paramètre | Raison |
|---|---|
| `tj_points` | Ancien calcul du TJ (points fixes) — remplacé par `note_max_matiere`. |
| `examen_pourcentage` | Redondant avec les « à l'examen (%) ». |
| `points_par_heure` | Ancien facteur — TJ utilise désormais `note_max_matiere` directement. |
| `ressources_pourcentage` (global) | Doublon de « Ressources à l'examen (%) » — la surcharge par classe reste possible dans la page Classes. |
| `competences_pourcentage` (global) | Idem (doublon de « Compétences à l'examen (%) »). |
| `comportement_sans_bulletin` | Supprimé : sans bulletin → l'élève redouble toujours. |
| `nombre_trimestres` | Inutilisé : le nombre de périodes est déduit de la table `periodes`, pas d'un paramètre. |
| `prochain_num_recu` | Inutilisé : le numéro de reçu est saisi manuellement (`recus.numero_recu`, contrôle de doublon dans `Recu.php`). |
| `tva` | Inutilisé : aucun calcul de TVA n'existe dans l'application. |

La table `parametres` contient désormais **41 paramètres** (audit : tous utilisés, sauf `annee_active` qui est sauvegardé par la page mais dont l'année active réelle vient de `annees_scolaires.est_en_cours`).

## 8. Répertoire des paramètres (43)

> Où chaque paramètre est **consommé** (hors page Paramètres elle-même). Valeurs actuelles en base.

### 8.1. École & identité

| Clé | Valeur | Rôle | Utilisé dans |
|---|---|---|---|
| `nom_ecole` | FUTURE VIP SCHOOL | Nom de l'école : titres des pages d'authentification, en-têtes des e-mails et des reçus | `Cpanel_email.php:17,88,190` ; `Recu.php:125` ; `Header.php:18` ; vues Login/Register/Forgot/Reset/Verify (`:6`) |
| `adresse_ecole` | (vide) | Adresse affichée en en-tête du reçu | `Recu.php:126` |
| `telephone_ecole` | (vide) | Téléphone affiché en en-tête du reçu | `Recu.php:127` |
| `email_ecole` | admin@vip-school.com | Email général de l'école : expéditeur par défaut + reçu | `Cpanel_email.php:18` ; `Recu.php:128` |
| `logo_ecole` | assets/uploads/logo/...jpg | Logo : pages d'authentification, barre latérale, e-mails | `Cpanel_email.php:89` ; `Header.php:199` ; vues Login/Register/Forgot/Reset/Verify (`:23-24`) |
| `favicon_ecole` | assets/uploads/logo/...png | Favicon du navigateur | `Header.php:19` ; vues Login/Register/Forgot/Reset/Verify (`:7`) |
| `login_img` | *(non en base)* | Image de fond des pages d'authentification (insérée à la sauvegarde) | Vues Login/Register/Forgot/Reset/Verify (`:17`) |
| `devise` | BIF | Devise affichée sur les prix (produits) et reçus | `Footer.php:26` (var JS `DEVISE`) ; `Produits/views/index.php:109,113` ; `Recu.php:134` ; `Stock_Mouvements.php:128` |

### 8.2. E-mail (SMTP)

| Clé | Valeur | Rôle | Utilisé dans |
|---|---|---|---|
| `email_protocol` | mail | Protocole d'envoi (`mail` ou `smtp`) | `Cpanel_email.php:26` |
| `email_smtp_host` | (vide) | Hôte SMTP | `Cpanel_email.php:38` |
| `email_smtp_user` | admin@vip-school.com | Utilisateur SMTP (expéditeur par défaut) | `Cpanel_email.php:20,39` |
| `email_smtp_pass` | admin123 | Mot de passe SMTP | `Cpanel_email.php:40` |
| `email_smtp_port` | 587 | Port SMTP | `Cpanel_email.php:41` |
| `email_smtp_crypto` | tls | Chiffrement (`tls`/`ssl`) | `Cpanel_email.php:42` |
| `email_sendmail_path` | (vide) | Chemin sendmail si protocole `mail` | `Cpanel_email.php:44` |

### 8.3. Horaires (Emploi du temps)

#### Paramètres

| Clé | Défaut | Rôle | Utilisé dans |
|---|---|---|---|
| `heure_debut_journee` | 07:30 | Heure du premier créneau | `Horaires_model.php:57` |
| `duree_cours` | 45 | Durée (min) d'un cours | `Horaires_model.php:58` |
| `duree_pause` | 20 | Durée (min) de la pause (insérée au milieu) | `Horaires_model.php:59` |
| `duree_vigie` | 10 | Durée (min) de la vigie matinale (créneau spécial) | `Horaires_model.php:60` |
| `nb_creneaux_jour` | 8 | Nombre de créneaux cours par jour | `Horaires_model.php:61` |
| `duree_culte` | 35 | Durée (min) du culte (jour spécial uniquement) | `Horaires_model.php:123` |
| `jour_special` | mardi | Code du jour spécial (culte) | `Horaires.php:16` |
| `jour_special_actif` | 1 | Active/désactive l'affichage du jour spécial | `Horaires.php:17` |

#### Architecture

```
modules/Horaires/
├── controllers/Horaires.php   (287 lignes — index + CRUD + api_generer + generer)
├── models/Horaires_model.php  (332 lignes — requêtes + compute_creneaux + list_horaires)
├── libraries/HorairesGenerator.php (réécrit — préflight + pré-placement tight + générateur groupé + swap + brute force)
└── views/index.php            (491 lignes — grille + exports A4/Excel)
```

**Principe fondamental** : chaque génération effectue un `TRUNCATE` de la table `horaires` puis réécrit toutes les sessions. Pas de soft delete sur les anciennes sessions d'une génération.

**Routes API** (`config/routes.php:164-173`) :

| Route | Méthode | Rôle |
|---|---|---|
| `api/horaires` | `api_list` | Liste tous les horaires |
| `api/horaires/{uuid}` | `api_get` | Détail d'un horaire par UUID |
| `api/horaires/create` | `api_create` | Ajouter un horaire |
| `api/horaires/{uuid}/update` | `api_update` | Modifier un horaire |
| `api/horaires/{uuid}/delete` | `api_delete` | Suppression logique (soft delete) |
| `api/horaires/generer` | `api_generer` | Générer l'emploi du temps (algorithme complet) |
| `api/horaires/generations` | `api_generations` | Lister les générations |
| `api/horaires/matieres/{id}` | `api_matieres_by_classe` | Matières d'une classe |
| `api/horaires/enseignant/{id}/{id}` | `api_enseignant_by_classe_matiere` | Enseignant par classe/matière |

#### Schéma BDD (3 tables)

**`horaires`** — table principale

| Colonne | Type | Rôle |
|---|---|---|
| `id_horaire` | int PK AUTO | Identifiant |
| `uuid` | char(36) UNIQUE | UUID public |
| `id_generation` | int FK → `horaires_generations` | Génération (CASCADE) |
| `id_enseignement` | int FK → `enseignements` | Lien enseignement |
| `id_matiere` | int FK → `matieres` (SET NULL) | Matière |
| `id_enseignant` | int FK → `enseignants` | Enseignant |
| `id_classe` | int FK → `classes` | Classe |
| `id_creneau` | int/string | Créneau (1,2,3… / `vigile` / `pause3` / `culte`) |
| `id_jour` | int FK → `jours_semaine` | Jour de la semaine |
| `deleted_at` | datetime nullable | Soft delete |

**UNIQUE** : `(id_generation, id_creneau, id_jour, id_enseignant)` — un enseignant = 1 cours/créneau/jour.
**UNIQUE** : `(id_generation, id_creneau, id_jour, id_classe)` — une classe = 1 cours/créneau/jour.

**`horaires_generations`** — suivi des générations

| Colonne | Type | Rôle |
|---|---|---|
| `id_generation` | int PK AUTO | Identifiant |
| `uuid` | char(36) UNIQUE | UUID public |
| `libelle` | varchar(100) | Nom (ex: "Emploi du temps 2026") |
| `id_annee` | int FK → `annees_scolaires` | Année scolaire (CASCADE) |
| `date_generation` | datetime | Date de création |
| `statut` | enum(brouillon,publie,archive) | État |

**`contraintes_horaires`** — contraintes de planification

| Colonne | Type | Rôle |
|---|---|---|
| `id_contrainte` | int PK AUTO | Identifiant |
| `type` | enum(matiere,classe,enseignant,global) | Portée de la contrainte |
| `id_concerne` | int nullable | ID matière/classe/enseignant (NULL = global) |
| `id_jour` | int FK → `jours_semaine` | Jour concerné |
| `id_creneau_debut` / `id_creneau_fin` | int nullable | Plage de créneaux |
| `regle` | varchar(50) | `interdit` / `preferer_matin` / `max_consecutifs` / `seulement_creneau` |
| `valeur` | varchar(255) | Valeur associée à la règle |

#### Types de créneaux

Les créneaux sont **calculés dynamiquement** depuis les paramètres (pas de table `creneaux`). Types possibles :

| Type | ID | Description |
|---|---|---|
| `cours` | integer (1, 2, 3…) | Créneaux normaux |
| `vigile` | `vigile` | Salut du drapeau (premier créneau, optionnel si `duree_vigie > 0`) |
| `pause` | `pause{i}` | Pause/récréation (insérée au milieu, après créneau `nb_creneaux/2`) |
| `culte` | `culte` | Culte du mardi (durée `duree_culte`, remplace la pause standard) |

#### Algorithme de génération (`api_generer`)

Le générateur utilise un **verrou MySQL** (`GET_LOCK`) pour éviter les exécutions concurrentes.

**Entrées** : classes, matieres_classes, enseignements, jours (filtrés `deleted_at IS NULL`), créneaux (cours uniquement — vigile/pause/culte = affichage), indisponibilités.

##### Preflight — Validation avant écriture

Le preflight classe les diagnostics en deux catégories :

| Type | Bloquant ? | Exemples |
|---|---|---|
| **Blocking** (bloque la génération) | Oui | Affectation manquante, capacité dépassée (charge > capacité), jours insuffisants, conflit de fixe, fixe hors limites |
| **Warning** (information uniquement) | Non | Marge zéro (enseignant tight) |

Seuls les diagnostics `blocking => true` empêchent la génération. Un enseignant à marge zéro n'est **pas** une erreur — c'est un cas valide géré par le pré-placement tight.

##### Processus

| Étape | Stratégie | Description |
|---|---|---|
| **Preflight** | Validation complète | Vérifie : affectations manquantes, capacité enseignant, marge zéro, jours insuffisants par cours, limites quotidiennes, créneaux fixes (disponibilité + conflits + limites). Blocage immédiat si violation. |
| **Construction** | Map MC→Ens | Construit `$mapMC2Ens` : chaque `id_matiere_classe` → `{id_enseignement, id_enseignant}` depuis `enseignements` |
| **Sessions** | Expansion | Chaque `matieres_classes` avec `nb_heures_par_semaine > 0` génère N sessions (une par heure/semaine) |
| **Filtrage fixe** | Déduction | Retire les sessions auto correspondant aux créneaux fixes |
| **Pré-placement tight** | TightTeacherPre | Détecte les enseignants dont total >= available - 1 (marge 0 ou 1). Triage par marge croissante. Sessions converties en fixe. |
| **Groupement** | Par (classe, matière) | Sessions regroupées par `id_classe` + `id_matiere_classe` pour traitement groupé |
| **3 ordonnancements** | Groupes contraints | Teste 3 ordres : (1) plus de sessions d'abord, (2) plus contraints d'abord, (3) par charge enseignant |
| **Placement groupé** | PlaceGroupConsecutive | Pour chaque groupe : place les sessions sur le même jour avec des créneaux consécutifs (C1→C2→C3). |
| **Swap** | Déplacement (depth 3) | Si cellule ou prof bloqué → déplace la session existante vers un autre slot libre |
| **Rattrapage** | Individuel | Sessions non encore placées → itère jour×créneau |
| **Brute force** | Relocalisation (depth 6) | Dernier recours : retire des sessions pour libérer des slots |
| **Optimisation** | ConsecutivePost | Réorganiser les sessions d'une même matière sur un jour pour les rendre consécutives |
| **Validation** | Complète | Vérifie unicité classe/créneau/jour, unicité prof/créneau/jour, limites quotidiennes, disponibilités, 0 cases vides |
| **Insertion** | TRUNCATE + Batch | TRUNCATE la table `horaires` (hors transaction), puis INSERT batch dans une transaction MySQL |

**Priorité succession** : les cours multi-heures (ex: FRA TECH 3h) sont placés de préférence sur le même jour avec des créneaux consécutifs (ex: C4→C5→C6). Ceci est une **préférence**, pas une obligation — si le placement consécutif bloque, le cours est placé librement. Pendant les **swaps**, la contrainte de succession n'est **pas appliquée** (priorité au placement complet).

**Grille finale** : chaque classe × chaque jour × chaque créneau = **exactement 1 session** (aucune case vide).

**Contraintes respectées** : indisponibilités enseignants (`disponibilites_enseignants`), unicité classe/créneau/jour, unicité enseignant/créneau/jour, `nb_heures_par_jour` (maximum de séances d'une matière par jour).

**Insertion** : TRUNCATE de la table `horaires` avant la transaction (évite le COMMIT implicite de TRUNCATE dans une transaction). Puis INSERT batch de toutes les sessions. Statut = `brouillon`.

**Jours** : seuls les jours avec `deleted_at IS NULL` sont utilisés (samedi supprimé = 5 jours × 8 créneaux = 40 slots/classe).

**Retry** : si les ordres déterministes ne donnent pas 100%, jusqu'à **50 tentatives aléatoires** sont effectuées. Si toujours pas 100%, des **20 passes de rattrapage par swap** puis **5 rounds de brute force** (depth 6) sont lancés.

#### Enseignants tight (marge nulle)

Un enseignant est dit « tight » quand le nombre total de sessions (toutes classes confondues) est **>= au nombre de créneaux disponibles**. Exemple : un enseignant disponible 2 jours (8 créneaux/jour) avec 16h de cours/semaine est tight (marge = 0).

**Règles** :
- **charge > capacité** → blocage immédiat avant génération
- **charge = capacité** (marge 0) → tous les créneaux autorisés DOIVENT être occupés
- **charge = capacité - 1** (marge 1) → pré-placement tight activé

**Traitement** :
1. **Détection** : calcule pour chaque enseignant le ratio `total / available`.
2. **Triage** : enseignants à marge 0 d'abord, puis marge 1.
3. **Pré-placement** : les sessions tight sont converties en sessions « fixe » et placées en priorité sur tous les créneaux disponibles de l'enseignant, en respectant `nb_heures_par_jour` par matière/classe.
4. **Validation preflight** : vérifie que chaque cours d'un enseignant tight a suffisamment de jours disponibles.

**Exemple concret** : enseignant disponible lundi et jeudi (2 × 8 = 16 créneaux), enseigne 3 classes (6h + 5h + 5h = 16h/semaine). Les 16 créneaux seront tous occupés. Quand il quitte une classe à un créneau, il entre immédiatement dans une autre.

#### Résultat typique de génération (vérifié en base)

| Métrique | Valeur attendue |
|---|---|
| Total sessions | Exactement `sum(nb_heures_par_semaine)` pour toutes les matières_classes |
| Conflits enseignant | 0 — un enseignant = 1 cours/créneau/jour |
| Conflits classe | 0 — une classe = 1 cours/créneau/jour |
| Heures/semaine par cours | Exactement `nb_heures_par_semaine` pour chaque matiere_classe |
| Limites quotidiennes | 0 violation — chaque cours respecte `nb_heures_par_jour` |
| Sessions non placées | 0 — sinon la génération est refusée |

#### Jour spécial (mardi)

Quand `jour_special_actif = 1`, la vue affiche **deux grilles par classe** :
- **Grille principale** : tous les jours **sauf** le jour spécial (créneaux standard via `get_creneaux_cours()`)
- **Grille secondaire** : jour spécial uniquement (créneaux avec culte via `get_creneaux_mardi()`)

Le mardi a un **ensemble de créneaux différent** : le créneau `culte` remplace la pause standard, avec sa propre durée (`duree_culte`).

#### Exports

- **A4** : impression via `window.open()` avec CSS `@media print`, 4 classes par page, Police Times New Roman
- **Excel** : via SheetJS (`xlsx.full.min.js`), un onglet par classe

### 8.4. Notation & bulletin

| Clé | Valeur | Rôle | Utilisé dans |
|---|---|---|---|
| `facteur_points_heure` | 15 | Non utilisé — TJ = `note_max_matiere` directement | (obsolète, conservé pour compatibilité) |
| `pourcentage_ressources_examen` | 60.00 | % du TJ alloué aux Ressources (MAX RESS = TJ × %/100) | `Bulletins_model.php:84,124` ; `Bulletins.php:463` ; `Fiches.php:115` |
| `pourcentage_competences_examen` | 40 | % du TJ alloué aux Compétences (MAX COMP = TJ × %/100) | `Bulletins_model.php:87,123` ; `Bulletins.php:466` ; `Fiches.php:114` |

> **Note** : `ressources_active` et `competences_active` ne sont **plus des paramètres globaux** — ils sont gérés par classe dans la table `classes`.

### 8.5. Mentions (seuils dynamiques)

Seuils en % de la note de référence (`moyenne / sur × 100`) + libellés personnalisables. Clés construites dynamiquement : `$cle` et `$cle . '_libelle'`.

| Clé | Valeur | Rôle | Utilisé dans |
|---|---|---|---|
| `mention_excellent` / `_libelle` | 90 / Excellent | Mention ≥ 90 % | `settings_helper.php:41,49,58,63` |
| `mention_tres_bien` / `_libelle` | 80 / Très Bien | Mention ≥ 80 % | `settings_helper.php:42,50,58,63` |
| `mention_bien` / `_libelle` | 70 / Bien | Mention ≥ 70 % | `settings_helper.php:43,51,58,63` |
| `mention_assez_bien` / `_libelle` | 60 / Assez Bien | Mention ≥ 60 % | `settings_helper.php:44,52,58,63` |
| `mention_passable` / `_libelle` | 40 / Passable | Mention ≥ 40 % | `settings_helper.php:45,53,58,63` |
| `mention_insuffisant` / `_libelle` | 0 / Insuffisant | Mention < 40 % (repli) | `settings_helper.php:46,54,58,63,66` |

### 8.6. Report d'année (clôture)

| Clé | Valeur | Rôle | Utilisé dans |
|---|---|---|---|
| `seuil_moyenne` | 50 | Seuil (%) de moyenne annuelle : en dessous → Ajourné | `Annees.php:163` |
| `seuil_matiere` | 50 | Seuil (%) par matière : % = notes ÷ (note_max_matiere × 6) × 100 ; une matière sous le seuil = échec | `Annees.php:164` |
| `max_repechage` | 3 | Nombre maximal d'échecs toléré avant ajournement | `Annees.php:165` |

### 8.7. Conduite & divers

| Clé | Valeur | Rôle | Utilisé dans |
|---|---|---|---|
| `points_conduite_defaut` | 60 | Points de conduite initiaux d'un élève | `Conduite_model.php:62` |
| `echelle_notes` | 100 | Échelle des notes (sur /20, /100…) pour les stats du tableau de bord | `Dashboard.php:32,34` |
| `periode_active` | 1 | Période active en secours (fallback si aucune `periodes.est_en_cours`) | `MY_Controller.php:120` |
| `annee_active` | 1 | Sélection de l'année sur la page Paramètres (affichage) — l'année active réelle = `annees_scolaires.est_en_cours` (bouton « Activer » via `API.annees.setActive`) ; le paramètre n'est pas relu par le code | `Parametres/views/index.php` (select + `activerAnnee`) |

## 9. Fichiers principaux

- `application/modules/Parametres/views/index.php` — page Paramètres (saisie, synchronisation 100 %, garde-fous).
- `application/modules/Parametres/controllers/Parametres.php` — sauvegarde (whitelist des clés autorisées).
- `application/modules/Notes/models/Bulletins_model.php` — `_detecter_categories` (détection dynamique), `_get_maxima` (calcul TJ / COMP / RESS), `_get_notes_aggregated` (CASE WHEN avec aliases SQL), `_get_conduite_map` (points_conduite), `_build_result` (assemblage), `get_bulletin_complet` (flags + neutralisation 3 modes).
- `application/modules/Notes/controllers/Bulletins.php` — `api_bulletin_complet` (JSON), `api_periodes` (AJAX par année), `export_bulletins_classe` (print).
- `application/modules/Notes/controllers/Fiches.php` — `api_fiche_par_cours`, `export_fiche_classe` (print fiche).
- `application/modules/Notes/views/bulletins.php` — bulletin interactif (JS, cumulMode, conduite, 3 modes d'affichage).
- `application/modules/Notes/views/print_bulletins.php` — impression bulletin (thead 3 lignes Mode B, Religion avec relComp/relRess).
- `application/modules/Notes/views/fiches.php` — fiche de points interactive.
- `application/modules/Notes/views/print_fiches_v2.php` — impression fiche de points (vue active).
- `application/modules/Notes/views/print_fiches.php` — **code mort** (non chargé, non modifié).
- `application/modules/Classes/controllers/Annees.php` — `_calculer_report` (décision de passage), `api_apercu_cloture`, `api_cloturer` (report avec override manuel).
- `application/modules/Classes/views/annees.php` — page Années + modal Clôture & Report (aperçu + ajustement manuel par élève).
- `application/config/routes.php` — routes API (`api/bulletins/periodes/{id_annee}`, `api/bulletins/complet/{id}`).

## 10. Journal des modifications

### Session notation (à jour)
- Calcul du TJ corrigé : TJ = heures × facteur (coefficient calculé), plus de TJ fixe.
- Pourcentages RESS/COMP dynamiques depuis la base, somme = 100 %.
- Activation/désactivation des catégories avec garde-fou et auto-ajustement 0/100.
- Affichage TJ/RESS/COMP ou TJ/EX selon les catégories actives (bulletin + fiches + impressions).
- `table-layout: fixed` sur toutes les tables pour des colonnes égales.
- Lignes Religion / Signatures / TITULAIRE adaptées au colspan.
- Suppression des paramètres redondants (tj_points, examen_pourcentage, points_par_heure, ressources_pourcentage/competences_pourcentage globaux).
- **Report d'année** : le passage des élèves en REPÊCHAGE est désormais une décision **manuelle** (aperçu avec ajustement par élève) — plus de promotion automatique des repêchages.
- **Sans bulletin** : l'élève sans bulletin **redouble toujours** (paramètre « Comportement sans bulletin » supprimé).
- **nombre_trimestres supprimé** (inutilisé — le nombre de périodes vient de la table `periodes`) : page Paramètres, whitelist, base ; parametres = 45.
- **prochain_num_recu et tva supprimés** (orphelins — jamais lus par le code) : n° de reçu saisi manuellement, aucun calcul de TVA. **annee_active conservé** (sélection de la page, l'activation réelle passe par `est_en_cours`). parametres = 43.
- **`ressources_active` et `competences_active` supprimés** (valeurs gérées par classe dans `classes`, pas de paramètre global). parametres = 41.
- **Audit complet des paramètres** : chaque clé vérifiée — répertoire complet ajouté en **section 8** (rôle + lieu d'utilisation fichier:ligne pour chacun).
- **Corruption `?` corrigée en base** : les mots contenant des accents étaient stockés avec des `?` littéraux (0x3F) — `classes.libelle` (1ère PEDAGOGIQUE), `menus.libelle` (Scolarité, Reçus, Échéanciers, Paramètres, Disponibilités, Générer), `produits.unite` (pièce). Vérifié par scan binaire (`LIKE '%?%' COLLATE utf8mb4_bin`) sur les 154 colonnes texte.
- **`evaluations.sur` → `evaluations.ponderee_sur`** (renommée) : barème de chaque évaluation (défaut 20). Grille de notes, fiches et bulletins utilisent `note / ponderee_sur` pour normaliser.
- **`matieres_classes.coefficient` → `matieres_classes.note_max_matiere`** (renommée) : « note max matière » (poids de la matière pour la classe, défaut 1.0). Utilisée comme TJ dans `_get_maxima()`, affichée dans la grille de notes, les programmes et les horaires. L'API et les vues des enseignants utilisent désormais `note_max_matiere`.
- **Coefficient supprimé de la table `evaluations`** : le formulaire et l'API d'évaluation n'acceptent que `ponderee_sur` ; la colonne « Coeff. » a été retirée des tableaux d'évaluations.
- **Types d'évaluation alignés sur l'enum** `('interrogation','devoir','ressource','competance','examen')` : listes déroulantes (ajout/modification) mises à jour dans les modules Notes et Evaluations.
- **`api_grille_notes` corrigé** : le select des évaluations ne contenait pas `ev.id_matiere` → warning « Undefined array key » → en-têtes déjà envoyés → réponse HTML au lieu de JSON (« Unexpected token '<' »). `ev.id_matiere` ajouté au select ; `api.js` protégé contre les réponses non-JSON (message d'erreur propre au lieu de `response.json()` qui plante).
- **Grille de notes (page Notes)** : en-tête des colonnes simplifié → seul le barème est affiché (`/15`), le `×note_max_matiere` est retiré de l'affichage (et du payload `api_grille_notes`) ; la molette de la souris ne modifie plus la note saisie (`onwheel preventDefault` sur les champs) ; colonne **Moyenne retirée** (en-tête, cellule élève, pied de tableau et calculs `computeStats`/`recalcRow`) — restent Total et % ; **scroll vertical du tableau supprimé** (plus de `max-height:65vh`) → tout s'affiche ; **filtre Trimestre** : seuls les trimestres de l'année sélectionnée s'affichent (au chargement = année active, mise à jour au changement d'année avec auto-sélection du premier trimestre visible).
- **Molette souris bloquée sur TOUS les champs numériques** (`Footer.php`, garde global `wheel` + `preventDefault` sur `input[type=number]`) : plus de changement de valeur accidentel en scrollant (notes, montants, paramètres…).
- **Suppression d'évaluation en cascade** : supprimer une évaluation efface aussi (soft delete) toutes les notes attribuées aux élèves — avec **confirmation** : l'utilisateur est averti du nombre de notes concernées (module Evaluations, `api_get` renvoie `note_count` ; module Notes, message dans la modale). Réponse : `notes_supprimees`.
- **Fiches de points — fiche élève par cours** (page Fiches) : quand un cours est sélectionné dans le filtre, la fiche garde **exactement le design de la fiche classe** (mêmes classes CSS, en-tête 2 lignes avec blocs par période, pied gris TOTAUX ÉLÈVES). Colonnes : 1 = **N°** (numérotation), 2 = **ÉLÈVES** (tous les élèves de la classe, ordre alphabétique), puis **4 colonnes par période** : **TJ** = somme des notes de type interrogation + devoir, **COMP** = somme type competance, **RESS** = somme type ressource, **TOT** = TJ+COMP+RESS (notes lues dans `notes` en respectant le type de chaque évaluation), puis **T.A** (total annuel = somme des TOT des trimestres affichés) et **%** (= T.A ÷ somme des barèmes `ponderee_sur` × 100). En-tête sur **3 lignes** : par trimestre → **TJ | EXAMEN | TOT** avec **COMP | RESS** sous EXAMEN. Pas de ligne MAXIMA — sauf **ligne BARÈME** : une ligne grise **MAXIMA** avant les lignes élèves, avec par trimestre le barème TJ (= somme `ponderee_sur` interro+devoir), COMP, RESS, TOT et T.A (= somme des barèmes), % = 100%. `api_fiche` accepte `matiere=` et `periode=all` (toute l'année, évaluations groupées par période) et renvoie `classe`/`annee_scolaire`/`periode_libelle`. Sans cours sélectionné, la fiche classe (MAXIMA/TJ/RESS/COMP) reste inchangée.
- **Bulletins** : en-tête des bulletins mis au même format que la fiche élève — 3 lignes : par trimestre → **TJ | EXAMEN | TOT** avec **COMP | RESS** sous EXAMEN (quand RESS et COMP sont actifs) ; ordre des cellules = TJ, COMP, RESS, TOT. Si seul EX est actif, en-tête 2 lignes TJ/EX/TOT conservé.
- **Export fiche élève** (`Notes/Fiches/export/{classe}?periode=&matiere=`) : quand `matiere` est passé, l'export rend la **même fiche élève que l'écran** (`print_fiche_eleve.php`) — N° | ÉLÈVES | TJ/EXAMEN(COMP|RESS)/TOT par trimestre | T.A | %, ligne de barème grise, colonne TOT grise. Sans `matiere`, l'ancienne fiche classe (`print_fiches_v2.php`) reste utilisée. `_export_fiche_eleve` construit les données (élèves, évaluations groupées par période, notes) dans le contrôleur.
- **Cumul des périodes (fiche élève + export)** : les 3 blocs trimestres sont **toujours affichés** (en-têtes complets). Trimestre N sélectionné → les colonnes des trimestres **1 à N** contiennent les valeurs (T.A et % calculés sur ces périodes seulement) ; les trimestres **suivants** sont affichés mais **vides** (cellules sans valeur, barème vide). « Tous » → valeurs sur les 3 trimestres. `api_fiche`/`_export_fiche_eleve` renvoient toujours toutes les évaluations de l'année ; la vue calcule `dataIdx` (index de la période sélectionnée) et vide les colonnes au-delà.
- **Bulletins — correction mapping types** : `Bulletins_model::_get_notes_aggregated` utilisait des types inexistants (`'composition'`, `'tp'`) → les notes compétence/ressource n'étaient jamais comptées (RESS = 0). Corrigé : **TJ = interrogation + devoir, COMP = competance, RESS = ressource** (même mapping que la fiche élève). Les notes de type `examen` ne sont pas affichées (ni fiche ni bulletin).
- **Bulletins — robustesse agrégation notes** : `Bulletins_model::_get_notes_aggregated` a été simplifié pour sommer **toutes les notes** de la table `notes` par période pour chaque matière (sans filtre strict sur le type d'évaluation), garantissant que toute note enregistrée s'affiche bien sur le bulletin.
- **Emploi du temps (Horaires)** : les jours de la semaine s'affichent de gauche à droite dans l'ordre chronologique (**Lundi, Mardi, Mercredi...**). La pause s'insère automatiquement au milieu.
- **Succession consécutive multi-heures** : les cours multi-heures (ex: FRA TECH 3h) sont placés sur le même jour avec des créneaux consécutifs (C4→C5→C6). Préférence (pas obligation) — 95.1% de taux consécutif. Swap sans contrainte de succession.
- **Documentation Horaires étoffée** : section 8.3 complétée avec les 8 paramètres (ajout `duree_culte`, `jour_special`, `jour_special_actif`), architecture (3 fichiers, 9 routes API), schéma BDD (3 tables avec colonnes et contraintes UNIQUE), algorithme de génération (5 passes + CRE), types de créneaux, fonctionnement du jour spécial (mardi = grille séparée avec culte), et exports A4/Excel.
- **Audit & corrections Horaires (12 fixes)** :
  - `_cloneSchedule()` : deep copy corrigée (shallow copy → copie indépendante des entrées grille)
  - `_searchChain()` : bug backtracking corrigé (suppression `unset($visited)` qui permettait de revisiter les états → boucles infinies possibles)
  - Pass 5 : requête DB dans boucle remplacée par un preload batch (1 requête au lieu de N×M)
  - Pass 5 : exclusion du prof cible ajoutée dans les candidats substituts
  - `$pass3cPlacees` : variable inutilisée supprimée (init + rapport)
  - `insert_horaires_batch()` : UUID inline remplacé par `generate_uuid()` helper
  - `Disponibilites/views/index.php` : double inclusion `Footer.php` corrigée
  - `Disponibilites_model` : code mort supprimé (méthodes `get_all`, `create_record`, `delete_record` inutilisées)
  - `Enseignants/views/timetable.php` : requête SQL + model load déplacés dans le controller, vue nettoyée
  - `Horaires_model` : ajout `get_horaires_by_enseignant()` pour centraliser la logique
  - SheetJS : CDN externe remplacé par copie locale (`assets/vendor/xlsx.full.min.js`)
  - `api.js` : objet `creneaux` orphelin supprimé (routes commentées)

### Session horaires — correction bug placement multi-heures

- **Bug critique Pass 4/5 corrigé** : les passes 4 (intersection, swap intelligent) et 5 (substitution, déplacement) ne plaçaient qu'**1 seule séance** d'un bloc multi-heures puis marcaient `placed = true` sur tout le bloc. 14 heures étaient perdues silencieusement (226/240 au lieu de 240/240).
- **Fix** : ajout d'une fonction `$placerResteBloc()` qui, après le 1er placement, cherche les créneaux restants en essayant : (1) consécutifs sur le même jour, (2) isolés sur le même jour, (3) consécutifs puis isolés sur les autres jours. Les 4 points de bug (intersection, swap, substitution, déplacement) appellent maintenant `$placerResteBloc()` et ne marquent `placed = true` que si `$reste === 0`.
- **Validation corrigée** : le compteur `seances_placees` comptait les **blocs** marqués placed, pas les **séances réelles** dans la grille. Remplacé par un comptage direct `count($grille)` et une comparaison MC attendu vs placé.
- **Résultat test** : 239/240 séances, **0 conflits prof**, **0 conflits classe**, 9 blocs dégradés, 6 violations de succession. La séance manquante (IG I BUREAUTIQUE) est un **blocage physique** : le seul créneau libre (Lundi 1) est occupé par le même prof qui enseigne BASE DE DONNEES en IG II.
- **Fichiers modifiés** : `application/modules/Horaires/controllers/Horaires.php` (lignes ~535-600 : `$placerResteBloc`, lignes ~970-1000 : fix intersection, ~1060-1070 : fix swap, ~1275-1285 : fix substitution, ~1375-1385 : fix déplacement, ~1480-1510 : validation).

### Session horaires — déplacement inter-classes (240/240)

- **Problème** : quand un prof enseigne dans 2 classes (ex: ARAKAZA Arcade = BUREAUTIQUE IG I + BASE DE DONNEES IG II), le système pouvait le bloquer sur le seul créneau libre d'une classe.
- **Solution** : ajout de `$deplacementInterClasse()` — quand le prof est bloqué sur le seul créneau libre, le système décale son cours dans l'autre classe vers un autre créneau, puis place le cours courant au créneau libéré.
- **Résultat final** : **240/240 séances**, 0 conflits prof, 0 conflits classe, 10 placements forcés (dont 4 déplacements inter-classes). Toutes les 6 classes (BA I-III, IG I-III) ont exactement 40 créneaux remplis.
- **Fichier modifié** : `application/modules/Horaires/controllers/Horaires.php` — ajout `$deplacementInterClasse` (~100 lignes), intégré dans `$placerResteBloc` comme étape 3.

### Session bulletins — correction critique et dynamisme

- **Bug critique corrigé : notes à 0 dans le frontend** — `_get_notes_aggregated()` utilisait `implode(', ', $cases)` qui **supprimait les aliases SQL** (`AS note_tj_X`), les colonnes retourées par MySQL avaient des noms d'expression SQL au lieu de `note_tj_X` → tous les `isset($r['note_tj_X'])` retournaient `false` → fallback à 0. Corrigé en ajoutant `AS \`{$alias}\`` à chaque expression CASE WHEN. Le print (`export_bulletins_classe`) n'était pas affecté car il utilise des aliases hardcodés (`AS note_t1_tj`).
- **Détection dynamique des catégories d'évaluation** — `_detecter_categories($id_classe)` interroge la table `evaluations` pour déterminer les types réellement présents (`competance`, `ressource`, `examen`). Trois modes d'affichage :
  - **Mode B** (competance OU ressource présent) : TJ / COMP / RESS / TOT — TJ = interrogation + devoir uniquement, EX neutralisé (remis à 0).
  - **Mode A** (examen présent, sans competance/ressource) : TJ / EX / TOT — COMP et RESS neutralisés.
  - **Défaut** (ni competance, ni ressource, ni examen) : TJ / TOT — COMP, RESS et EX neutralisés.
- **EX max = TJ max** : la règle `'ex' => $tj` dans `_get_maxima()` est conservée (l'utilisateur a confirmé).
- **Neutralisation en cascade** : `get_bulletin_complet()` et `_build_result()` neutralisent les colonnes non utilisées selon le mode détecté (pour chaque matière, chaque période, et les maxima).
- **Ordre de binding SQL corrigé** — dans `_get_notes_aggregated` et `export_bulletins_classe`, les placeholders de période viennent **AVANT** les IDs étudiants/matieres (pour éviter les erreurs de binding CI3 avec 35+ paramètres).
- **Plafond validation corrigée** (4 contrôleurs : `Notes/Bulletins`, `Notes/Evaluations`, `Evaluations/Evaluations`) — vérification `note_max_matiere !== null && floatval(...) > 0` au lieu de simple `> 0`, pour éviter les erreurs sur les matières sans plafond défini.
- **`examen_active` ajouté** aux réponses `api_fiche()` (Fiches.php) et `export_bulletins_classe()` (Bulletins.php) — renvoyé au frontend pour le mode d'affichage.
- **Print view thead corrigé** — en-tête 3 lignes pour Mode B avec `rowspan=2` sur TJ/TOT, cellules COMP/RESS dans la bonne colonne sous EXAMEN, colonnes TOTAUX ANNUELS sur 2 lignes. Ligne Religion avec `$relComp`/`$relRess` pour Mode B.
- **`$data['periodes']` ajouté** au contrôleur `export_bulletins_classe()` — correction de l'erreur `Undefined variable $periodes` dans `print_bulletins.php:56`.
- **Ligne Sous-Tot supprimée** de `bulletins.php` — le bulletin passe directement de Matières à Conduite à Totaux.
- **Select trimestre filtré par année active** — le contrôleur `Bulletins::index()` ne charge que les périodes de l'année active. Nouvel endpoint `api/bulletins/periodes/{id_annee}` permet de recharger les périodes dynamiquement au changement d'année (AJAX).
- **Logging diagnostique ajouté** à `bulletins.php` (`openBulletinPeriode` et `renderBulletins`) — console.log détaillés avec try/catch, status HTTP, structure des données, pour faciliter le debug.
- **Paramètres `ressources_active` et `competences_active` supprimés** de la table `parametres` et de la page Paramètres — les valeurs sont désormais gérées **uniquement par classe** dans la table `classes` (pas de paramètre global). Whitelist, UI et JS nettoyés. parametres = 41.
- Dump `DB/vip_school.sql` régénéré après chaque changement de base.

### Session horaires — reconstruction complète du générateur (sept. 2026)

**Problème** : le module Horaires ne fonctionnait pas (page inaccessible, API 500, génération incomplète, cases vides dans la grille).

**12 corrections appliquées** :

| # | Correction | Détail |
|---|---|---|
| 1 | **Controller : index() manquante** | Méthode `index()` absente → page `/Horaires` inaccessible. Ajout avec toutes les variables PHP requises par la vue. |
| 2 | **Controller : CRUD manquant** | `api_list`, `api_get`, `api_create`, `api_update`, `api_delete` absentes (routes définies mais méthodes manquantes). |
| 3 | **Controller : FK `id_annee`** | INSERT `horaires_generations` échouait (FK `id_annee` manquante). Ajoutée dans `create_generation_record()`. |
| 4 | **Controller : soft-delete avant régénération** | Anciens horaires non supprimés → duplication entre générations. Ajout de soft-delete (`deleted_at = NOW()`). |
| 5 | **Controller : réponse API** | Vue attend `r.data.created` mais contrôleur renvoyait `r.data.validation` → "undefined créneaux créés". Format corrigé. |
| 6 | **Model : compute_creneaux()** | Ne générait que des entiers sans horaires. Reconstruit : vigile, cours 1-N avec `heure_debut`/`heure_fin`/`libelle`, pause au milieu. |
| 7 | **Model : payload générateur** | `get_generation_payload()` passait vigile/pause au générateur → `Duplicate entry` UNIQUE. Filtré : type `cours` uniquement. |
| 8 | **Model : méthodes manquantes** | `get_creneaux_cours()`, `get_creneaux_mardi()`, `list_horaires()`, `get_latest_generation()` absentes. |
| 9 | **Generator : id_enseignant** | Lisait `id_enseignant` depuis `matieres_classes` (NULL) au lieu de `enseignements`. `mapMC2Ens` reconstruit. |
| 10 | **Jours déletés** | Requête `jours_semaine` ne filtrait pas `deleted_at` → samedi inclus (soft-delete) → 6×8=48 slots, 40h → 8 cases vides. |
| 11 | **Algorithme greedy** | Remplissait lundi→vendredi, bloquait sur les derniers créneaux (prof en conflit). Nouvel algo : **5 stratégies de tri** + **swap simple** + **swap prof**. |
| 12 | **api_generer() manquante** | Point d'entrée API pour la génération absent. |

**Jours** : samedi supprimé (soft-delete, `actif=0`). Seuls 5 jours actifs : lundi→vendredi. Grille = 5 jours × 8 créneaux = **40 slots/classe**.

**Résultat** (données réelles de la base) :

| Métrique | Valeur |
|---|---|
| Sessions totales | **240/240 (100%)** |
| Cases vides | **0** |
| Conflits prof | **0** |
| Conflits classe | **0** |
| Créneaux remplis | **100%** (30/30 classes × 8 créneaux) |

**Fichiers modifiés** :
- `application/modules/Horaires/controllers/Horaires.php` : reconstruit (index, CRUD, generer, api_generer, soft-delete, FK, réponse API)
- `application/modules/Horaires/models/Horaires_model.php` : reconstruit (compute_creneaux, get_generation_payload, list_horaires, get_creneaux_cours/mardi, filtre jours)
- `application/modules/Horaires/libraries/HorairesGenerator.php` : reconstruit (mapMC2Ens, multi-stratégies, swap simple, swap prof)

### Session horaires — placement consécutif multi-heures (sept. 2026)

**Objectif** : quand un cours a plusieurs heures par semaine (ex: FRA TECH = 3h), placer ses sessions sur le même jour avec des créneaux consécutifs (C4→C5→C6) pour faciliter l'emploi du temps. Ceci est une **préférence** — pas une obligation — et n'est **pas appliqué** lors des swaps.

**Nouvel algorithme** :

| Étape | Description |
|---|---|
| **Groupement** | Sessions regroupées par (classe, matière) |
| **Placement groupé consécutif** | Pour chaque groupe : place toutes les sessions sur le même jour avec créneaux consécutifs (longest consecutive run). Si le jour est plein → jour suivant. Si des sessions restent → placement libre individuel. |
| **Swap** | Swap classique (déplacement direct + déplacement prof), sans contrainte de succession |
| **Optimisation post-placement** | Max 200 itérations : tente de réorganiser les sessions d'une même matière sur un jour pour les rendre consécutives (swap multi-étapes) |
| **Retry** | Si les ordres déterministes ne donnent pas 100%, jusqu'à 100 tentatives aléatoires |

**3 ordonnancements de groupes testés** :
1. Plus de sessions d'abord (matières à forte charge)
2. Plus contraints d'abord (ratio sessions / flexibilité prof)
3. Ordre original

**Résultat** :

| Métrique | Avant | Après |
|---|---|---|
| Placement | 240/240 | 240/240 |
| Taux consécutif | 16% | **95.1%** |
| Groupes scatter | 31/37 | **4/82** |

**Fichier modifié** : `application/modules/Horaires/libraries/HorairesGenerator.php` — réécrit avec `placeGroupConsecutive()`, `longestConsecutiveRun()`, `optimizeConsecutive()`, 3 ordonnancements de groupes, 100 retries aléatoires.

---

## 13. Audit Notes/Bulletins/Fiches/Paramètres (2026-09-04)

### Corrections appliquées

| # | Sévérité | Fichier | Correction |
|---|----------|---------|------------|
| 1 | Haute | `print_fiches_v2.php` | Mode A (examen seul) géré : `ex_active` détecté, `fcells()` inclut `ex`, maxima incluent `ex` |
| 2 | Haute | `evaluations.php`, `fiches.php`, `index.php` | Double inclusion `Footer.php` supprimée (HTML dupliqué) |
| 3 | Moyenne | `fiches.php` | Ordre colonnes corrigé : TJ/COMP/RESS/TOT (cohérent avec bulletins) ; `cumMax`/`midMax` incluent `ex` |
| 4 | Moyenne | `Parametres.php` | `regle_admis_moy` et `regle_ajourne_moy` ajoutés à la whitelist |
| 5 | Moyenne | `Notes_model.php` | Seuils 12/10 hardcodés remplacés par `get_setting()` dynamiques |
| 6 | Basse | `Notes_model.php` | Code mort supprimé (~150 lignes : `get_all`, `get_by_id`, `create_record`, `create_batch`, `update_record`, `delete_record`, `get_bulletins`, `get_grille_notes`) |
| 7 | Basse | `print_fiches.php` | Fichier obsolète supprimé |
| 8 | Doc | `fonctionnement.md` | TJ = `note_max_matiere` (pas heures × facteur) ; `facteur_points_heure` marqué obsolète |

### Session UI/UX — bulletins, dropdowns, conduite (sept. 2026)

#### Bulletins — colonne "BLANCHES"

- **En-tête colonne première** : le `<th>` vide du tableau bulletin (rowspan 2 ou 3) reçoit le libellé **"BLANCHES"** (au lieu de rester vide). Mode A et mode B.
- **Largeur colonne** : `branches-header` passée de `140px` à `200px` pour accueillir les noms de matières plus longs.

#### Remplacement global des dropdowns 3-points par icônes directes

**Problème** : les menus déroulants Bootstrap (3-points `tabler:dots-vertical`) étaient **coupés/invisibles** dans les tableaux utilisant `scrollX: true` de DataTables. Le conteneur de scroll crée un `overflow: hidden` qui clipse les dropdowns positionnés en `absolute`. Même avec `data-bs-container="body"`, les boutons eux-mêmes restaient hors de vue quand la colonne Actions était scrollée.

**Solution** : remplacement de TOUS les dropdowns 3-points par des **icônes directes** (Modifier/Supprimer/etc.) sur la même ligne dans un `<div class="d-flex align-items-center gap-8">`.

**23 fichiers modifiés** dans tous les modules :

| Module | Fichier | Actions affichées |
|---|---|---|
| Classes | `matieres.php` | Modifier + Supprimer |
| Classes | `sections.php` | Modifier + Supprimer |
| Classes | `index.php` | Modifier + Supprimer |
| Classes | `enseignements.php` | Modifier + Supprimer |
| Classes | `periodes.php` | Modifier + Activer/Desactiver + Rendre en cours + Supprimer |
| Classes | `annees.php` | Modifier + Activer/Rendre en cours + Supprimer |
| Enseignants | `list.php` | Voir + Modifier + Emploi du temps + Supprimer |
| Enseignants | `programmes.php` | Modifier + Supprimer |
| Étudiants | `list.php` | Voir + Modifier + Supprimer |
| Étudiants | `inscriptions.php` | Supprimer |
| Disponibilites | `index.php` | Modifier + Supprimer |
| Notes | `evaluations.php` | Modifier + Supprimer |
| Utilisateurs | `index.php` | Modifier + Supprimer |
| Uniformes | `index.php` | Modifier + Supprimer |
| Type_frais | `index.php` | Modifier + Supprimer |
| Stock_Categories | `index.php` | Modifier + Supprimer |
| Frais | `index.php` | Modifier + Supprimer (×2 tables) |
| Produits | `index.php` | Modifier + Supprimer |
| Librairie | `index.php` | Modifier + Approvisionner + Supprimer |
| Jours | `index.php` | Modifier + Supprimer |
| Recu | `index.php` | Imprimer + Modifier + Supprimer |
| Paiement_recu | `index.php` | Supprimer |
| Echeance | `index.php` | Modifier + Supprimer |
| Commandes | `index.php` | Voir + Select statut (dropdown natif) + Supprimer |

**Couleurs des icônes** : bleu (`text-primary-light`) = modifier/voir ; vert (`text-success`) = activer/approvisionner ; orange (`text-warning`) = désactiver ; rouge (`text-danger`) = supprimer.

#### Conduite — corrections

| # | Correction | Détail |
|---|---|---|
| 1 | **Footer.php inclus 2 fois** | Supprimé le doublon (lignes 138 + 341 → 1 seule inclusion) |
| 2 | **Trimestres filtrés par année active** | Le controller `Conduite::index()` ne charge plus que les périodes de l'année active (`id_annee = id_annee_active`). L'API `Periodes::api_list()` accepte désormais le paramètre `id_annee` pour filtrer côté serveur. |
| 3 | **Changement d'année → rechargement des trimestres** | JS : quand on change l'année, les périodes sont rechargées via `API.periodes.list({ id_annee })` et le sélecteur est reconstruit dynamiquement (plus de simple masquage CSS) |