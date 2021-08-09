<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

use Contao\BackendUser;
use Contao\Config;
use Contao\System;

$table = 'tl_job';
$contentTable = 'tl_content';
System::loadLanguageFile($contentTable);

$GLOBALS['TL_DCA'][$table] = [
    'config' => [
        'dataContainer' => 'Table',
        'ptable' => 'tl_job_archive',
        'ctable' => ['tl_content'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index',
                'pid,published,featured,start,stop' => 'index',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode' => 4,
            'fields' => ['dateTime'],
            'headerFields' => ['title', 'jumpTo', 'tstamp'],
            'panelLayout' => 'filter;search,limit',
        ],
        'global_operations' => [
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_content',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'copy' => [
                'href' => 'act=paste&amp;mode=copy',
                'icon' => 'copy.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'icon' => 'visible.svg',
                'attributes' => 'onclick="Backend.getScrollOffset();return AjaxRequest.toggleVisibility(this,%s)"',
                'showInHeader' => true,
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['addImage', 'overwriteMeta'],
        'default' => '{title_legend},title,alias;{category_legend},categories;{date_legend},dateTime;{meta_legend},serpPreview;{teaser_legend},subHeadline,teaser;{image_legend},addImage;{expert_legend:hide},cssClass;{publish_legend},published,start,stop',
    ],

    'subpalettes' => [
        'addImage' => 'singleSRC,size,floating,imagemargin,fullsize,overwriteMeta',
        'overwrite_meta' => 'alt,imageTitle,imageUrl,caption',
    ],
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true, 'notnull' => true],
        ],
        'pid' => [
            'foreignKey' => 'tl_job_archive.title',
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'title' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'alias' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w50 clr'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'categories' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'select',
            'eval' => ['multiple' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'blob', 'notnull' => false],
        ],
        'dateTime' => [
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'flag' => 8,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'mandatory' => true, 'doNotCopy' => true, 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'serpPreview' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['serpPreview'],
            'exclude' => true,
            'inputType' => 'serpPreview',
            'eval' => ['titleFields' => ['pageTitle', 'title'], 'descriptionFields' => ['description', 'teaser']],
            'sql' => null,
        ],
        'subHeadline' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'teaser' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'tl_class' => 'clr'],
            'sql' => ['type' => 'text', 'notnull' => false],
        ],
        'addImage' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => ['type' => 'boolean', 'default' => false, 'notnull' => true],
        ],
        'singleSRC' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['singleSRC'],
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => Config::get('validImageTypes'), 'mandatory' => true],
            'sql' => ['type' => 'binary', 'length' => 16, 'notnull' => false],
        ],
        'size' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['size'],
            'exclude' => true,
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
            'options_callback' => static function () {
                return System::getContainer()->get('contao.image.image_sizes')->getOptionsForUser(BackendUser::getInstance());
            },
            'sql' => ['type' => 'string', 'length' => 64, 'default' => '', 'notnull' => true],
        ],
        'floating' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['floating'],
            'exclude' => true,
            'inputType' => 'radioTable',
            'options' => ['above', 'left', 'right', 'below'],
            'eval' => ['cols' => 4, 'tl_class' => 'w50'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'sql' => ['type' => 'string', 'length' => 12, 'default' => '', 'notnull' => true],
        ],
        'imagemargin' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['imagemargin'],
            'exclude' => true,
            'inputType' => 'trbl',
            'options' => $GLOBALS['TL_CSS_UNITS'],
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 128, 'default' => '', 'notnull' => true],
        ],
        'fullsize' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['fullsize'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => 'boolean', 'default' => false, 'notnull' => true],
        ],
        'overwriteMeta' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['overwriteMeta'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => ['type' => 'boolean', 'default' => false, 'notnull' => true],
        ],
        'alt' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['alt'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'imageTitle' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['imageTitle'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'imageUrl' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['imageUrl'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'addWizardClass' => false, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'caption' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['caption'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'cssClass' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'featured' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => 'boolean', 'default' => false, 'notnull' => true],
        ],
        'published' => [
            'exclude' => true,
            'filter' => true,
            'flag' => 1,
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true, 'tl_class' => 'm12'],
            'sql' => ['type' => 'boolean', 'default' => false, 'notnull' => true],
        ],
        'start' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'integer', 'default' => 0, 'notnull' => true],
        ],
        'stop' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'integer', 'default' => 0, 'notnull' => true],
        ],
    ],
];