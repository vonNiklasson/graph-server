<?php

use GraphServer\Map\WorkerDataTableMap;
use GraphServer\Map\WorkerTableMap;
use GraphServer\PoolQuery;
use GraphServer\WorkerDataQuery;
use GraphServer\WorkerQuery;
use Propel\Runtime\ActiveQuery\Criteria;

require_once(__DIR__ . '/../backend/config.php');

function print_calc_obj($calc) {
    echo '<td>' . $calc->min . '</td>';
    echo '<td>' . $calc->max . '</td>';
    echo '<td>' . $calc->avg . '</td>';
    echo '<td>' . $calc->med . '</td>';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <title>Graph Server</title>
</head>
<body>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">Graph Results</h1>
        <p class="lead">This page shows the current pool status for the graph server.</p>
    </div>
</div>

<h2>Static area</h2>
<table class="table table-striped table-hover table-sm">
    <thead class="thead-dark">
    <tr>
        <th scope="col" colspan="3"></th>

        <th scope="col" colspan="4">Edge count</th>
        <th scope="col" colspan="4">Convergence rate</th>
        <th scope="col" colspan="4">Edge cost</th>
        <th scope="col" colspan="4">Energy</th>
        <th scope="col" colspan="4">Diameter</th>
        <th scope="col" colspan="4">Avg. eccentricity</th>

        <th scope="col" colspan="4">Units covered</th>
        <th scope="col" colspan="4">Units uncovered</th>
        <th scope="col" colspan="4">Units overlap</th>
        <th scope="col" colspan="4">Units overlap (acc)</th>
        <th scope="col" colspan="4">Avg. detection speed</th>
        <th scope="col" colspan="4">Coverage</th>

    </tr>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Nodes</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

    </tr>
    </thead>
    <tbody>

    <?php
        $pools = PoolQuery::create()
            ->orderByNodeCount()
            ->filterBySolveType('field')
            ->filterByActive(true)
            ->orderByNodeCount()
            ->find();

        foreach ($pools as $pool) {
            $raw_results = $pool->getResults();
            $results = json_decode($raw_results);

            echo '<tr>';
                echo '<th scope="row">' . $pool->getId() . '</th>';
                echo '<td>' . $pool->getName() . '</td>';
                echo '<td>' . $pool->getNodeCount() . '</td>';

                print_calc_obj($results->edge_count);
                print_calc_obj($results->convergence_rate);
                print_calc_obj($results->edge_cost);
                print_calc_obj($results->energy);
                print_calc_obj($results->diameter);
                print_calc_obj($results->avg_eccentricity);

                print_calc_obj($results->units_covered);
                print_calc_obj($results->units_uncovered);
                print_calc_obj($results->units_overlap);
                print_calc_obj($results->units_overlap_acc);
                print_calc_obj($results->avg_detection_speed);
                print_calc_obj($results->coverage);
            echo '</tr>';
        }
    ?>
    </tbody>
</table>


<h2>Dynamic area</h2>
<table class="table table-striped table-hover table-sm">
    <thead class="thead-dark">
    <tr>
        <th scope="col" colspan="3"></th>

        <th scope="col" colspan="4">Edge count</th>
        <th scope="col" colspan="4">Convergence rate</th>
        <th scope="col" colspan="4">Edge cost</th>
        <th scope="col" colspan="4">Energy</th>
        <th scope="col" colspan="4">Diameter</th>
        <th scope="col" colspan="4">Avg. eccentricity</th>

        <th scope="col" colspan="4">Units covered</th>
        <th scope="col" colspan="4">Units uncovered</th>
        <th scope="col" colspan="4">Units overlap</th>
        <th scope="col" colspan="4">Units overlap (acc)</th>
        <th scope="col" colspan="4">Avg. detection speed</th>
        <th scope="col" colspan="4">Coverage</th>

    </tr>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Name</th>
        <th scope="col">Nodes</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

        <th scope="col">Min</th>
        <th scope="col">Max</th>
        <th scope="col">Avg.</th>
        <th scope="col">Med.</th>

    </tr>
    </thead>
    <tbody>

    <?php
    $pools = PoolQuery::create()
        ->orderByNodeCount()
        ->filterBySolveType('dfield')
        ->filterByActive(true)
        ->orderByNodeCount()
        ->find();

    foreach ($pools as $pool) {
        $raw_results = $pool->getResults();
        $results = json_decode($raw_results);

        echo '<tr>';
        echo '<th scope="row">' . $pool->getId() . '</th>';
        echo '<td>' . $pool->getName() . '</td>';
        echo '<td>' . $pool->getNodeCount() . '</td>';

        print_calc_obj($results->edge_count);
        print_calc_obj($results->convergence_rate);
        print_calc_obj($results->edge_cost);
        print_calc_obj($results->energy);
        print_calc_obj($results->diameter);
        print_calc_obj($results->avg_eccentricity);

        print_calc_obj($results->units_covered);
        print_calc_obj($results->units_uncovered);
        print_calc_obj($results->units_overlap);
        print_calc_obj($results->units_overlap_acc);
        print_calc_obj($results->avg_detection_speed);
        print_calc_obj($results->coverage);
        echo '</tr>';
    }
    ?>
    </tbody>
</table>


<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
</body>
</html>
