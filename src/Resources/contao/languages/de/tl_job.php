<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

$GLOBALS['TL_LANG']['tl_job'] = [
    // legends
    'title_legend' => 'Einstellungen für den Titel',
    'category_legend' => 'Job-Kategorien',
    'date_legend' => 'Datum und Zeit',
    'meta_legend' => 'Metadaten',
    'teaser_legend' => 'Unterüberschrift und Teaser',
    'image_legend' => 'Bildeinstellungen',
    'company_legend' => 'Firmendaten',
    'job_legend' => 'Job-Details',
    'salary_legend' => 'Grundgehalt',
    'conditions_legend' => 'Voraussetzungen',
    'apply_legend' => 'Bewerbungseinstellungen',
    'expert_legend' => 'Experteneinstellungen',
    'publish_legend' => 'Veröffentlichung',

    // fields
    'tstamp' => ['Änderungsdatum', 'Änderungsdatum des Jobs.'],
    'title' => ['Titel', 'Bitte geben Sie den Job-Titel ein.'],
    'alias' => ['Jobalias', 'Der Jobalias ist eine eindeutige Referenz, die anstelle der numerischen Job-ID aufgerufen werden kann.'],
    'categories' => ['Kategorien', 'Eine oder mehrere Kategorien auswählen.'],
    'date' => ['Datum', 'Bitten geben Sie das Datum gemäß dem globalen Format ein.'],
    'time' => ['Uhrzeit', 'Bitten geben Sie die Uhrzeit ein.'],
    'pageTitle' => ['Meta-Titel', 'Hier können Sie einen individuellen Meta-Titel eingeben, um den Standard-Seitentitel zu überschreiben.'],
    'description' => ['Meta-Description', 'Hier können Sie eine individuelle Meta-Beschreibung eingeben, um die Standard-Seitenbeschreibung zu überschreiben.'],
    'teaser' => ['Teasertext', 'Der Teasertext kann in einer Jobliste anstatt des ganzen Eintrags angezeigt werden.'],
    'addImage' => ['Ein Bild hinzufügen', 'Dem Job ein Bild hinzufügen.'],
    'company' => ['Firma', 'Name der Firma'],
    'companyLogo' => ['Firmenlogo', 'Logo, das die Organisation repräsentiert, min. 112x112 Pixel, Format: jpg, png oder gif'],
    'job_type' => ['Art der Beschäftigung', 'Sie können auch mehrere Beschäftigungsarten angeben.'],
    'job_times' => ['Arbeitszeit', 'Beispiel: 8:00-17:00, Gleitzeit'],
    'postal' => ['Postleitzahl', 'Geben Sie die Postleitzahl ein.'],
    'city' => ['Ort', 'Geben Sie den Ort der Beschäftigung ein.'],
    'street' => ['Straße', 'Geben Sie die Straße der Beschäftigung ein.'],
    'region' => ['Region', 'Geben Sie die Region der Beschäftigung ein, z.B. Bundesland.'],
    'country' => ['Land', 'Wählen Sie das Land der Beschäftigung aus.'],
    'remote' => ['Telearbeit', 'Es muss sich vollständig um Telearbeit handeln. Es darf nicht für Jobs verwendet werden, die gelegentlich von zu Hause aus ausgeübt werden können, bei denen über Heimarbeit verhandelt werden kann oder die aus anderen Gründen keine hundertprozentige Telearbeit umfassen.'],
    'salary' => ['Grundgehalt', 'Das tatsächliche Grundgehalt für den Job, wie vom Arbeitgeber angegeben (keine Schätzung).'],
    'salaryInterval' => ['Intervall', 'Das Gehalt gilt für den folgenden Zeitraum'],
    'responsibility' => ['Aufgabenbereiche', 'Definieren Sie die Aufgabenbereiche.'],
    'skills' => ['Kenntnisse', 'Kenntnisse, die vorausgesetzt werden.'],
    'qualification' => ['Qualifikationen', 'Gewünschte Qualifikationen des Bewerbers.'],
    'education' => ['Ausbildung', 'Ausbildungsanforderungen an den Bewerber.'],
    'experience' => ['Erfahrungen', 'Angaben zur Berufserfahrung.'],
    'apply_active' => ['Bewerbungsphase ist aktiv', 'Geben Sie hier an, ob man sich aktiv für den Job bewerben kann.'],
    'apply_link' => ['Bewerbungslink', 'Hinterlegen Sie hier den Bewerbungslink.'],
    'apply_inactive_link' => ['Initiativ-Bewerbungslink', 'Hinterlegen Sie hier den Link für eine Initiativ-Bewerbung.'],
    'apply_inactive_text' => ['Initiativ-Bewerbungstext', 'Geben Sie hier weitere Infos zur Initiativ-Bewerbung an.'],
    'cssClass' => ['CSS-Klasse', 'Hier können Sie eine oder mehrere Klassen eingeben.'],
    'featured' => ['Job hervorheben', 'Den Job in einer Liste von hervorgehobener Jobs anzeigen.'],
    'published' => ['Job veröffentlichen', 'Den Job auf der Webseite anzeigen.'],
    'start' => ['Anzeigen ab', 'Wenn Sie den Job erst ab einem bestimmten Zeitpunkt auf der Webseite anzeigen möchten, können Sie diesen hier eingeben. Andernfalls lassen Sie das Feld leer.'],
    'stop' => ['Anzeigen bis', 'Wenn Sie den Job nur bis zu einem bestimmten Zeitpunkt auf der Webseite anzeigen möchten, können Sie diesen hier eingeben. Andernfalls lassen Sie das Feld leer.'],

    // buttons
    'new' => ['Neu', 'Einen neuen Job anlegen'],
    'all' => ['Mehrere bearbeiten', 'Mehrere Jobs auf einmal bearbeiten'],
    'edit' => ['bearbeiten', 'Job %s bearbeiten'],
    'editheader' => ['Einstellungen bearbeiten', 'Einstellungen des Job %s bearbeiten'],
    'copy' => ['duplizieren', 'Job duplizieren'],
    'delete' => ['löschen', 'Job %s löschen'],
    'show' => ['Details anzeigen', 'Details des Jobs %s anzeigen'],
];
