<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

$table = 'tl_module';

$GLOBALS['TL_DCA'][$table]['fields']['headline']['options'] = ['h1', 'h2', 'h3', 'h4', 'span'];

// Add the new palettes for the new modules
$GLOBALS['TL_DCA'][$table]['palettes']['job_list'] = '{title_legend},name,headline,type;{config_legend},job_archives,job_readerModule,numberOfItems,job_order,skipFirst,perPage;{template_legend:hide},job_template,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';
$GLOBALS['TL_DCA'][$table]['palettes']['job_reader'] = '{title_legend},name,headline,type;{config_legend},job_archives;{template_legend:hide},job_template,customTpl;{image_legend:hide},imgSize;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID';

// Add new fields to tl_module
$GLOBALS['TL_DCA'][$table]['fields']['job_archives'] = [
    'exclude' => true,
    'inputType' => 'checkbox',
    'eval' => ['multiple' => true, 'mandatory' => true],
    'sql' => ['type' => 'blob', 'notnull' => false],
];

$GLOBALS['TL_DCA'][$table]['fields']['job_readerModule'] = [
    'exclude' => true,
    'inputType' => 'select',
    'reference' => &$GLOBALS['TL_LANG']['tl_module'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0, 'notnull' => true],
];

$GLOBALS['TL_DCA'][$table]['fields']['job_template'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => '', 'notnull' => true],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['job_order'] = [
    'exclude' => true,
    'inputType' => 'select',
    'reference' => &$GLOBALS['TL_LANG']['tl_module'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 32, 'default' => 'order_date_desc', 'notnull' => true],
];
