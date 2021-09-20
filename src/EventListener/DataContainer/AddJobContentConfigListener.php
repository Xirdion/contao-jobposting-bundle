<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\Input;

class AddJobContentConfigListener
{
    private string $table = 'tl_content';

    /**
     * Dynamically set the ptable of tl_content.
     *
     * @Callback(table="tl_content", target="config.onload")
     *
     * @param DataContainer $dc
     */
    public function loadContentDca(DataContainer $dc): void
    {
        if ('jobs' === Input::get('do')) {
            $GLOBALS['TL_DCA'][$this->table]['config']['ptable'] = 'tl_job';
            $dc->ptable = $GLOBALS['TL_DCA'][$this->table]['config']['ptable'];
        }
    }
}
