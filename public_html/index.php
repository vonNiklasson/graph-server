<?php

use GraphServer\PoolQuery;

require_once(__DIR__ . '/../backend/config.php');

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
        <h1 class="display-4">Graph Server Status</h1>
        <p class="lead">This page shows the current pool status for the graph server.</p>
    </div>
</div>

<div class="container">
    <h2>Combined optimization</h2>
    <table class="table table-striped table-hover table-sm">
        <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nodes</th>
                <th scope="col">Completed</th>
                <th scope="col">In progress</th>
                <th scope="col">Dead</th>
                <th scope="col">Max count</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $pools = PoolQuery::create()->orderByNodeCount()->filterByOptimization('combined')->filterByActive(true)->find();
            foreach ($pools as $pool) {
                $maxCountPercentage = round($pool->getCompletedCount() / $pool->getMaxCount() * 100);
                echo '<tr>';
                    echo '<th scope="row">' . $pool->getId() . '</th>';
                    echo '<td>' . $pool->getNodeCount() . '</td>';
                    echo '<td>' . $pool->getCompletedCount() . '</td>';
                    echo '<td>' . $pool->getInProgressCount() . '</td>';
                    echo '<td>' . $pool->getDeadCount() . '</td>';
                    echo '<td>' . $pool->getMaxCount();
                    if ($pool->getMaxCount() != 0) {
                        echo ' (' . $maxCountPercentage . '%)';
                    }
                    echo '</td>';
                echo '</tr>';
            }
        ?>

        </tbody>
    </table>
</div>


    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js" integrity="sha384-xrRywqdh3PHs8keKZN+8zzc5TX0GRTLCcmivcbNJWm2rs5C8PRhcEn3czEjhAO9o" crossorigin="anonymous"></script>
</body>
</html>
