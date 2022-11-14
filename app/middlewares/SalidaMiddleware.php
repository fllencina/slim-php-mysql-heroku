<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class SalidaMiddleware
{
    public  function __invoke($request, RequestHandler $handler):Response
    {
      
        $response=$handler->handle($request);

        $mensaje='Despues';
        $respuesta=['respuesta'=>$mensaje];
        $response->getBody()->write(json_encode($respuesta,true));
       
        return $response;
    }
}