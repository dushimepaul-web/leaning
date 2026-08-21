# Fonctionnement — Système de notation & bulletins

Ce document décrit la logique métier implémentée dans l'application. Il est **mis à jour à chaque modification** des règles.

## 1. Le TJ (Total de Jours / points max d'une matière)

- Le **TJ** d'une matière par période = **coefficient calculé** = `nb_heures_par_semaine × facteur_points_heure`.
- `facteur_points_heure` = **15** par défaut (paramètre global : `facteur_points_heure`).
- Exemple : CHIMIE 12 h/semaine → TJ = 12 × 15 = **180 points max**.
- Si les heures ne sont pas renseignées (`nb_heures_par_semaine` = 0) → TJ = 0 (maxima à 0 tant que les heures sont manquantes).

> Règle confirmée par l'utilisateur : **TJ = coefficient calculé (heures × facteur)** — la colonne stockée `matieres_classes.note_max_matiere` (ex-`coefficient`) n'est pas utilisée dans le calcul du TJ.

## 2. Les deux catégories de notes

Chaque matière est notée selon **deux catégories** :

| Catégorie | Rôle | Libellé à l'écran |
|---|---|---|
| **Ressources** | Évaluations ressources / TP | RESS |
| **Compétences** | Évaluations compétences / examen | COMP |

### 2.1. Pourcentages de répartition

- **Ressources à l'examen (%)** = `pourcentage_ressources_examen` (défaut **60**)
- **Compétences à l'examen (%)** = `pourcentage_competences_examen` (défaut **40**)

La répartition des maxima :
```
MAX RESS = TJ × pourcentage_ressources_examen / 100
MAX COMP = TJ × pourcentage_competences_examen / 100
MAX TOT  = TJ + MAX RESS + MAX COMP = 2 × TJ
```

- **Somme toujours = 100 %** : la page Paramètres auto-complète automatiquement l'autre champ (`100 − valeur saisie`).
- Exemple : TJ = 180, RESS 60 % → MAX RESS = 108 ; COMP 40 % → MAX COMP = 72 ; TOT = 360.

## 3. Activation / désactivation des catégories

- Paramètres globaux : `ressources_active` (1=Oui) et `competences_active` (1=Oui).
- **Garde-fou : au moins une catégorie doit rester active** — impossible de désactiver les deux à la fois.

### 3.1. Comportement à la désactivation

Quand on désactive une catégorie dans Paramètres :

- **Désactiver Compétences** → « Compétences à l'examen (%) » passe à **0** et « Ressources à l'examen (%) » à **100**.
- **Désactiver Ressources** → « Ressources à l'examen (%) » passe à **0** et « Compétences à l'examen (%) » à **100**.
- La catégorie active **absorbe tout le TJ** : `MAX EX = TJ` (100 %), l'autre catégorie = 0.
- **Total max toujours = 2 × TJ** (TJ + EX = 2 × TJ), que les deux catégories soient actives ou non.

### 3.2. Priorité classe → global

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
- Matières, Sous-Tot, Conduite, Totaux, Pourcentage, Mention, Place, Religion, Signatures (PARENTS / TITULAIRE).

Exemple de la ligne Signatures : `PARENTS` occupe la 1re cellule du bloc MAXIMA → seules `colSpan-1` cellules vides sont ajoutées après (pas `colSpan`), pour éviter une colonne excédentaire.

## 5. Flux des données

