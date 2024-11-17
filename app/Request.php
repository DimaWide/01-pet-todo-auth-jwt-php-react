<?php 
namespace App;

class Request
{
    public function getHeader($name)
    {
        $headerName = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
        return $_SERVER[$headerName] ?? null;
    }

    public function getBody()
    {
        return json_decode(file_get_contents('php://input'), true);
    }
}
