<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

if ('jobs' === Contao\Input::get('do')) {
    $GLOBALS['TL_DCA']['tl_content']['config']['ptable'] = 'tl_job';
}

$GLOBALS['TL_DCA']['tl_content']['palettes']['job_display'] = '{type_legend},type;{job_legend},job_id';

$GLOBALS['TL_DCA']['tl_content']['fields']['job_id'] = [
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
