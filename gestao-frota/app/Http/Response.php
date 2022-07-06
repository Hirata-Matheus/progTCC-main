<?php
namespace App\Http;

class Response{
    // código do status http (sucesso ou erro)
    private $httpCode = 200;
    // cabeçalho do response
    private $headers = [];
    // tipo de conteúdo retornado
    private $contentType = 'text/html';
    // conteúdo do response
    private $content;

    // método responsável por iniciar a classe e definir os valores
    public function __construct($httpCode, $content, $contentType = 'text/html'){
        $this->httpCode = $httpCode;
        $this->content = $content; 
        $this->setContentType($contentType);   
    }

    // métodoo responsavel por alterar o content type do response
    public function setContentType($contentType){
        $this->contentType = $contentType;
        $this->addHeader('Content-Type', $contentType);
    }

    // método responsavel por adicionar um registro no cabeçalho de response
    public function addHeader($key, $value){
        $this->headers[$key] = $value;
    }

    // método responsável por enviar os headers para o navegador
    public function sendHeaders(){
        http_response_code($this->httpCode);

        foreach ($this->headers as $key=>$value) {
            header($key.': '.$value);
        }
    }

    // método responsável por enviar a resposta para o usuário
    public function sendResponse(){
        // envia os headers
        $this->sendHeaders();

        // imprime o conteudo
        switch($this->contentType){
            case 'text/html':
                echo $this->content;
                exit;
            case 'application/json':
                echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                exit;
        }
    }

     


}