<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
//require_once './controllers/LoginController.php';
class JWTMiddleware
{
    public  function __invoke($request, RequestHandler $handler):Response
    {
        $header=$request->getHeaderLine['Authorization'];
        $token=trim(explode("Bearer",$header)[1]);
        $response=new Response();
        $esValido=false;

        try{
            AutentificadorJWT::VerificarToken($token);
            $esValido=true;
            $response=$handler->handle($request);
        }
        catch(Exception $err)
        {
            $payload=json_encode(array("error"=>$err->getMessage()));
        }
        if($esValido)
        {
            $payload=json_encode(array("Valid"=>$esValido));
        }
        $response->getBody()->write($payload);
        return $response;
    }
}
?>