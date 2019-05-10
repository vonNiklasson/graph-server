<?php

exit();

use GraphServer\Map\WorkerTableMap;
use GraphServer\PoolQuery;
use GraphServer\WorkerQuery;

require_once(__DIR__ . '/../backend/config.php');

$pools = PoolQuery::create()
    ->filterBySolveType('sfield')
    ->orderByNodeCount()
    ->find();

ob_end_flush();
foreach ($pools as $oldPool) {

    # Get all the old workers
    $oldWorkers = WorkerQuery::create()
        ->filterByState(WorkerTableMap::COL_STATE_DONE)
        ->filterByPool($oldPool)
        ->find();

    # Get the new related pool
    $newPool = PoolQuery::create()
        ->filterBySolveType('sfield_fr')
        ->filterByNodeCount($oldPool->getNodeCount())
        ->findOne();

    echo 'Copying workers from pool ' . $oldPool->getId() . ' to ' . $newPool->getId() . ' with ' . $oldPool->getNodeCount() . " nodes";
    flush();

    # Iterate over all the old workers
    foreach ($oldWorkers as $oldWorker) {
        # Create a new worker from the old one
        $newWorker = $oldWorker->copy();

        $newWorker->setEdgeData($oldWorker->getEdgeData(true));
        $newWorker->setNodeData($oldWorker->getNodeData(true));
        $newWorker->setEccentricityData($oldWorker->getEccentricityData(true));
        $newWorker->setCustomData('{}');
        $newWorker->setPool($newPool);
        $newWorker->save();
    }

    echo " - Done<br />\r\n";
    flush();
}
ob_start();
