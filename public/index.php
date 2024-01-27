<?php
use Psr\Http\Message\ResponseInterface as Response; 
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use DI\Container;

require __DIR__ . '/../vendor/autoload.php'; 

$container=new Container(); 
$container->set('templating', function(){ 
    return new Mustache_Engine([
        'loader' => new Mustache_Loader_FilesystemLoader(
            __DIR__.'/../templates',
            ['extension' => '']
        )
        ]);
});
$container->set('session', function(){
    return new \SlimSession\Helper();
});

AppFactory::setContainer($container);

$app = AppFactory::create();

$app->add(new \Slim\Middleware\Session);

$app->get('/', '\App\Controller\FirstController:homepage');

$app->get('/p1/jane', function (Request $request, Response $response){
    $response->getBody()->write("Hello, Jane!");
    return $response;
});

$app->get('/p2/{name}', function(Request $request, Response $response, array $args){
    $name=ucfirst($args['name']);
    $response->getBody()->write(sprintf("Hello, %s!", $name));
    return $response;
});
$app->get('/p3/{name}', function(Request $request, Response $response, array $args =[]){
    $html = $this->get('templating')->render('hello.html',[
        'name'=>$args['name']
    ]);
    $response->getBody()->write($html);
    return $response;
});
$app->get('/p4', '\App\Controller\SecondController:hello');
$app->get('/p5', '\App\Controller\SearchController:default');
$app->get('/p6', '\App\Controller\SearchController:search');
$app->get('/p7', '\App\Controller\ApiController:search');
$app->any('/login', '\App\Controller\AuthController:login');
$app->any('/logout', '\App\Controller\AuthController:logout');
$app->group('/secure', function($app){
    $app->any('', '\App\Controller\SecureController:member');
    $app->any('/status', '\App\Controller\SecureController:status');
})->add(new \App\Middleware\Authenticate($app->getContainer()->get('session')));

$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$errorMiddleware->setErrorHandler(
    Slim\Exception\HttpNotFoundException::class,
    function (Psr\Http\Message\ServerRequestInterface $request) use ($container) {
        $controller = new App\Controller\ExceptionController($container); 
        return $controller->notFound($request);
    });

$app->run();