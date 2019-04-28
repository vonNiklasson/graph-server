<?php

namespace GraphServer;

use GraphServer\Base\PoolQuery as BasePoolQuery;
use Propel\Runtime\ActiveQuery\Criteria;

/**
 * Skeleton subclass for performing query and update operations on the 'pool' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class PoolQuery extends BasePoolQuery
{
    /**
     * Sorts the query on the current worker count.
     *
     * @param string $order
     * @return BasePoolQuery|\Propel\Runtime\ActiveQuery\ModelCriteria
     */
    public function orderByCurrentCount($order = Criteria::ASC) {
        return parent::addAscendingOrderByColumn('completed_count+in_progress_count');
    }

    public function getActive() {
        return parent::where('active = 1 and (completed_count+in_progress_count < max_count or max_count = 0)');

    }
}
