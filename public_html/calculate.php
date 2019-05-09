<?php

use GraphServer\Map\WorkerDataTableMap;
use GraphServer\Map\WorkerTableMap;
use GraphServer\PoolQuery;
use GraphServer\WorkerDataQuery;
use GraphServer\WorkerQuery;
use Propel\Runtime\ActiveQuery\Criteria;

require_once(__DIR__ . '/../backend/config.php');


class CalcObject {
    private $highest;
    private $lowest;

    private $median;
    private $average;

    private $total;
    private $count;

    private $values;

    function __construct() {
        $this->highest = null;
        $this->lowest = null;

        $this->median = null;
        $this->average = null;

        $this->values = array();
    }

    public function add($value) {
        if (!isset($this->highest) || $this->highest < $value) {
            $this->highest = $value;
        }
        if (!isset($this->lowest) || $value < $this->lowest) {
            $this->lowest = $value;
        }

        array_push($this->values, $value);

        $this->total += $value;
        $this->count++;
    }

    public function getAverage() {
        return ($this->total / $this->count);
    }

    public function getMedian() {
        sort($this->values);
        $middle = (int)count($this->values) / 2;
        return $this->values[$middle];
    }

    public function getHighest() {
        return $this->highest;
    }

    public function getLowest() {
        return $this->lowest;
    }

    public function toObject() {
        return array(
            'max' => $this->getHighest(),
            'min' => $this->getLowest(),
            'avg' => $this->getAverage(),
            'med' => $this->getMedian()
        );
    }
}


$pools = PoolQuery::create()
    ->filterByActive(true)
    ->find();

ob_end_flush();
foreach ($pools as $pool) {
    $edge_count = new CalcObject();
    $convergence_rate = new CalcObject();
    $edge_cost = new CalcObject();
    $energy = new CalcObject();
    $diameter = new CalcObject();
    $avg_eccentricity = new CalcObject();

    $units_covered = new CalcObject();
    $units_uncovered = new CalcObject();
    $units_overlap = new CalcObject();
    $units_overlap_acc = new CalcObject();
    $avg_detection_speed = new CalcObject();
    $coverage = new CalcObject();

    $workers = WorkerQuery::create()
        ->filterByState(WorkerTableMap::COL_STATE_DONE)
        ->filterByPool($pool)
        ->leftJoinWithData()
        ->find();

    echo 'Calculating pool ' . $pool->getId() . ' with ' . $workers->count() . " results";

    foreach ($workers as $worker) {
        $edge_count->add(       $worker->getEdgeCount());
        $convergence_rate->add( $worker->getConvergenceRate());
        $edge_cost->add(        $worker->getEdgeCost());
        $energy->add(           $worker->getEnergyCost());
        $diameter->add(         $worker->getDiameter());
        $avg_eccentricity->add( $worker->getAverageEccentricity());

        $customData = WorkerDataQuery::create()->filterByWorker($worker)
            ->filterByDataType(WorkerDataTableMap::COL_DATA_TYPE_CUSTOM)->findOne();

        if (isset($customData)) {
            $raw_custom_data = $customData->getData();
            $jc = json_decode($raw_custom_data);

            if (isset($jc->covered_units)) $units_covered->add($jc->covered_units);
            if (isset($jc->uncovered_units)) $units_uncovered->add($jc->uncovered_units);
            if (isset($jc->overlapping_units)) $units_overlap->add($jc->overlapping_units);
            if (isset($jc->overlapping_units_accumulated)) $units_overlap_acc->add($jc->overlapping_units_accumulated);
            if (isset($jc->average_detection_speed)) $avg_detection_speed->add($jc->average_detection_speed);
            if (isset($jc->coverage)) $coverage->add($jc->coverage);
        }

    }

    $results = array(
        'edge_count' => $edge_count->toObject(),
        'convergence_rate' => $convergence_rate->toObject(),
        'edge_cost' => $edge_cost->toObject(),
        'energy' => $energy->toObject(),
        'diameter' => $diameter->toObject(),
        'avg_eccentricity' => $avg_eccentricity->toObject(),

        'units_covered' => $units_covered->toObject(),
        'units_uncovered' => $units_uncovered->toObject(),
        'units_overlap' => $units_overlap->toObject(),
        'units_overlap_acc' => $units_overlap_acc->toObject(),
        'avg_detection_speed' => $avg_detection_speed->toObject(),
        'coverage' => $coverage->toObject()
    );

    $pool->setResults(
        json_encode($results)
    );
    $pool->save();

    echo " - Done<br />\r\n";
}
ob_start();
