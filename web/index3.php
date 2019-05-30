<?php

require('../vendor/autoload.php');

$app = new Silex\Application();
$app['debug'] = true;

// Register the monolog logging service
$app->register(new Silex\Provider\MonologServiceProvider(), array(
  'monolog.logfile' => 'php://stderr',
));

// Register view rendering
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// Our web handlers

$app->get('/', function() use($app) {
  $app['monolog']->addDebug('logging output.');
  return $app['twig']->render('index.twig');
});

$dbopts = parse_url(getenv('DATABASE_URL'));
$app->register(new Csanquer\Silex\PdoServiceProvider\Provider\PDOServiceProvider('pdo'),
               array(
                'pdo.server' => array(
                   'driver'   => 'pgsql',
                   'user' => $dbopts["user"],
                   'password' => $dbopts["pass"],
                   'host' => $dbopts["host"],
                   'port' => $dbopts["port"],
                   'dbname' => ltrim($dbopts["path"],'/')
                   )
               )
);


$app->get('/db/', function() use($app) {
  $st = $app['pdo']->prepare('SELECT id,tarea FROM tareas');
  $st->execute();

  
  $id = array();
  while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
    $app['monolog']->addDebug('Row ' . $row['id,tarea']);
    $id[] = $row;
  }
  return $app['twig']->render('database.twig', array(
    'id' => $id));
});


$app->get('/db/$id',function() use($app)){

  $command = " DELETE FROM tareas WHERE Id=:id LIMIT 1"; 
  $stmt = $dbh ->prepare($command); 
  $stmt->bindParam(':id', $Id, PDO::PARAM_INT); $stmt->execute();
}

$app->run();

 