<?php

namespace GraphServer;

use GraphServer\Base\Pool as BasePool;

/**
 * Skeleton subclass for representing a row from the 'pool' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Pool extends BasePool
{
    public const DEFAULT_OPTIMIZATION = 'combined';

    /**
     * Checks whether the pool has reached the desired number of results.
     *
     * @return bool
     */
    public function isActive() {
        if ($this->getMaxCount() > 0) {
            $current_count = $this->getCompletedCount() + $this->getInProgressCount();
            if ($current_count >= $this->getMaxCount()) {
                return false;
            }
        }
        return parent::isActive(); // TODO: Change the autogenerated stub
    }

}