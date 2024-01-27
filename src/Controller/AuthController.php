<?php

namespace App\Controller;
use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response; 
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthController extends Controller{
    public function login(Request $request, Response $response){
        if ($request->getMethod() == 'POST'){
            $data = $request->getParsedBody();
            if ($data['username'] == 'admin' && $data['password'] == 'admin'){
                $this->ci->get('session')->set('username', $data['username']);
                return $response->withRedirect('/secure');
            }
            else{
                $this->ci->get('session')->set('error', 'Invalid username or password');
                return $response->withRedirect('/login');
            }
        }
        return $this->render($response, 'login.html');
    }

    public function logout(Request $request, Response $response){
        $this->ci->get('session')->delete('username');
        return $response->withRedirect('/');
    }

}