<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

use Contao\Config;
use Contao\DC_Table;
use Doctrine\DBAL\Types\Types;

$table = 'tl_job_category';

$GLOBALS['TL_DCA'][$table] = [
    'config' => [
        'dataContainer' => DC_Table::class,
        'enableVersioning' => true,
        'markAsCopy' => 'title',
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'alias' => 'index',
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
            'all' => [
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset()" accesskey="e"',
            ],
        ],
        'operations' => [
            'edit' => [
                'href' => 'act=edit',
                'icon' => 'edit.svg',
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
        'default' => '{title_legend},title,alias,frontendTitle;{details_legend},description,singleSRC;{redirect_legend},jumpTo;{expert_legend:hide},cssClass;{publish_legend},published;',
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
        'alias' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'unique' => true, 'spaceToUnderscore' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'description' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => ['rte' => 'tinyMCE', 'helpwizard' => true, 'tl_class' => 'clr'],
            'explanation' => 'insertTags',
            'sql' => ['type' => Types::TEXT, 'notnull' => false],
        ],
        'singleSRC' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['files' => true, 'filesOnly' => true, 'fieldType' => 'radio', 'extensions' => Config::get('validImageTypes'), 'tl_class' => 'clr'],
            'sql' => ['type' => Types::BINARY, 'length' => 16, 'notnull' => false],
        ],
        'jumpTo' => [
            'exclude' => true,
            'inputType' => 'pageTree',
            'foreignKey' => 'tl_page.title',
            'eval' => ['fieldType' => 'radio', 'tl_class' => 'clr'],
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
            'sql' => ['type' => Types::INTEGER, 'default' => 0, 'notnull' => true],
        ],
        'cssClass' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'published' => [
            'exclude' => true,
            'filter' => true,
            'default' => 0, // MySQL converts boolean into TINYINT
            'inputType' => 'checkbox',
            'sql' => ['type' => Types::BOOLEAN, 'default' => false, 'notnull' => true],
        ],
    ],
];
