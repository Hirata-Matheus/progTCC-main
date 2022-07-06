<?php
require __DIR__.'/includes/app.php';

use \App\Http\Router;
$obRouter = new Router(URL);

// inclui rotas da api
include __DIR__.'/routes/api.php';

// imprime o response da rota
$obRouter->run()
         ->sendResponse();
