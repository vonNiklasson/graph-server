<?php

namespace GraphServer;

use GraphServer\Base\Worker as BaseWorker;
use GraphServer\Map\WorkerDataTableMap;
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
        $this->setUpdateTs(time());

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

        $this->setUpdateTs(time());

        return parent::preUpdate($con);
    }

    public function setNodeData($nodes) {
        $wd = $this->getNodeData();
        if (!isset($wd)) {
            $wd = new WorkerData();
            $wd->setDataType(WorkerDataTableMap::COL_DATA_TYPE_NODES);
            $wd->setWorker($this);
        }
        $wd->setData($nodes);
        $wd->save();
    }

    public function setEdgeData($edges) {
        $wd = $this->getEdgeData();
        if (!isset($wd)) {
            $wd = new WorkerData();
            $wd->setDataType(WorkerDataTableMap::COL_DATA_TYPE_EDGES);
            $wd->setWorker($this);
        }
        $wd->setData($edges);
        $wd->save();
    }

    public function setEccentricityData($eccentricities) {
        $wd = $this->getEccentricityData();
        if (!isset($wd)) {
            $wd = new WorkerData();
            $wd->setDataType(WorkerDataTableMap::COL_DATA_TYPE_ECCENTRICITIES);
            $wd->setWorker($this);
        }
        $wd->setData($eccentricities);
        $wd->save();
    }

    public function setCustomData($extra_data) {
        $wd = $this->getCustomData();
        if (!isset($wd)) {
            $wd = new WorkerData();
            $wd->setDataType(WorkerDataTableMap::COL_DATA_TYPE_CUSTOM);
            $wd->setWorker($this);
        }
        $wd->setData($extra_data);
        $wd->save();
    }


    public function getNodeData($str = false) {
        $data = WorkerDataQuery::create()
            ->filterByWorker($this)
            ->filterByDataType(WorkerDataTableMap::COL_DATA_TYPE_NODES)
            ->findOne();
        if ($str) {
            return (isset($data)) ? $data->getData() : '';
        }
        return $data;
    }

    public function getEdgeData($str = false) {
        $data = WorkerDataQuery::create()
            ->filterByWorker($this)
            ->filterByDataType(WorkerDataTableMap::COL_DATA_TYPE_EDGES)
            ->findOne();
        if ($str) {
            return (isset($data)) ? $data->getData() : '';
        }
        return $data;
    }

    public function getEccentricityData($str = false) {
        $data = WorkerDataQuery::create()
            ->filterByWorker($this)
            ->filterByDataType(WorkerDataTableMap::COL_DATA_TYPE_ECCENTRICITIES)
            ->findOne();
        if ($str) {
            return (isset($data)) ? $data->getData() : '';
        }
        return $data;
    }

    public function getCustomData($str = false) {
        $data = WorkerDataQuery::create()
            ->filterByWorker($this)
            ->filterByDataType(WorkerDataTableMap::COL_DATA_TYPE_CUSTOM)
            ->findOne();
        if ($str) {
            return (isset($data)) ? $data->getData() : '';
        }
        return $data;
    }
}
