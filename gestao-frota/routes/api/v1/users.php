<?php
use \App\Http\Response;
use \App\Controller\Api;

//ROTA GET USUÁRIO (LISTAGEM TODOS USUARIOS)
$obRouter->get('/api/v1/users', [
    'middlewares' => [
        'api'
    ],
    function($request){
        return new Response(200, Api\User::getUsers($request), 'application/json');
    }
]);

//ROTA GET USUARIO (POR USUÁRIO)
$obRouter->get('/api/v1/users/{id}', [
    'middlewares' => [
        'api'
    ],
    function($request, $id){
        return new Response(200, Api\User::getUser($request, $id), 'application/json');
    }
]);

//ROTA POST CADASTRO USUÁRIO (CADASTRAR)
$obRouter->post('/api/v1/users', [
    'middlewares' => [
        'api'
    ],
    function($request){
        //RETORNA O MÉTODO RESPONSÁVEL POR CADASTRAR USUÁRIO
        return new Response(201, Api\User::setNewUser($request), 'application/json');
    }
]);

//ROTA PUT CADASTRO USUÁRIO (ALTERAR)
$obRouter->put('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
    ],
    function($request, $id){
        //RETORNA O MÉTODO RESPONSÁVEL POR ATUALIZAR USUÁRIO
        return new Response(200, Api\User::setEditUser($request, $id), 'application/json');
    }
]);

//ROTA DELETE USUÁRIO (EXCLUIR)
$obRouter->delete('/api/v1/users/{id}', [
    'middlewares' => [
        'api',
    ],
    function($request, $id){
        return new Response(200, Api\User::setDeleteUser($request, $id), 'application/json');
    }
]);