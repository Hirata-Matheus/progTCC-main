<?php

namespace App\Model\Entity;
use \WilliamCosta\DatabaseManager\Database;

/**
 * Classe responsável por gerenciar os models do usuário
 */
class User{
    //ID DO USUÁRIO
    public $id;
    //NOME DO USUÁRIO
    public $nome;
    //APELIDO DO USUÁRIO
    public $apelido;
    //TELEFONE DO USUÁRIO
    public $telefone;
    //EMAIL DO USUÁRIO
    public $email;
    //SENHA DO USUÁRIO
    public $senha; 

    /**
     * Método responsável por buscar um usuário através do email
     *
     */
    public static function getUserByEmail($email){
        return (new Database('usuario'))->select('emailUsuario = "'.$email.'"')->fetchObject(self::class);
    }

    /**
     * Método responsável por buscar todos os usuários
     *
     */
    public static function getUsers($where = null, $order = null, $limit = null, $fields = '*'){
        return (new Database('usuario'))->select($where, $order, $limit, $fields);
    }

    /**
     * Método responsável por cadastrar um usuário
     */
    public function cadastrar(){
        $this->id = (new Database('usuario'))->insert([
            'nomeUsuario' => $this->nome,
            'apelidoUsuario' => $this->apelido,
            'telefoneUsuario' => $this->telefone,
            'emailUsuario' => $this->email,
            'senhaUsuario' => $this->senha        
        ]);
        return true;
    }

    /**
     * Método responsável por atualizar as informações do usuário
     */
    public function atualizar(){
        return (new Database('usuario'))->update('idUsuario = '.$this->id,[
            'nomeUsuario' => $this->nome,
            'apelidoUsuario' => $this->apelido,
            'telefoneUsuario' => $this->telefone,
            'emailUsuario' => $this->email,
            'senhaUsuario' => $this->senha  
        ]);
    }

    /**
     * Método responsável por excluir um usuário
     *
     */
    public function excluir(){
        return (new Database('usuario'))->delete('idUsuario = '.$this->id);
    }

    /**
     * Método responsável por buscar um usuário através pelo ID
     *
     * 
     */
    public static function getUserById($id){
        return self::getUsers('idUsuario = '.$id)->fetchObject(self::class);
    }

}