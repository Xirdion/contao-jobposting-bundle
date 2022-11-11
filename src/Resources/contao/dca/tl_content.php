<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

use Doctrine\DBAL\Types\Types;

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
        'mandatory' => true,
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
    'sql' => ['type' => Types::INTEGER, 'unsigned' => true, 'default' => 0, 'notnull' => true],
];
