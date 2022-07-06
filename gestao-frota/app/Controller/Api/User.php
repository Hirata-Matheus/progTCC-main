<?php

namespace App\Controller\Api;

use \App\Model\Entity\User as EntityUser;
use \WilliamCosta\DatabaseManager\Pagination;

class User extends Api{
    /**
     * Método responsável por mostrar cada item do usuário
     *
     */
    private static function getUserItems($request, &$obPagination){
        $itens = [];

        //QUANTIDADE TOTAL DE REGISTROS
        $quantidadeTotal = EntityUser::getUsers(null, null, null, 'COUNT(*) as qtd')->fetchObject()->qtd;

        //PÁGINA ATUAL
        $queryParams = $request->getQueryParams();
        $paginaAtual = $queryParams['page'] ?? 1;

        $obPagination = new Pagination($quantidadeTotal, $paginaAtual, 5);
        $results = EntityUser::getUsers(null, 'idUsuario ASC', $obPagination->getLimit());

        while($obUser = $results->fetchObject(EntityUser::class)){

            $itens[] =  [
                'id' => (int)$obUser->id,
                'nome' => $obUser->nome,
                'apelido' => $obUser->apelido,
                'telefone' => $obUser->telefone,
                'email' => $obUser->email
            ];
        }

        return $itens;
    }

    /**
     * Método responsável por mostrar todos usuários
     *
     */
    public static function getUsers($request){
        return [
            'usuarios' => self::getUserItems($request, $obPagination),
            'paginacao' => parent::getPagination($request, $obPagination)
        ];
    }

    /**
     * Método responsável por pegar um usuário
     *
     */
    public static function getUser($request, $id){
        //VALIDA O ID
        if (!is_numeric($id)) {
            throw new \Exception("O id". $id. " Não é valido", 404);
        }

        //BUSCA USUÁRIO
        $obUser = EntityUser::getUserById($id);

        if (!$obUser instanceof EntityUser) {
            throw new \Exception("O usuario ". $id. " Não foi encontrado", 404);
        }

        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'apelido' => $obUser->apelido,
            'telefone' => $obUser->telefone,
            'email' => $obUser->email
        ];
    }

    public static function getCurrentUser($request){
        //USUÁRIO ATUAL
        $obUser = $request->user;
        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'apelido' => $obUser->apelido,
            'telefone' => $obUser->telefone,
            'email' => $obUser->email
        ];
    }

    /**
     * Método responsável por adicionar novo usuario
     *
     * @param [type] $request
     * @return array
     */
    public static function setNewUser($request){
        $postVars = $request->getPostVars();

        //VALIDA CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) or !isset($postVars['email']) or !isset($postVars['senha'])) {
            throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios", 400);
        }

        //VALIDA SE E-MAIL JÁ ESTÁ EM USO
       $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
       if ($obUserEmail instanceof EntityUser) {
        throw new \Exception("O email '".$postVars['email']."' já esta em uso.", 400);
       }

        //CADASTRA NOVO USUÁRIO
        $obUser = new EntityUser;
        $obUser->nome = $postVars['nome'];
        $obUser->apelido = $postVars['apelido'];
        $obUser->telefone = $postVars['telefone'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->cadastrar();

        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'apelido' => $obUser->apelido,
            'telefone' => $obUser->telefone,
            'email' => $obUser->email,
        ];
    }

    /**
     * Método responsável por atualizar um usuário
     *
     * @param [type] $request
     * @param integer $id
     * @return array
     */
    public static function setEditUser($request, $id){
        $postVars = $request->getPostVars();

        //VALIDA CAMPOS OBRIGATÓRIOS
        if (!isset($postVars['nome']) or !isset($postVars['email']) or !isset($postVars['senha'])) {
            throw new \Exception("Os campos 'nome', 'email' e 'senha' são obrigatórios ", 400);
        }

        //BUSCA USUÁRIO
        $obUser = EntityUser::getUserById($id);

        //VALIDA USUÁRIO
        if (!$obUser instanceof EntityUser) {
            throw new \Exception("O usuario ". $id. " Não foi encontrado", 404);
        }

       $obUserEmail = EntityUser::getUserByEmail($postVars['email']);
       if ($obUserEmail instanceof EntityUser && $obUserEmail->id != $obUser->id) {
        throw new \Exception("O email '".$postVars['email']."' já esta em uso.", 400);
       }

       //ATUALIZA USUÁRIO
        $obUser->nome = $postVars['nome'];
        $obUser->apelido = $postVars['apelido'];
        $obUser->telefone = $postVars['telefone'];
        $obUser->email = $postVars['email'];
        $obUser->senha = password_hash($postVars['senha'], PASSWORD_DEFAULT);
        $obUser->atualizar();

        return [
            'id' => (int)$obUser->id,
            'nome' => $obUser->nome,
            'apelido' => $obUser->apelido,
            'telefone' => $obUser->telefone,
            'email' => $obUser->email,
        ];
    }

    /**
     * Método responsável por excluir um usuário
     */
    public static function setDeleteUser($request, $id){

        //BUSCA USUÁRIO NO BANCO
        $obUser = EntityUser::getUserById($id);

        //VALIDA INSTANCIA
        if (!$obUser instanceof EntityUser) {
            throw new \Exception("O usuario ". $id. " Não foi encontrado", 404);          
        }

        //EXCLUIR USUARIO
        $obUser->excluir();

        return [
            'sucesso' => true
        ];
    }
}