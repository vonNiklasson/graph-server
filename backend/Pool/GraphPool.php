<?php


namespace Pool;

use GraphServer\Pool;
use GraphServer\PoolQuery;
use GraphServer\Worker;


class GraphPool {

    public function __construct() {

    }

    public function GetNewThread($optimization = Pool::DEFAULT_OPTIMIZATION) {
        $poolQuery = PoolQuery::create()->filterByOptimization($optimization)->orderByProgress()->getActive()->find();
        if ($poolQuery->count() == 0) {
            throw new \Exception('No workers in pool.');
        }

        $pool = $poolQuery->getFirst();
        $graph = new Worker();
        $graph->setPool($pool);
        $graph->save();

        return $graph;
    }
}