<?php

namespace App\Controller\Api;

/**
 * metodo responsavel por retornar os detalhes da api
 */
class Api{
    public static function getDetails($request){
        return [
            'nome' => 'API - Gestao-Frota - TCC',
            'versao' => 'v1.0.0',
            'autor' => 'Gabriel Domiciano',
            'email' => 'gabriel.ads18@gmail.com'
        ];
    }

    /**
     * método responsavel por retornar os detalhes da paginação
     */
    protected static function getPagination($request, $obPagination){
        // query params
        $queryParams = $request->getQueryParams();

        //pagina 
        $pages = $obPagination->getPages();

        //retorno dos dados
        return [
            'paginaAtual' => isset($queryParams['page']) ? (int)$queryParams['page'] : 1,
            'quantidadedePaginas' => !empty($pages) ? count($pages) : 1
        ];
    }
}