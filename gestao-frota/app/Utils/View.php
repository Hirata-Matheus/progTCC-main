<?php

namespace App\Utils;

class View{

    private static $vars = [];
    /**
     * Método responsavel por definir os dados iniciais da classe
     */
    public static function init($vars = []){
        self::$vars = $vars;
    }

    /**
     * Método responsavel por retornar o conteudo de uma view
     */
    private static function getContentView($view){
        $file = __DIR__.'/../../resources/view/'.$view.'.html';
        return file_exists($file) ? \file_get_contents($file) : '';
    }

    /**
     * Método responsavel por retornar o conteúdo renderizado de uma view
     */
    public static function render($view, $vars =[]){
        // conteudo da view
        $contentView = self::getContentView($view);

        
        $vars = array_merge(self::$vars, $vars);
        // chaves do array de variaveis
        $keys = array_keys($vars);
        $keys = array_map(function($item){
            return '{{'.$item.'}}';
        }, $keys);

        
        // retorna o conteúdo renderizado
        return str_replace($keys, array_values($vars), $contentView);
    }
}