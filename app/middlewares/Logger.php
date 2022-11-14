<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
require_once './controllers/LoginController.php';
class Logger
{
//     public static function LogOperacion($request, RequestHandler $handler)
//     {
//         // $retorno = $handler($request, $response);

//         // $requestType=$request ->getMethod();
//        // $response=new Response();
//  $response=$handler->handle($request);
//         $mensaje='Despues';
//         $respuesta=['respuesta'=>$mensaje];
//         $response->getBody()->write(json_encode(($respuesta,true));
       
//         return $response;
//     }

public  function __invoke($request, RequestHandler $handler):Response
{

  $method = $request->getMethod();

  $response = new Response();

  $parametros = $request->getParsedBody();

  $usuario = $parametros['usuario'];
  $clave = $parametros['clave'];

  $Usuario=UsuarioController::obtenerUsuario($usuario);
   
  
    //$Usuario=UsuarioController::obtenerUsuario($data["usuario"]);
 
     if (isset($Usuario) && $Usuario) {
       
      $response->getBody()->write(json_encode(["Mensaje" => "Usuario tiene rol ".$Usuario->tipo_perfil]));
      $response = $handler->handle($request);

     } else {
     
      $response->getBody()->write(json_encode(["Error" => "403"]));
      $response = $response->withStatus(403);
     }


  return $response;
}
}