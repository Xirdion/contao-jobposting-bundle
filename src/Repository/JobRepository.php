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

class JobRepository extends Model
{
    protected static $strTable = 'tl_job';

    /**
     * Find a specific job by its ID.
     *
     * @param int $id
     *
     * @return JobModel|null
     */
    public static function findById(int $id): ?JobModel
    {
        return self::findByPk($id);
    }

    /**
     * @param string $alias
     * @param array  $pids
     *
     * @return JobModel|null
     */
    public static function findPublishedByAliasAndPids(string $alias, array $pids): ?JobModel
    {
        if (empty($pids)) {
            return null;
        }

        $table = static::$strTable;
        $columns = static::getSearchColumns($pids);
        $columns[] = $table . '.alias=?';

        return static::findOneBy($columns, $alias);
    }

    /**
     * Find all jobs within a given range (limit + offset) and a given order
     * for some specific job archives.
     *
     * @param array  $pids
     * @param int    $limit
     * @param int    $offset
     * @param string $order
     *
     * @return JobModel[]|Model\Collection|null
     */
    public static function findPublishedByPids(array $pids, int $limit = 0, int $offset = 0, string $order = ''): ?Model\Collection
    {
        if (empty($pids)) {
            return null;
        }

        // Prepare the query columns
        $columns = static::getSearchColumns($pids);

        // Prepare the options for the query
        $table = static::$strTable;
        switch ($order) {
            case 'order_date_asc':
                $order = $table . '.dateTime ASC';
                break;
            case 'order_date_desc':
                $order = $table . '.dateTime DESC';
                break;
            case 'order_title_asc':
                $order = $table . '.title ASC';
                break;
            case 'order_title_desc':
                $order = $table . '.title DESC';
                break;
            case 'order_random':
                $order = 'RAND()';
                break;
            default:
                $order = $table . '.dateTime DESC';
        }

        $options = [
            'limit' => $limit,
            'offset' => $offset,
            'order' => $order,
        ];

        return static::findBy($columns, null, $options);
    }

    /**
     * Count all jobs by a given pID.
     *
     * @param int $pid
     *
     * @return int
     */
    public static function countByPid(int $pid): int
    {
        return static::countBy(['pid=?'], [$pid]);
    }

    /**
     * Get the amount of jobs for some job archives.
     *
     * @param array $pids
     *
     * @return int
     */
    public static function countPublishedByPids(array $pids): int
    {
        if (empty($pids)) {
            return 0;
        }

        $columns = static::getSearchColumns($pids);

        return static::countBy($columns);
    }

    /**
     * Try to find the amount of entries with the given alias but not a specific ID.
     *
     * @param int    $id
     * @param string $alias
     *
     * @return int
     */
    public static function checkAlias(int $id, string $alias): int
    {
        return static::countBy(['id != ?', 'alias = ?'], [$id, $alias]);
    }

    /**
     * Prepare the columns for the count- and find-queries on multiple pIDs.
     *
     * @param array $pids
     *
     * @return array
     */
    private static function getSearchColumns(array $pids): array
    {
        $table = static::$strTable;
        $columns = [];

        // all jobs for the given archive IDs.
        $columns[] = sprintf('%s.pid IN (%s)', $table, implode(',', $pids));

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
