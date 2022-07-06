<?php
namespace App\Http\Middleware;
use \App\Utils\Cache\File as CacheFile;

class Cache{
    private function isCacheable($request){
        // valida o tempo de cache
        if (getenv('CACHE_TIME') <= 0) {
            return false;
        }

        // valida o metodo da requisicao
        if ($request->getHttpMethod() != 'GET') {
            return false;
        }

        //valida o header de cache
        $headers = $request->getHeaders();

        if(isset($headers['Cache-Control']) and $headers['Cache-Control'] == 'no-cache'){
            return false;
        }

        return true;
    }

    /**
     * metodo responsavel por retornar a hash do cache
     */
    private function getHash($request){
        // uri da rota
        $uri = $request->getRouter()->getUri();
        
        // query params
        $queryParams = $request->getQueryParams();
        
        $uri .= !empty($queryParams) ? '?'.http_build_query($queryParams) : '';
        //print_r($uri); exit;

        // remove as barras e retorna a hash
        return rtrim('route-'.preg_replace('/[^0-9a-zA-Z]/', '-', ltrim($uri, '/')),'-');
    }

    public function handle($request, $next) {
        // verifica se a request atual é cacheavel
        if (!$this->isCacheable($request)) {
            return $next($request);
        }

        //hash do cache
        $hash = $this->getHash($request);

        // retorna os dados do cache
        return CacheFile::getCache($hash, getenv('CACHE_TIME'), function() use($request, $next){
            return $next($request); 
        });
    }
}