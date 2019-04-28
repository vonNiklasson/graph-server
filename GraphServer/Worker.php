<?php

namespace GraphServer;

use GraphServer\Base\Worker as BaseWorker;
use GraphServer\Map\WorkerTableMap;
use GraphServer\Pool as ChildPool;
use Propel\Runtime\Connection\ConnectionInterface;

/**
 * Skeleton subclass for representing a row from the 'worker' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Worker extends BaseWorker
{
    public function setPool(ChildPool $v = null) {
        $this->setNodeCount($v->getNodeCount());
        $this->setOptimization($v->getOptimization());

        return parent::setPool($v);
    }

    public function preInsert(ConnectionInterface $con = null) {
        $this->setCreatedTs(time());
        return parent::preInsert($con);
    }

    public function preUpdate(ConnectionInterface $con = null) {
        if ($this->isColumnModified(WorkerTableMap::COL_STATE)) {
            if ($this->getState() == WorkerTableMap::COL_STATE_DONE ||
                $this->getState() == WorkerTableMap::COL_STATE_DEAD) {
                $this->setClosedTs(time());
            } elseif ($this->getState() == WorkerTableMap::COL_STATE_IN_PROGRESS) {
                $this->setClosedTs(null);
            }
        }

        return parent::preUpdate($con);
    }
}
