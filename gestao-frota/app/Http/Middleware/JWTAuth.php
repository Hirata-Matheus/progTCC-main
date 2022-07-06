<?php
namespace App\Http\Middleware;
use \App\Model\Entity\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTAuth{

    private function getJWTAuthUser($request){
        $headers = $request->getHeaders();
        
        // token puro 
        $jwt = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : '';

        $key = getenv('JWT_KEY');
        try {
            // decode 
            $decode = (array)JWT::decode($jwt, new Key($key, 'HS256'));
        } catch (\Exception $e) {
            throw new \Exception("Token Inválido", 403);
        }   
        
        
        // email
        $email = $decode['email'] ?? '';   

        // busca o usuario pelo email
        $obUser = User::getUserByEmail($email);

        return $obUser instanceof User ? $obUser : false;
            
    }

    private function auth($request){
        // verifica o usuario recebido
        if ($obUser = $this->getJWTAuthUser($request)) {
            $request->user = $obUser;
            return true;
        }

        // emite o erro de senha inválida
        throw new \Exception("Acesso negado", 403);
        
    }

    public function handle($request, $next) {
        $this->auth($request);

        return $next($request);
    }
}