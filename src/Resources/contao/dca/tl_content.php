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

if ('jobs' === Contao\Input::get('do')) {
    $GLOBALS['TL_DCA'][$table]['config']['ptable'] = 'tl_job';
}

$GLOBALS['TL_DCA'][$table]['palettes']['job_display'] = '{type_legend},type;{job_legend},job_id,job_template';

$GLOBALS['TL_DCA'][$table]['fields']['job_id'] = [
    'exclude' => true,
    'inputType' => 'text',
    'eval' => [
        'dcaPicker' => [
            'do' => 'jobs',
            'context' => 'job',
            'fieldType' => 'radio',
        ],
        'tl_class' => 'w50 wizard',
    ],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA'][$table]['fields']['job_template'] = [
    'exclude' => true,
    'inputType' => 'select',
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 64, 'default' => '', 'notnull' => true],
];
