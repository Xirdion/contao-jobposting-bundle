<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

$table = 'tl_content';

$GLOBALS['TL_DCA'][$table]['palettes']['job_display'] =
    '{type_legend},type;'
    . '{job_legend},job_id;'
    . '{template_legend:hide},customTpl;'
    . '{protected_legend:hide},protected;'
    . '{expert_legend:hide},guests,cssID;'
    . '{invisible_legend:hide},invisible,start,stop';

$GLOBALS['TL_DCA'][$table]['fields']['job_id'] = [
    'exclude' => true,
    'inputType' => 'jobPicker',
    'foreignKey' => 'tl_job.title',
    'eval' => [
        'dcaPicker' => [
            'do' => 'jobs',
            'context' => 'job',
            'fieldType' => 'radio',
        ],
        'fieldType' => 'radio',
        'context' => 'job',
        'tl_class' => 'w50 wizard',
    ],
    'relation' => [
        'type' => 'belongsToOne',
        'load' => 'lazy',
    ],
    'sql' => ['mandatory' => true, 'type' => 'integer', 'unsigned' => true, 'default' => 0, 'notnull' => true],
];
