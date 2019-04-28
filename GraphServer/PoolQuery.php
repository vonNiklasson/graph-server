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
        return parent::withColumn('completed_count+in_progress_count', 'current_count')
            ->orderBy('current_count', $order);
    }

    public function getActive() {
        return parent::withColumn('completed_count+in_progress_count', 'current_count')
            ->where('active = "true" and (current_count < max_count or max_count = 0)');

    }
}
