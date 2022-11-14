<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
 require_once './middlewares/Logger.php';
 require_once './middlewares/ManejoTokenMiddleware.php';

 require_once './middlewares/EntradaMiddleware.php';

 require_once './middlewares/SalidaMiddleware.php';

 require_once './models/AutentificadorJWT.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/LoginController.php';




// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();
//$app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->post('/{id}', \UsuarioController::class . ':ModificarUno');
    $group->delete('/{id}', \UsuarioController::class . ':BorrarUno');//la request se arma en json ej: {"id":4}

  })->add(new Logger());

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP- Hola");
    return $response;

}) ->add(new EntradaMiddleware())->add(new SalidaMiddleware());
// Hacer un login para nuestra aplicacion;
// para esto vamos a necesitar añadir una columna mas a la tabla usuarios que ya tenemos en nuestro localhost
// tipo_perfil
// [empleado, cliente, admin]
// vamos a utilizar un middleware para poder chequear los perfiles de usuario en los request necesarios
// '/login' ese no deberia tener un middleware de proteccion
// por POST usuario y clave
// es usuario franco con perfil admin ingresó en la aplicacion

$app->post('/Login', LoginController::class . ':VerificarUsuario');

// JWT test routes
$app->group('/jwt', function (RouteCollectorProxy $group) {

  $group->post('/crearToken', function (Request $request, Response $response) {    
   
    return $response;
  }) ->add(new ManejoTokenMiddleware());
//     $parametros = $request->getParsedBody();
// //var_dump($parametros);
//     $usuario = $parametros['usuario'];
//     $perfil = $parametros['perfil'];
//     $alias = $parametros['alias'];

//     $datos = array('usuario' => $usuario, 'perfil' => $perfil, 'alias' => $alias);

//     $token = AutentificadorJWT::CrearToken($datos);
//     $payload = json_encode(array('jwt' => $token));

//     $response->getBody()->write($payload);
//     return $response
//       ->withHeader('Content-Type', 'application/json');
//   });

  $group->get('/devolverPayLoad', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/devolverDatos', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('datos' => AutentificadorJWT::ObtenerData($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/verificarToken', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $esValido = false;

    try {
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $payload = json_encode(array('valid' => $esValido));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });
});

$app->run();



// pm.environment.set("access_token", pm.response.json());