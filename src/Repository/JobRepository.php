<?php

declare(strict_types=1);

/*
 * This file is part of the Dreibein job posting bundle.
 *
 * @copyright  Copyright (c) 2021, Digitalagentur Dreibein GmbH
 * @author     Digitalagentur Dreibein GmbH <https://www.agentur-dreibein.de>
 * @link       https://github.com/dreibein/contao-jobposting-bundle
 */

namespace Dreibein\JobpostingBundle\Repository;

use Contao\Date;
use Contao\Model;
use Dreibein\JobpostingBundle\Model\JobModel;

/**
 * @method static JobModel|null findByIdOrAlias($varId, array $arrOptions = array())
 */
abstract class JobRepository extends AbstractAliasRepository
{
    protected static $strTable = 'tl_job';

    /**
     * Find a specific job by its ID.
     *
     * @param int $id
     *
     * @return static|null
     */
    public static function findById(int $id): ?JobModel
    {
        return static::findByPk($id);
    }

    /**
     * @param string $alias
     * @param array  $pIds
     *
     * @return static|null
     */
    public static function findPublishedByAliasAndPids(string $alias, array $pIds): ?JobModel
    {
        if (empty($pIds)) {
            return null;
        }

        $table = static::$strTable;
        $columns = static::getSearchColumns($pIds);
        $columns[] = $table . '.alias = ?';

        return static::findOneBy($columns, $alias);
    }

    /**
     * Find all jobs within a given range (limit + offset) and a given order
     * for some specific job archives.
     *
     * @param array  $pIds
     * @param ?bool  $onlyFeatured
     * @param int    $limit
     * @param int    $offset
     * @param string $order
     * @param bool   $orderFeatured
     *
     * @return static[]|Model\Collection|null
     */
    public static function findPublishedByPids(array $pIds, ?bool $onlyFeatured = null, int $limit = 0, int $offset = 0, string $order = '', bool $orderFeatured = false): ?Model\Collection
    {
        if (empty($pIds)) {
            return null;
        }

        // Prepare the query columns
        $columns = static::getSearchColumns($pIds, $onlyFeatured);
        $table = static::$strTable;
        $queryOrder = '';

        // Check if the entries should get ordered by their featured status
        if (true === $orderFeatured) {
            $queryOrder = $table . 'featured DESC, ';
        }

        // Prepare the options for the query
        switch ($order) {
            case 'order_date_asc':
                $queryOrder .= $table . '.date ASC';
                break;
            case 'order_date_desc':
                $queryOrder .= $table . '.date DESC';
                break;
            case 'order_title_asc':
                $queryOrder .= $table . '.title ASC';
                break;
            case 'order_title_desc':
                $queryOrder .= $table . '.title DESC';
                break;
            case 'order_random':
                $queryOrder .= 'RAND()';
                break;
            default:
                $queryOrder .= $table . '.date DESC';
        }

        $options = [
            'limit' => $limit,
            'offset' => $offset,
            'order' => $queryOrder,
        ];

        return static::findBy($columns, null, $options);
    }

    /**
     * Count all jobs by a given pID.
     *
     * @param int $pId
     *
     * @return int
     */
    public static function countByPid(int $pId): int
    {
        return static::countBy(['pid=?'], [$pId]);
    }

    /**
     * Get the amount of jobs for some job archives.
     *
     * @param array $pIds
     *
     * @return int
     */
    public static function countPublishedByPids(array $pIds, ?bool $onlyFeatured = null): int
    {
        if (empty($pIds)) {
            return 0;
        }

        $columns = static::getSearchColumns($pIds, $onlyFeatured);

        return static::countBy($columns);
    }

    /**
     * Prepare the columns for the count- and find-queries on multiple pIDs.
     *
     * @param array $pIds
     * @param ?bool $onlyFeatured
     *
     * @return array
     */
    private static function getSearchColumns(array $pIds, ?bool $onlyFeatured = null): array
    {
        $table = static::$strTable;
        $columns = [];

        // all jobs for the given archive IDs.
        $columns[] = sprintf('%s.pid IN (%s)', $table, implode(',', $pIds));

        // Check the featured-mode
        if (true === $onlyFeatured) {
            $columns[] = $table . '.featured=1';
        } elseif (false === $onlyFeatured) {
            $columns[] = $table . '.featured=0';
        }

        // if not preview mode only show published jobs
        if (false === static::isPreviewMode([])) {
            $time = Date::floorToMinute();

            $columns[] = sprintf(
                '%s.published="1" AND (%s.start="" OR %s.start<=%s) AND (%s.stop="" OR %s.stop>%s)',
                $table, $table, $table, $time, $table, $table, $time
            );
        }

        return $columns;
    }
}
