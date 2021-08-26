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
    'conditions_legend' => 'Vorraussetzungen',
    'expert_legend' => 'Experteneinstellungen',
    'publish_legend' => 'Veröffentlichung',

    // fields
    'tstamp' => ['Änderungsdatum', 'Änderungsdatum des Jobs.'],
    'title' => ['Titel', 'Bitte geben Sie den Job-Titel ein.'],
    'alias' => ['Jobalias', 'Der Jobalias ist eine eindeutige Referenz, die anstelle der numerischen Job-ID aufgerufen werden kann.'],
    'categories' => ['Kategorien', 'Eine oder mehrere Kategorien auswählen.'],
    'dateTime' => ['Datum und Uhrzeit', 'Bitten geben Sie das Datum und die Uhrzeit gemäß dem globalen Format ein.'],
    'subHeadline' => ['Unterüberschrift', 'Hier können Sie eine Unterüberschrift eingeben.'],
    'teaser' => ['Teasertext', 'Der Teasertext kann in einer Jobliste anstatt des ganzen Eintrags angezeigt werden.'],
    'addImage' => ['Ein Bild hinzufügen', 'Dem Job ein Bild hinzufügen.'],
    'company' => ['Firma', 'Name der Firma'],
    'companyLogo' => ['Firmenlogo', 'Logo, das die Organisation repräsentiert, min. 112x112 Pixel, Format: jpg, png oder gif'],
    'type' => ['Art der Beschäftigung', 'Sie können auch mehrere Beschäftigungsarten angeben.'],
    'times' => ['Arbeitszeit', 'Beispiel: 8:00-17:00, Gleitzeit'],
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

    'types' => [
        'FULL_TIME' => 'Vollzeit',
        'PART_TIME' => 'Teilzeit',
        'CONTRACTOR' => 'Anbieter',
        'TEMPORARY' => 'Befristet',
        'INTERN' => 'Intern',
        'VOLUNTEER' => 'Freischaffender',
        'PER_DIEM' => 'Tageweise',
        'OTHER' => 'Andere',
    ],

    'jobIntervals' => [
        'HOUR' => 'pro Stunde',
        'DAY' => 'pro Tag',
        'WEEK' => 'pro Woche',
        'MONTH' => 'pro Monat',
        'YEAR' => 'pro Jahr',
    ],
];


$GLOBALS['TL_LANG']['tl_content']['jobEmptypes']['FULL_TIME']  = 'Vollzeit';
$GLOBALS['TL_LANG']['tl_content']['jobEmptypes']['PART_TIME']  = 'Teilzeit';
$GLOBALS['TL_LANG']['tl_content']['jobEmptypes']['CONTRACTOR'] = 'Anbieter';
$GLOBALS['TL_LANG']['tl_content']['jobEmptypes']['TEMPORARY']  = 'Befristet';
$GLOBALS['TL_LANG']['tl_content']['jobEmptypes']['INTERN']     = 'Intern';
$GLOBALS['TL_LANG']['tl_content']['jobEmptypes']['VOLUNTEER']  = 'Freischaffender';
$GLOBALS['TL_LANG']['tl_content']['jobEmptypes']['PER_DIEM']   = 'Tageweise';
$GLOBALS['TL_LANG']['tl_content']['jobEmptypes']['OTHER']      = 'Andere';

$GLOBALS['TL_LANG']['tl_content']['jobIntervals']['HOUR']      = 'pro Stunde';
$GLOBALS['TL_LANG']['tl_content']['jobIntervals']['DAY']       = 'pro Tag';
$GLOBALS['TL_LANG']['tl_content']['jobIntervals']['WEEK']      = 'pro Woche';
$GLOBALS['TL_LANG']['tl_content']['jobIntervals']['MONTH']     = 'pro Monat';
$GLOBALS['TL_LANG']['tl_content']['jobIntervals']['YEAR']      = 'pro Jahr';
