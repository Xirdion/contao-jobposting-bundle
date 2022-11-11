<?php

declare(strict_types=1);

/*
 * This file is part of the job posting bundle.
 *
 * @author     Thomas Dirscherl <https://github.com/xirdion>
 * @link       https://github.com/xirdion/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Repository;

interface AliasInterface
{
    /**
     * Getter method to receive the ID of the current model.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Try to find the amount of entries with the given alias but not the specific ID.
     *
     * @param int    $id
     * @param string $alias
     *
     * @return int
     */
    public static function checkAlias(int $id, string $alias): int;

    /**
     * Try to get the ID of a jump-to-page.
     *
     * @return int
     */
    public function getJumpToPageId(): int;
}
