<?php
require_once './models/Usuario.php';
class LoginController extends Usuario
{
    public static function VerificarUsuario($request, $response, $args)
    {
        $datos=$request->getParsedBody();
        
        $UsuarioLogin=Usuario::obtenerUsuario($datos['usuario']);
       
        //$response= new Response();
        if(password_verify($datos['clave'], $UsuarioLogin->clave)){
            $token = AutentificadorJWT::CrearToken($datos);
            $payload = json_encode(array('jwt' => $token));
            //$payload = json_encode(array("mensaje" => "Usuario Logueado"));
        }
        else{
            $payload = json_encode(array("mensaje" => "No coincide la clave"));

        }
        $response->getBody()->write($payload);
        return $response ->withHeader('Content-Type', 'application/json');
    }
}


// $header = $request->getHeaderLine('authorization');
// $token = trim(explode("Bearer", $header)[1]);


?>