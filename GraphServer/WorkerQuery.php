<?php

namespace GraphServer;

use GraphServer\Base\WorkerQuery as BaseWorkerQuery;
use GraphServer\Map\WorkerTableMap;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'worker' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class WorkerQuery extends BaseWorkerQuery
{
    public function filterRetired($threshold = 7200) {
        $dead_ts = time() - $threshold;
        return $this->filterByState(WorkerTableMap::COL_STATE_IN_PROGRESS)
            ->filterByCreatedTs($dead_ts, Criteria::LESS_THAN);
    }

    public static function GetStateValue($queryType) {
        switch ($queryType) {
            case WorkerTableMap::COL_STATE_IN_PROGRESS:
                return 0;
            case WorkerTableMap::COL_STATE_DONE:
                return 1;
            case WorkerTableMap::COL_STATE_DEAD:
                return 2;
        }
        throw new \InvalidArgumentException("Invalid query type");
    }

    public function kill() {
        return parent::update(array(
            'ClosedTs' => time(),
            'State' => WorkerQuery::GetStateValue(WorkerTableMap::COL_STATE_DEAD)
        ));
    }
}
