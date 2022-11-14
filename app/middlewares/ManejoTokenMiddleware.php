<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class ManejoTokenMiddleware
{
    public  function __invoke($request, RequestHandler $handler):Response
    {
        $response = new Response();
    $parametros = $request->getParsedBody();

    $usuario = $parametros['usuario'];
    $perfil = $parametros['perfil'];
    //$alias = $parametros['alias'];

    $datos = array('usuario' => $usuario, 'perfil' => $perfil);

    $token = AutentificadorJWT::CrearToken($datos);
    $payload = json_encode(array('jwt' => $token));

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');

    }
}
?>