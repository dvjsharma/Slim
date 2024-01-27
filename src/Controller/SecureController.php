<?php

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request; 

class SecureController extends Controller{
    public function member(Request $request, Response $response){
        return $this->render($response, 'member.html',[
            'username'=>$this->ci->get('session')->get('username')
        ]);
    }
    public function status(Request $request, Response $response){
        return $this->render($response, 'status.html');
    }
}