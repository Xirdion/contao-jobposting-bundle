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
use Contao\System;
use Doctrine\DBAL\Types\Types;

$table = 'tl_job';
$contentTable = 'tl_content';
System::loadLanguageFile($contentTable);

$GLOBALS['TL_DCA'][$table] = [
    'config' => [
        'dataContainer' => DC_Table::class,
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
            'fields' => ['title'],
            'panelLayout' => 'filter;search,sort,limit',
            'headerFields' => ['title'],
        ],
        'label' => [
            'fields' => ['title', 'date'],
            'showColumns' => true,
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
                'href' => 'act=copy',
                'icon' => 'copy.svg',
            ],
            'cut' => [
                'href' => 'act=paste&amp;mode=cut',
                'icon' => 'cut.svg',
                'attributes' => 'onclick="Backend.getScrollOffset()"',
            ],
            'delete' => [
                'href' => 'act=delete',
                'icon' => 'delete.svg',
                'attributes' => 'onclick="if(!confirm(\'' . ($GLOBALS['TL_LANG']['MSC']['deleteConfirm'] ?? null) . '\'))return false;Backend.getScrollOffset()"',
            ],
            'toggle' => [
                'icon' => 'visible.svg',
            ],
            'feature' => [
                'icon' => 'featured.svg',
            ],
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],

    'palettes' => [
        '__selector__' => ['addImage', 'overwriteMeta'],
        'default' => '{title_legend},title,alias;'
            . '{category_legend},categories;'
            . '{date_legend},date,time;'
            . '{meta_legend},pageTitle,description,serpPreview;'
            . '{teaser_legend},teaser;'
            . '{image_legend},addImage;'
            . '{company_legend},company,companyUrl,companyLogo;'
            . '{job_legend},job_type,job_times,postal,city,street,region,country,remote;'
            . '{salary_legend},salary,salaryInterval;'
            . '{conditions_legend},responsibility,skills,qualification,education,experience;'
            . '{apply_legend},apply_link,apply_inactive_link,apply_inactive_text;'
            . '{expert_legend:hide},cssClass,featured;'
            . '{publish_legend},apply_active,published,start,stop',
    ],

    'subpalettes' => [
        'addImage' => 'singleSRC,size,floating,imagemargin,fullsize,overwriteMeta',
        'overwriteMeta' => 'alt,imageTitle,imageUrl,caption',
    ],
    'fields' => [
        'id' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'autoincrement' => true, 'notnull' => true],
        ],
        'pid' => [
            'foreignKey' => 'tl_job_archive.title',
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'title' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'flag' => 1,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'alias' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'alias', 'doNotCopy' => true, 'unique' => true, 'maxlength' => 255, 'tl_class' => 'w50 clr'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'categories' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'jobCategoryPicker', // use custom dca-picker-widget
            'foreignKey' => 'tl_job_category.title',
            'eval' => [
                'dcaPicker' => [
                    'do' => 'jobs', // BE_MOD
                    'context' => 'jobCategory', // internal context
                    'fieldType' => 'checkbox',
                ],
                'context' => 'jobCategory',
                'fieldType' => 'checkbox',
                'multiple' => true,
                'tl_class' => 'w50 wizard',
            ],
            'relation' => [
                'type' => 'belongsToMany',
                'load' => 'lazy',
            ],
            'sql' => ['type' => Types::BLOB, 'notnull' => false],
        ],
        'date' => [
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'flag' => 8,
            'inputType' => 'text',
            // alwaysSave is used here, because the default value is set with the load_callback the value wont be stored if it was not changed
            'eval' => ['rgxp' => 'date', 'mandatory' => true, 'doNotCopy' => true, 'datepicker' => true, 'tl_class' => 'w50 wizard', 'alwaysSave' => true],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'time' => [
            'default' => time(),
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'flag' => 8,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'time', 'mandatory' => true, 'doNotCopy' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0, 'notnull' => true],
        ],
        'pageTitle' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'decodeEntities' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'description' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => ['style' => 'height:60px', 'decodeEntities' => true, 'tl_class' => 'clr'],
            'sql' => ['type' => Types::TEXT, 'notnull' => false],
        ],
        'serpPreview' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['serpPreview'],
            'exclude' => true,
            'inputType' => 'serpPreview',
            'eval' => ['titleFields' => ['pageTitle', 'title'], 'descriptionFields' => ['description', 'teaser']],
            'sql' => null,
        ],
        'teaser' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr'],
            'sql' => ['type' => Types::TEXT, 'notnull' => false],
        ],
        'addImage' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true],
            'sql' => ['type' => Types::STRING, 'length' => 1, 'default' => '', 'notnull' => true],
        ],
        'singleSRC' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['singleSRC'],
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'extensions' => Config::get('validImageTypes'), 'mandatory' => true],
            'sql' => ['type' => Types::BINARY, 'length' => 16, 'notnull' => false],
        ],
        'size' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['size'],
            'exclude' => true,
            'inputType' => 'imageSize',
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'eval' => ['rgxp' => 'natural', 'includeBlankOption' => true, 'nospace' => true, 'helpwizard' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'floating' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['floating'],
            'exclude' => true,
            'inputType' => 'radioTable',
            'options' => ['above', 'left', 'right', 'below'],
            'eval' => ['cols' => 4, 'tl_class' => 'w50'],
            'reference' => &$GLOBALS['TL_LANG']['MSC'],
            'sql' => ['type' => Types::STRING, 'length' => 12, 'default' => '', 'notnull' => true],
        ],
        'imagemargin' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['imagemargin'],
            'exclude' => true,
            'inputType' => 'trbl',
            'options' => $GLOBALS['TL_CSS_UNITS'],
            'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 128, 'default' => '', 'notnull' => true],
        ],
        'fullsize' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['fullsize'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => Types::BOOLEAN, 'default' => false, 'notnull' => true],
        ],
        'overwriteMeta' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['overwriteMeta'],
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
            'sql' => ['type' => Types::STRING, 'length' => 1, 'default' => '', 'notnull' => true],
        ],
        'alt' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['alt'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'imageTitle' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['imageTitle'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'imageUrl' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['imageUrl'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'addWizardClass' => false, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'caption' => [
            'label' => &$GLOBALS['TL_LANG'][$contentTable]['caption'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'allowHtml' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'company' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['mandatory' => true, 'maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'companyUrl' => [
            'label' => &$GLOBALS['TL_LANG']['MSC']['url'],
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'addWizardClass' => false, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'companyLogo' => [
            'exclude' => true,
            'inputType' => 'fileTree',
            'eval' => ['fieldType' => 'radio', 'filesOnly' => true, 'isGallery' => true, 'extensions' => 'jpg,jpeg,gif,png', 'tl_class' => 'clr m12'],
            'sql' => ['type' => Types::BINARY, 'length' => 16, 'notnull' => false],
        ],
        'job_type' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['multiple' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'job_times' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'postal' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 32, 'tl_class' => 'clr w50'],
            'sql' => ['type' => Types::STRING, 'length' => 32, 'notnull' => true, 'default' => ''],
        ],
        'city' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'street' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'region' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'country' => [
            'exclude' => true,
            'filter' => true,
            'sorting' => true,
            'inputType' => 'select',
            'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
            'options_callback' => static function () {
                return System::getCountries();
            },
            'sql' => ['type' => Types::STRING, 'length' => 64, 'notnull' => true, 'default' => ''],
        ],
        'remote' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'clr'],
            'sql' => ['type' => Types::BOOLEAN, 'notnull' => true, 'default' => false],
        ],
        'salary' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 10, 'rgxp' => 'digit', 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 10, 'notnull' => true, 'default' => ''],
        ],
        'salaryInterval' => [
            'default' => 'MONTH',
            'exclude' => true,
            'inputType' => 'select',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 8, 'notnull' => true, 'default' => ''],
        ],
        'responsibility' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'skills' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'qualification' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'education' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'experience' => [
            'exclude' => true,
            'search' => true,
            'inputType' => 'text',
            'eval' => ['maxlength' => 255, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'notnull' => true, 'default' => ''],
        ],
        'apply_active' => [
            'exclude' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => Types::BOOLEAN, 'default' => false, 'notnull' => true],
        ],
        'apply_link' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['unique' => true, 'rgxp' => 'url', 'decodeEntities' => true, 'maxlength' => 255, 'dcaPicker' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
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
        'cssClass' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => Types::STRING, 'length' => 255, 'default' => '', 'notnull' => true],
        ],
        'featured' => [
            'exclude' => true,
            'filter' => true,
            'inputType' => 'checkbox',
            'eval' => ['tl_class' => 'w50 m12'],
            'sql' => ['type' => Types::BOOLEAN, 'default' => false, 'notnull' => true],
        ],
        'published' => [
            'exclude' => true,
            'filter' => true,
            'flag' => 1,
            'default' => 0, // MySQL converts boolean into TINYINT
            'inputType' => 'checkbox',
            'eval' => ['doNotCopy' => true, 'tl_class' => 'w50 m12'],
            'sql' => ['type' => Types::BOOLEAN, 'default' => false, 'notnull' => true],
        ],
        'start' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => Types::INTEGER, 'default' => 0, 'notnull' => true],
        ],
        'stop' => [
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => Types::INTEGER, 'default' => 0, 'notnull' => true],
        ],
    ],
];
