<?php


namespace Pool;

use GraphServer\Map\WorkerTableMap;
use GraphServer\Pool;
use GraphServer\PoolQuery;
use GraphServer\Worker;
use GraphServer\WorkerData;
use GraphServer\WorkerQuery;


class PoolWrapper {

    public function __construct() {

    }

    /**
     * Creates a new worker instance based on which graph type is most needed and returns it.
     *
     * @param string $workerName
     * @return Worker
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public static function GetNewThread(string $workerName = null) {
        // Get the active workers
        $pools = PoolQuery::create()->getActive()->orderByCurrentCount()->find();

        // If no workers have any tasks, throw an error
        if ($pools->count() == 0) {
            throw new \Exception('No workers in pool.');
        }

        // Otherwise, create a new worker and return it
        /** @var Pool $pool */
        $pool = $pools->getFirst();

        $worker = new Worker();
        $worker->setWorkerName(strtolower($workerName));
        $worker->setPool($pool);
        $worker->save();

        $pool->setInProgressCount($pool->getInProgressCount() + 1);

        return $worker;
    }

    public static function AddWorkerData(int $workerId, $body) {
        $worker = WorkerQuery::create()->findPk($workerId);

        if (array_key_exists('EdgeCount', $body)) {
            $worker->setEdgeCount($body['EdgeCount']);
        }

        if (array_key_exists('ConvergenceRate', $body)) {
            $worker->setConvergenceRate($body['ConvergenceRate']);
        }

        if (array_key_exists('EnergyCost', $body)) {
            $worker->setEnergyCost($body['EnergyCost']);
        }

        if (array_key_exists('EdgeCost', $body)) {
            $worker->setEdgeCost($body['EdgeCost']);
        }

        if (array_key_exists('Diameter', $body)) {
            $worker->setDiameter($body['Diameter']);
        }

        if (array_key_exists('AverageEccentricity', $body)) {
            $worker->setAverageEccentricity($body['AverageEccentricity']);
        }


        if (array_key_exists('nodes', $body)) {
            $nodeData = new WorkerData();
            $nodeData->setData($body['nodes']);
            $worker->setNodes($nodeData);
        }

        if (array_key_exists('edges', $body)) {
            $edgeData = new WorkerData();
            $edgeData->setData($body['edges']);
            $worker->setEdges($edgeData);
        }

        if (array_key_exists('eccentricities', $body)) {
            $eccentricities = new WorkerData();
            $eccentricities->setData($body['eccentricities']);
            $worker->setEccentricities($eccentricities);
        }

        if ($worker->getState() != WorkerTableMap::COL_STATE_DONE) {
            $worker->setState(WorkerTableMap::COL_STATE_DONE);

            $pool = $worker->getPool();
            $pool->setCompletedCount($pool->setCompletedCount() + 1);

            // If the worker was not dead, decrease the progress count by one
            if ($worker->getState() != WorkerTableMap::COL_STATE_DEAD) {
                $pool->setInProgressCount($pool->getInProgressCount() - 1);
            }
        }
        $worker->save();

        return true;
    }

    public static function GetWorkerResults(string $workerName) {
        $workerResults = WorkerQuery::create()->filterByWorkerName($workerName)->orderByCreatedTs()->find();
        return $workerResults;
    }
}