```
Page Paramètres (settings) ──> table parametres (globaux)
Page Classes (override)    ──> table classes (par classe)

Bulletins_model::get_bulletin_complet
  ├─ lit flags + pourcentages (classe → global)
  ├─ calcule les maxima (TJ, MAX RESS, MAX COMP) via _get_maxima
  └─ neutralise la catégorie inactive (EX = TJ, l'autre = 0)

Vues
  ├─ bulletins.php / fiches.php   (interactives, chargées en AJAX depuis api/bulletins/complet)
  ├─ print_bulletins.php          (impression bulletin)
  └─ print_fiches_v2.php          (impression fiche de points)
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
| `tj_points` | Ancien calcul du TJ (points fixes) — remplacé par le coefficient calculé. |
| `examen_pourcentage` | Redondant avec les « à l'examen (%) ». |
| `points_par_heure` | Ancien facteur — remplacé par `facteur_points_heure`. |
| `ressources_pourcentage` (global) | Doublon de « Ressources à l'examen (%) » — la surcharge par classe reste possible dans la page Classes. |
| `competences_pourcentage` (global) | Idem (doublon de « Compétences à l'examen (%) »). |
| `comportement_sans_bulletin` | Supprimé : sans bulletin → l'élève redouble toujours. |
| `nombre_trimestres` | Inutilisé : le nombre de périodes est déduit de la table `periodes`, pas d'un paramètre. |
| `prochain_num_recu` | Inutilisé : le numéro de reçu est saisi manuellement (`recus.numero_recu`, contrôle de doublon dans `Recu.php`). |
| `tva` | Inutilisé : aucun calcul de TVA n'existe dans l'application. |

La table `parametres` contient désormais **43 paramètres** (audit : tous utilisés, sauf `annee_active` qui est sauvegardé par la page mais dont l'année active réelle vient de `annees_scolaires.est_en_cours`).

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

### 8.3. Horaires

| Clé | Valeur | Rôle | Utilisé dans |
|---|---|---|---|
| `heure_debut_journee` | 07:30 | Heure du premier créneau | `Horaires_model.php:68` |
| `duree_cours` | 45 | Durée (min) d'un cours | `Horaires_model.php:69` |
| `duree_pause` | 20 | Durée (min) d'une pause | `Horaires_model.php:70` |
| `duree_vigie` | 10 | Durée (min) de la vigie | `Horaires_model.php:71` |
| `nb_creneaux_jour` | 8 | Nombre de créneaux par jour | `Horaires_model.php:72` |

### 8.4. Notation & bulletin

| Clé | Valeur | Rôle | Utilisé dans |
|---|---|---|---|
| `facteur_points_heure` | 15 | **TJ = heures hebdomadaires × facteur** (coefficient calculé) | `Bulletins_model.php:122` ; `Bulletins.php:467` ; `Fiches.php:113` ; `print_bulletins.php:43` |
| `pourcentage_ressources_examen` | 60.00 | % du TJ alloué aux Ressources (MAX RESS = TJ × %/100) | `Bulletins_model.php:84,124` ; `Bulletins.php:463` ; `Fiches.php:115` |
| `pourcentage_competences_examen` | 40 | % du TJ alloué aux Compétences (MAX COMP = TJ × %/100) | `Bulletins_model.php:87,123` ; `Bulletins.php:466` ; `Fiches.php:114` |
| `ressources_active` | 1 | Catégorie Ressources active (0/1), surchargeable par classe | `Bulletins_model.php:76-78,91` ; `Bulletins.php:455-457` ; `Classes.php:35,50` ; `print_fiches_v2.php:37` |
| `competences_active` | 1 | Catégorie Compétences active (0/1), surchargeable par classe | `Bulletins_model.php:79-81,92` ; `Bulletins.php:458-460` ; `Classes.php:35,50` ; `print_fiches_v2.php:38` |

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
- `application/modules/Notes/models/Bulletins_model.php` — `_get_maxima` (calcul TJ / RESS / COMP) et `get_bulletin_complet` (flags + neutralisation).
- `application/modules/Notes/controllers/Bulletins.php` — `api_bulletin_complet`, `export_bulletins_classe` (print).
- `application/modules/Notes/controllers/Fiches.php` — `api_fiche_par_cours`, `export_fiche_classe` (print fiche).
- `application/modules/Classes/controllers/Annees.php` — `_calculer_report` (décision de passage), `api_apercu_cloture`, `api_cloturer` (report avec override manuel).
- `application/modules/Classes/views/annees.php` — page Années + modal Clôture & Report (aperçu + ajustement manuel par élève).
- `application/modules/Notes/views/bulletins.php` — bulletin interactif.
- `application/modules/Notes/views/fiches.php` — fiche de points interactive.
- `application/modules/Notes/views/print_bulletins.php` — impression bulletin.
- `application/modules/Notes/views/print_fiches_v2.php` — impression fiche de points (vue active).
- `application/modules/Notes/views/print_fiches.php` — **code mort** (non chargé, non modifié).

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
- **Audit complet des paramètres** : chaque clé vérifiée — répertoire complet ajouté en **section 8** (rôle + lieu d'utilisation fichier:ligne pour chacun).
- **Corruption `?` corrigée en base** : les mots contenant des accents étaient stockés avec des `?` littéraux (0x3F) — `classes.libelle` (1ère PEDAGOGIQUE), `menus.libelle` (Scolarité, Reçus, Échéanciers, Paramètres, Disponibilités, Générer), `produits.unite` (pièce). Vérifié par scan binaire (`LIKE '%?%' COLLATE utf8mb4_bin`) sur les 154 colonnes texte.
- **`evaluations.sur` → `evaluations.ponderee_sur`** (renommée) : barème de chaque évaluation (défaut 20). Grille de notes, fiches et bulletins utilisent `note / ponderee_sur` pour normaliser.
- **`matieres_classes.coefficient` → `matieres_classes.note_max_matiere`** (renommée) : « note max matière » (poids de la matière pour la classe, défaut 1.0). Utilisée dans la clôture (`× 6`) et affichée dans la grille de notes (`×note_max_matiere`), les programmes et les horaires. L'API et les vues des enseignants utilisent désormais `note_max_matiere`.
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
- Dump `DB/vip_school.sql` régénéré après chaque changement de base.
- Dump `DB/vip_school.sql` régénéré après chaque changement de base.