<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Http\Response as Response;

class ApiController extends Controller
{
    public function search(Request $request, Response $response)
    {
        $albums = json_decode(file_get_contents(__DIR__ . '/../../data/albums.json'), true);
        $query = $request->getQueryParams()['q'] ?? '';

        if ($query == '') {
            return $response->withStatus(400)->withJson(['error' => 'Invalid request: Please enter a searh term in query params']);
        }
        
        if ($query) {
            $albums = array_values(array_filter($albums, function($album) use ($query) {
                return strpos($album['title'], $query) !== false || strpos($album['artist'], $query) !== false;
            }));
            return $response->withJson($albums);
        }
    }
}