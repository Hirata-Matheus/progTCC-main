<?php
namespace App\Http\Middleware;
use \App\Model\Entity\User;

class UserBasicAuth{

    private function getBasicAuthUser(){
        if (!isset($_SERVER['PHP_AUTH_USER']) or !isset($_SERVER['PHP_AUTH_PW'])) {
            return false;
        }

        // busca o usuario pelo email
        $obUser = User::getUserByEmail($_SERVER['PHP_AUTH_USER']);
        //print_r($obUser); exit;
        if (!$obUser instanceof User) {
            return false;
        }

        return password_verify($_SERVER['PHP_AUTH_PW'], $obUser->senha) ? $obUser : false;
    }

    private function basicAuth($request){
        // verifica o usuario recebido
        if ($obUser = $this->getBasicAuthUser()) {
            $request->user = $obUser;
            return true;
        }

        // emite o erro de senha inválida
        throw new \Exception("Usuario ou senha inválidos", 403);
        
    }

    public function handle($request, $next) {
        $this->basicAuth($request);

        return $next($request);
    }
}