<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

$table = 'tl_job_archive';

$GLOBALS['TL_DCA'][$table] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'ctable' => ['tl_job'],
        'switchToEdit' => true,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
            ],
        ],
    ],

    'list' => [
        'sorting' => [
            'mode' => 1,
            'fields' => ['title'],
            'flag' => 1,
            'panelLayout' => 'filter;search,limit',
        ],
        'label' => [
            'fields' => ['title'],
            'format' => '%s',
        ],
        'global_operations' => [
            'categories' => [
                'href' => 'table=tl_job_category',
                'icon' => 'bundles/dreibeinjobposting/category.png',
            ],
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'table=tl_job',
                'icon' => 'edit.svg',
            ],
            'editheader' => [
                'href' => 'act=edit',
                'icon' => 'header.svg',
            ],
            'copy' => [
                'href' => 'act=copy',
                'icon' => 'copy.svg',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'palettes' => [
        'default' => '{title_legend},title,frontendTitle,jumpTo;{apply_legend},apply_inactive_link,apply_inactive_text;',
    ],
    'fields' => [
        'id' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'title' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'frontendTitle' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'jumpTo' => [
            'exclude' => true,
            'inputType' => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval' => ['mandatory' => true, 'fieldType' => 'radio', 'tl_class' => 'clr'],
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
            'sql' => ['type' => Types::INTEGER, 'default' => 0, 'notnull' => true],
        ],
        'apply_inactive_link' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'apply_inactive_text' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'],
            'explanation' => 'insertTags',
            'sql' => ['type' => Types::TEXT, 'notnull' => false],
        ],
    ],
];
