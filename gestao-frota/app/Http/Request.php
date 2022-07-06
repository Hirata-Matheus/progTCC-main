<?php

namespace App\Http;

class Request{
    /**
     * intancia do rounter
     */
    private $router;
    /**
     * Método http da requisição
     */
    private $httpMethod;
    /**
     * URI da página (url)
     */
    private $uri;
    /**
     * Parametros da URL ($_GET)
     */
    private $queryParams = [];

    /**
     * Variáveis recebidas no post da página $_POST
     */
    private $postVars = [];

    /**
     * Cabeçalho de requisição
     */
    private $headers = [];

    // construtor da classe
    public function __construct($router){
        $this->router = $router;
        $this->queryParams = $_GET ?? [];
        $this->headers = getallheaders();
        $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->setUri();
        $this->setPostVars();
    }

    private function setPostVars(){
        if ($this->httpMethod == 'GET') return false;

        $this->postVars = $_POST ?? [];

        $inputRaw = file_get_contents('php://input');
        //print_r($inputRaw); exit;
        $this->postVars = (strlen($inputRaw) && empty($_POST)) ? json_decode($inputRaw, true) : $this->postVars;

    }

    private function setUri(){
        $this->uri = $_SERVER['REQUEST_URI'] ?? '';

        $xURI = explode('?', $this->uri);
        $this->uri = $xURI[0];
    }

    public function getRouter(){
        return $this->router;
    }

    // método responsável por retornar o método http da requisição
    public function getHttpMethod(){
        return $this->httpMethod;
    }
    // método responsável por retornar a uri
    public function getUri(){
        return $this->uri;
    }
    // método responsável por retornar os parâmetros da url
    public function getQueryParams(){
        return $this->queryParams;
    }
    // método responsável por retornar o método $_POST
    public function getPostVars(){
        return $this->postVars;
    }
    // método responsável por retornar o cabeçalho da requisição
    public function getHeaders(){
        return $this->headers;
    }

}