# Charte de développement – Projet PHP

## 1. Objectif du document

Cette charte définit les **règles officielles de développement** du projet.
Elle a pour but de hookup garantir un code **cohérent, maintenable et homogène**, et de servir de **référence commune** à toute l’équipe.

Toute contribution au projet doit respecter les règles décrites ci-dessous.

---

## 2. Technologies imposées

### 2.1 PHP & Framework

* **Framework obligatoire : CodeIgniter**
* Il est **impératif de privilégier les services natifs de CodeIgniter**.

Exemples (liste non exhaustive) :

* Gestion des requêtes GET / POST
* Helpers et librairies natives
* Envoi d’emails
* Services utilitaires fournis par le framework

👉 Éviter au maximum le code custom lorsqu’un service CodeIgniter existe déjà.

---

### 2.2 Gestion du style – Tailwind CSS

* **Tailwind CSS est obligatoire**
* **Aucun CSS classique dispersé dans le projet**
* Tout le style (layout, couleurs, espacements, typographie) doit être réalisé via Tailwind

👉 Tailwind est l’unique source de vérité pour le design.

---

## 3. Architecture par composants

### 3.1 Principe général

* Le projet repose sur une **architecture orientée composants**.
* Chaque élément réutilisable doit être isolé dans un **composant dédié**.

Exemples de composants :

* Formulaires
* Cartes (ex : carte d’accueil)
* Boutons
* Sections récurrentes

---

### 3.2 Rôle des vues

* Les fichiers de vues doivent être **les plus vides possibles**.
* Une vue ne contient **aucune logique HTML complexe**.
* Une vue a pour rôle **d’assembler des composants existants** afin de construire une page.

👉 Toute structure complexe doit être déplacée dans un composant.

---

## 4. Centralisation des couleurs

* Les couleurs du projet doivent être **centralisées dans un seul fichier**.
* Il est **interdit d’inventer de nouvelles couleurs** au fur et à mesure du développement.
* Toute couleur utilisée doit provenir de la palette définie.

👉 Objectif : cohérence visuelle et maintenance simplifiée.

---

## 5. Container global obligatoire

### 5.1 Principe

* Un **container global** doit être utilisé pour **toutes les vues**.
* Aucune vue ne doit placer directement des éléments en dehors de ce container.

---

### 5.2 Rôle du container

Le container est responsable de :

* L’adaptation de l’affichage **mobile / desktop**
* La gestion des **marges latérales**
* La structure générale de la page

Concept visuel :

* Le contenu est affiché comme sur une **feuille A4 centrée à l’écran**
* Tous les éléments sont positionnés à l’intérieur de ce cadre

---

## 6. Principe fondamental à retenir

> **Une vue = un assemblage de composants, placés dans un container global, stylés uniquement avec Tailwind et utilisant prioritairement les services CodeIgniter.**

---

## 7. Respect de la charte

* Toute nouvelle fonctionnalité doit respecter cette charte
* En cas de doute, la charte fait foi
* Toute évolution des règles doit être discutée et validée par l’équipe

---

📌 Ce document est la référence officielle du projet.
