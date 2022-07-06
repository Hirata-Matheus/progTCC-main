<?php
namespace App\Http;
use \Closure;
use \Exception;
use \ReflectionFunction;
use \App\Http\Middleware\Queue as MiddlewareQueue;


class Router{
    // url completa do projThrowableeto
    private $url = '';

    //prefixo do projeto
    private $prefix = '';

    // indice de rotas
    private $routes = [];

    // instancia de request
    private $request;

    private $contentType = 'text/html';

    // método construtor iniciar a classe
    public function __construct($url){
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    public function setContentType($contentType){
        $this->contentType = $contentType;
    }

    // método responsavel por definir o prefixo das rotas
    private function setPrefix(){
        $parseUrl = parse_url($this->url);
        
        $this->prefix = $parseUrl['path'] ?? '';
    }

    private function addRoute($method, $route, $params = []){
        // validação dos parametros
        foreach ($params as $key=>$value) {
            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        $params['middlewares'] = $params['middlewares'] ?? [];
        // print_r($params); exit;

        // variaveis da rota
        $params['variables'] = [];

        //padrao validacao das variaveis das rotas
        $patternVariable = '/{(.*?)}/';
        if (preg_match_all($patternVariable, $route, $matches)) {
            $route = preg_replace($patternVariable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }

        $route = rtrim($route, '/');
        
        // padrao de validacao url
        $patternRoute = '/^'.str_replace('/','\/',$route).'$/';

        // adiciona a rota dentro da classe
        $this->routes[$patternRoute][$method] = $params;
    }

    // método responsavel por definir uma rota de get
    public function get($route, $params = []){
        return $this->addRoute('GET', $route, $params);
    }
    // método responsavel por definir uma rota de post
    public function post($route, $params = []){
        return $this->addRoute('POST', $route, $params);
    }
    // método responsavel por definir uma rota de put
    public function put($route, $params = []){
        return $this->addRoute('PUT', $route, $params);
    }
    // método responsavel por definir uma rota de delete
    public function delete($route, $params = []){
        return $this->addRoute('DELETE', $route, $params);
    }

    // metodo responsavel por retornar a uri
    public function getUri(){
        // uri da request
        $uri = $this->request->getUri();

        
        $xUri = strlen($this->prefix) ? explode($this->prefix,$uri) : [$uri];
       

        return rtrim(end($xUri), '/');

    }

    // metodo responsavel por retornar os dados da rota atual
    private function getRoute(){
        // uri
        $uri = $this->getUri();
        

        $httpMethod = $this->request->getHttpMethod();
        
        
        foreach ($this->routes as $patternRoute=>$methods) {
            if(preg_match($patternRoute, $uri, $matches)){
                if(isset($methods[$httpMethod])){
                   // print_r($matches);

                    unset($matches[0]);
                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;

                    return $methods[$httpMethod];
                }
                throw new Exception("Método não é permitido", 405);             
            }
        }
        throw new Exception("URL não encontrada", 404);      
    }

    // metodo responsavel por executar  arota atual
    public function run(){
        try {
            // obtem a rota atual
            $route = $this->getRoute();
            
            if (!isset($route['controller'])) {
                throw new Exception("Url não pode ser processada", 500);
            }

            $args = [];
            
            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter) {
                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            // retorna a execução da fila de middlewares
            return (new MiddlewareQueue($route['middlewares'], $route['controller'], $args))->next($this->request);
            // return call_user_func_array($route['controller'], $args);
            
        } catch (Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
        }
    }

    private function getErrorMessage($message){
        switch ($this->contentType) {
            case 'application/json':
                return [
                    'error' => $message
                ];
                break;
            
            default:
                return $message;
                break;
        }
    }

    public function getCurrentUrl(){
        return $this->url.$this->getUri();
    }

    public function redirect($route){
        $url = $this->url.$route;
        
        header('location: '.$url);
        exit;
    }

}