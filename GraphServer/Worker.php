<?php

namespace GraphServer;

use GraphServer\Base\Worker as BaseWorker;
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
        parent::setNodeCount($v->getNodeCount());
        parent::setOptimization($v->getOptimization());

        return parent::setPool($v);
    }

    public function preInsert(ConnectionInterface $con = null) {
        parent::setCreatedTs(time());
        return parent::preInsert($con);
    }
}
