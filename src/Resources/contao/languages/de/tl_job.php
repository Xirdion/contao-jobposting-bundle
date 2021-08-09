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
