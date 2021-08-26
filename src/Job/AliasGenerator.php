<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Job;

use Contao\CoreBundle\Slug\Slug;
use Dreibein\JobpostingBundle\Repository\AliasInterface;

class AliasGenerator
{
    private Slug $slug;

    public function __construct(Slug $slug)
    {
        $this->slug = $slug;
    }

    /**
     * Generate a valid alias for a given model.
     *
     * @param AliasInterface $model
     * @param string         $title
     *
     * @return string
     */
    public function generateAlias(AliasInterface $model, string $title): string
    {
        // Declare the check-function for the slug generator
        $aliasExists = static function (string $alias) use ($model): bool {
            return $model::checkAlias($model->getId(), $alias) > 0;
        };

        // Generate alias
        return $this->slug->generate($title, $model->getJumpToPageId(), $aliasExists);
    }
}
