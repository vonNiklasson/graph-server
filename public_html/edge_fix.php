<?php

use GraphServer\Map\WorkerDataTableMap;
use GraphServer\Map\WorkerTableMap;
use GraphServer\WorkerDataQuery;
use GraphServer\WorkerQuery;

require_once(__DIR__ . '/../backend/config.php');



$workers = WorkerQuery::create()
    ->filterByState(WorkerTableMap::COL_STATE_DONE)
    ->leftJoinWithData()
    ->find();


foreach ($workers as $worker) {

    $edgeData = WorkerDataQuery::create()
        ->filterByWorker($worker)
        ->filterByDataType(WorkerDataTableMap::COL_DATA_TYPE_EDGES)
        ->findOne();

    $raw_edges = $edgeData->getData();
    $edge_list = (array)json_decode($raw_edges);

    $edgeCount = array_sum(array_map("count", $edge_list));

    $worker->setEdgeCount($edgeCount);
    $worker->save();
}

echo 'Done';

?>
