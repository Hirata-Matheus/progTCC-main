<?php

namespace App\Utils\Cache;

class File{
    /**
     * metodo responsavel por retornar um caminho
     */
    private static function getFilePath($hash){
        // diretorio de cache
        $dir = getenv('CACHE_DIR');

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // retorna o caminho
        return $dir.'/'.$hash;
    }
    /**
     * metodo responsavel por retornar informações no cache
     */
    private static function storageCache($hash, $content){
        // converter retorno
        $serialize = serialize($content);
        
        // obtem caminho
        $cacheFile = self::getFilePath($hash);
        
        return file_put_contents($cacheFile, $serialize);
    }

    /**
     * metodo responsavel por retornar o conteudo gravado no cache
     */
    private static function getContentCache($hash, $expiration){
        // obtem o caminho
        $cacheFile = self::getFilePath($hash);
        
        //verifica existencia do arquivo
        if(!file_exists($cacheFile)){
            return false;
        }

        // valida a expiração do cache
        $createTime = filectime($cacheFile);
        $diffTime = time() - $createTime;

        if ($diffTime > $expiration) {
            return false;
        }

        $serialize = file_get_contents($cacheFile);
        return unserialize($serialize);
    }

    /**
     * metodo responsavel por obter informação do cache
     */
    public static function getCache($hash, $expiration, $function){
        // verifica o conteudo gravado
        if ($content = self::getContentCache($hash, $expiration)) {
            return $content;
        }

        $content = $function();
        
        // grava o retorno no cache
        self::storageCache($hash, $content);

        // retorna o conteudo
        return $content;
    }
}