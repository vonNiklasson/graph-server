<?php


namespace Pool;

use GraphServer\Map\PoolTableMap;
use GraphServer\Map\WorkerTableMap;
use GraphServer\Pool;
use GraphServer\PoolQuery;
use GraphServer\Worker;
use GraphServer\WorkerData;
use GraphServer\WorkerQuery;
use Propel\Runtime\ActiveQuery\Criteria;


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
        $pool->save();

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


        if (array_key_exists('Nodes', $body)) {
            $worker->addNodeData(json_encode($body['Nodes']));
        }

        if (array_key_exists('Edges', $body)) {
            $worker->addEdgeData(json_encode($body['Edges']));
        }

        if (array_key_exists('Eccentricities', $body)) {
            $worker->addEccentricityData(json_encode($body['Eccentricities']));
        }

        if ($worker->getState() != WorkerTableMap::COL_STATE_DONE) {
            $worker->setState(WorkerTableMap::COL_STATE_DONE);

            $pool = $worker->getPool();
            $pool->setCompletedCount($pool->getCompletedCount() + 1);

            // If the worker was not dead, decrease the progress count by one
            if ($worker->getState() != WorkerTableMap::COL_STATE_DEAD) {
                $pool->setInProgressCount($pool->getInProgressCount() - 1);
            }

            $pool->save();
        }

        $worker->save();

        return $worker;
    }

    public static function GetWorkerResults(string $workerName) {
        $workerResults = WorkerQuery::create()->filterByWorkerName($workerName)->orderByCreatedTs()->find();
        return $workerResults;
    }

    public static function UpdateWorkers(string $workerName = null, $threshold = 7200) {
        $workerQuery = WorkerQuery::create()->filterRetired($threshold);
        if ($workerName != null) {
            $workerQuery = $workerQuery->filterByWorkerName($workerName);
        }
        $workerQuery->kill();
    }

    public static function UpdatePools() {
        $c_progress = new Criteria();
        $c_progress->add(
            WorkerTableMap::COL_STATE,
            WorkerQuery::GetStateValue(WorkerTableMap::COL_STATE_IN_PROGRESS)
        );

        $c_completed = new Criteria();
        $c_completed->add(
            WorkerTableMap::COL_STATE,
            WorkerQuery::GetStateValue(WorkerTableMap::COL_STATE_DONE)
        );

        $c_dead = new Criteria();
        $c_dead->add(
            WorkerTableMap::COL_STATE,
            WorkerQuery::GetStateValue(WorkerTableMap::COL_STATE_DEAD)
        );

        $pools = PoolQuery::create()->leftJoinWithWorkers()->find();
        foreach ($pools as $pool) {
            $pool->setInProgressCount($pool->countWorkerss($c_progress));
            $pool->setCompletedCount($pool->countWorkerss($c_completed));
            $pool->setDeadCount($pool->countWorkerss($c_dead));
            $pool->save();
        }
    }
}