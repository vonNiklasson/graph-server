<?php

use GraphServer\WorkerQuery;
use Pool\PoolWrapper;
use Slim\Http\Request;
use Slim\Http\Response;

require_once(__DIR__ . '/../../backend/config.php');

$config['displayErrorDetails'] = true;
//$config['addContentLengthHeader'] = false;

$app = new \Slim\App(["settings" => $config]);


/* Get the worker status */
$app->get('/workers/{workerName}[/]', function(Request $request, Response $response, array $args) {
    $response = $response->withHeader('Content-type', 'application/json');
    $workerName = strtolower($args['workerName']);

    $workerResults = PoolWrapper::GetWorkerResults($workerName);

    $response = $response->withStatus(200);
    $response = $response->withJson($workerResults->toArray());

    return $response;
})->setName('get-worker-status');


/* Getting a new thread */
$app->post('/thread[/]', function(Request $request, Response $response, array $args) {
    $response = $response->withHeader('Content-type', 'application/json');
    $parsedBody = $request->getParsedBody();
    $workerName = $parsedBody['name'] ?? 'anonymous';

    $thread = PoolWrapper::GetNewThread($workerName);

    $response = $response->withStatus(201);
    $response = $response->withJson($thread->toArray());

    return $response;
})->setName('create-thread');


/* Uploading results */
$app->put('/thread/{workerId}[/]', function(Request $request, Response $response, array $args) {
    $response = $response->withHeader('Content-type', 'application/json');
    $workerId = strtolower($args['workerId']);

    $body = $request->getParsedBody();

    $worker = PoolWrapper::AddWorkerData($workerId, $body);

    $response = $response->withStatus(202);
    $response = $response->withJson($worker->toArray());

    return $response;
})->setName('update-thread');


/* Get worker id */
$app->get('/thread/recalc[/]', function(Request $request, Response $response, array $args) {
    $response = $response->withHeader('Content-type', 'application/json');

    $worker = PoolWrapper::GetThreadForRecalculation();

    if ($worker != null) {
        $response = $response->withStatus(200);
    } else {
        $response = $response->withStatus(404);
    }

    return $response->withJson($worker->toArray());
})->setName('get-recalculation-thread');


/* Get worker id */
$app->get('/thread/{workerId}[/]', function(Request $request, Response $response, array $args) {
    $response = $response->withHeader('Content-type', 'application/json');
    $workerId = strtolower($args['workerId']);

    $worker = WorkerQuery::create()->findPk($workerId);

    if ($worker != null) {
        $response = $response->withStatus(200);
    } else {
        $response = $response->withStatus(404);
    }

    return $response->withJson($worker->toArray());
})->setName('view-thread');


/* Closing the old workers */
$app->any('/workers/{workerName}/close[/]', function(Request $request, Response $response, array $args) {
    $response = $response->withHeader('Content-type', 'application/json');
    $workerName = strtolower($args['workerName']);

    PoolWrapper::UpdateWorkers($workerName, 0);
    PoolWrapper::UpdatePools();

    $response = $response->withStatus(202);

    return $response;
})->setName('get-worker-status');


try {
    $app->run();
}
catch (Exception $exception) {
    echo $exception->getMessage();
}
