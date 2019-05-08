<?php

use GraphServer\Map\WorkerDataTableMap;
use GraphServer\Map\WorkerTableMap;
use GraphServer\PoolQuery;
use GraphServer\WorkerDataQuery;
use GraphServer\WorkerQuery;
use Propel\Runtime\ActiveQuery\Criteria;

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
        <?php

        $workers_last_hour = WorkerQuery::create()
            ->filterByState(WorkerTableMap::COL_STATE_DONE)
            ->filterByClosedTs(time()-3600, Criteria::GREATER_EQUAL)
            ->count();

        $workers_last_hour_6 = WorkerQuery::create()
            ->filterByState(WorkerTableMap::COL_STATE_DONE)
            ->filterByClosedTs(time()-3600*6, Criteria::GREATER_EQUAL)
            ->count();

        ?>
      <p>
          <strong><?= round($workers_last_hour/60) ?></strong> graphs / minute<br />
          <strong><?= $workers_last_hour ?></strong> graphs / hour<br />
          <strong><?= $workers_last_hour_6 ?></strong> graphs / six hours
      </p>
    </div>
</div>

<div class="container">
    <h2>Static area</h2>
    <table class="table table-striped table-hover table-sm">
        <thead class="thead-dark">
            <tr>
              <th scope="col">#</th>
              <th scope="col">Name</th>
              <th scope="col">Nodes</th>
              <th scope="col">Completed</th>
              <th scope="col">In progress</th>
              <th scope="col">Max count</th>
            </tr>
        </thead>
        <tbody>
        <?php
          $total_completed = 0;
          $total_in_progress = 0;

            $pools = PoolQuery::create()->orderByNodeCount()->filterByOptimization('combined')->filterByActive(true)->find();
            foreach ($pools as $pool) {
                if ($pool->getInProgressCount() > 0) {
                    echo '<tr class="table-primary">';
                } elseif ($pool->getMaxCount() != 0 && $pool->getCompletedCount() < $pool->getMaxCount()) {
                    echo '<tr class="table-success">';
                } else {
                    echo '<tr>';
                }
                echo '<th scope="row">' . $pool->getId() . '</th>';
                echo '<td>' . $pool->getName() . '</td>';
                echo '<td>' . $pool->getNodeCount() . '</td>';
                echo '<td>' . $pool->getCompletedCount() . '</td>';
                echo '<td>' . $pool->getInProgressCount() . '</td>';
                echo '<td>';
                if ($pool->getMaxCount() != 0) {
                    $maxCountPercentage = round($pool->getCompletedCount() / $pool->getMaxCount() * 100);
                    echo $pool->getMaxCount() . ' (' . $maxCountPercentage . '%)';
                } else {
                    echo '-';
                }
                echo '</td>';
                echo '</tr>';

                $total_completed += $pool->getCompletedCount();
                $total_in_progress += $pool->getInProgressCount();
            }
            echo '<thead class="thead-light"><tr>';

                echo '<th></th>';
                echo '<th></th>';
                echo '<th></th>';

                echo '<th>' . $total_completed . '</th>';
                echo '<th>' . $total_in_progress . '</th>';

                echo '<th></th>';

            echo '</tr></thead>';
        ?>

        </tbody>
    </table>



  <h3>Latest workers</h3>
  <table class="table table-striped table-hover table-sm">
    <thead class="thead-dark">
    <tr>
      <th scope="col">#</th>
      <th scope="col">Created</th>
      <th scope="col">Client</th>
      <th scope="col">Name</th>
      <th scope="col">Nodes</th>
      <th scope="col">Coverage</th>
      <th scope="col">State</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $workers = WorkerQuery::create()
        ->filterByOptimization('combined')
        ->orderByCreatedTs(Criteria::DESC)
        ->innerJoinWithPool()
        ->limit(100)->find();

    foreach ($workers as $worker) {
        $pool = $worker->getPool();
        if ($worker->getState() == WorkerTableMap::COL_STATE_IN_PROGRESS) {
            echo '<tr class="table-primary">';
        } elseif ($worker->getState() == WorkerTableMap::COL_STATE_DONE) {
            echo '<tr class="table-success">';
        } elseif ($worker->getState() == WorkerTableMap::COL_STATE_DEAD) {
            echo '<tr class="table-danger">';
        }
        echo '<th scope="row">' . $worker->getId() . '</th>';
        echo '<td>' . date('H:i:s', $worker->getCreatedTs() + 7200) . '</td>';
        echo '<td>' . $worker->getWorkerName() . '</td>';
        echo '<td>' . $pool->getName() . '</td>';
        echo '<td>' . $pool->getNodeCount() . '</td>';
        $customData = WorkerDataQuery::create()->filterByWorker($worker)
            ->filterByDataType(WorkerDataTableMap::COL_DATA_TYPE_CUSTOM)->findOne();
        if (isset($customData)) {
            $raw_custom_data = $customData->getData();
            $json_custom = json_decode($raw_custom_data);
            if (isset($json_custom->coverage)) {
                echo '<td>' . round((float)$json_custom->coverage * 100 ) . '%' . '</td>';
            } else {
                echo '<td>-</td>';
            }
        } else {
            echo '<td>-</td>';
        }
        echo '<td>';
        if ($worker->getState() == WorkerTableMap::COL_STATE_IN_PROGRESS) {
            $delta = time() - $worker->getCreatedTs();
            echo 'In progress';
            echo ' (' . date('H:i:s', $delta) . ')';
        } elseif ($worker->getState() == WorkerTableMap::COL_STATE_DONE) {
            $delta = $worker->getClosedTs() - $worker->getCreatedTs();
            echo 'Finished';
            echo ' (' . date('H:i:s', $delta) . ')';
        } elseif ($worker->getState() == WorkerTableMap::COL_STATE_DEAD) {
            echo 'Dead';
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
