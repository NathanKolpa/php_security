<?php
namespace app\endpoints;

use core\crypto\JwtToken;
use core\crypto\PasswordHash;
use core\database\Database;
use core\handling\Request;
use core\handling\RequestHandler;
use core\handling\ResponseWriter;
use core\handling\writers\ErrorWriter;
use core\handling\writers\JsonErrorFormatWriter;
use core\handling\writers\JsonResponseWriter;
use core\handling\writers\StatusCodeResponseWriter;

require_once "core/handling/RequestHandler.php";
require_once "core/handling/writers/JsonResponseWriter.php";
require_once "core/handling/writers/StatusCodeResponseWriter.php";
require_once "core/crypto/PasswordHash.php";
require_once "core/crypto/JwtToken.php";

class CreateMovieHandler implements RequestHandler
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    
    function handle(Request $req): ResponseWriter
    {
        $body = $req->getMiddlewareData("body");
        $title = $body['title'];
        $time = $body['time'];
        $ageRating = $body['age_rating'];
        $genre = $body['genre'];
        
        $this->database->insert("INSERT INTO films(titel, speelduur, kijkwijzer, genre) VALUES(?, ?, ?, ?)", 'siss', $title, $time, $ageRating, $genre);
        $id = $this->database->getInsertId();
        
        return new JsonResponseWriter([
            'id' => $id,
            'title' => $title,
            'time' => $time,
            'age_rating' => $ageRating,
            'genre' => $genre
        ]);
    }
}

class GetAllMoviesRequestHandler implements RequestHandler
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    function handle(Request $req): ResponseWriter
    {
        $results = $this->database->query("SELECT * FROM films", "");
        $json = [];
        
        while($row = $results->fetch_assoc())
        {
            array_push($json, [
                'id' => $row['id'],
                'title' => $row['titel'],
                'time' => $row['speelduur'],
                'age_rating' => $row['kijkwijzer'],
                'genre' => $row['genre']
            ]);
        }
        
        return new JsonResponseWriter($json);
    }
}

class DeleteMovieRequestHandler implements RequestHandler
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    function handle(Request $req): ResponseWriter
    {
        $id = $req->getRouteData('id');
        
        if($this->database->query("SELECT * FROM films WHERE id = ?", 'i', $id)->fetch_assoc() == null)
        {
            return new ErrorWriter(404, 'Movie not found', 'try another id', new JsonErrorFormatWriter());
        }
        
        if(!$this->database->insert("DELETE FROM films WHERE id = ?", "i", $id))
        {
            return new StatusCodeResponseWriter(500);
        }
        
        return new StatusCodeResponseWriter(204);
    }
}

class GetSingleMovieRequestHandler implements RequestHandler
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    
    function handle(Request $req): ResponseWriter
    {
        $id = $req->getRouteData('id');
        
        $row = $this->database->query("SELECT * FROM films WHERE id = ?", 'i', $id)->fetch_assoc();
        
        if($row == null)
        {
            return new ErrorWriter(404, 'Movie not found', 'try another id', new JsonErrorFormatWriter());
        }
        
        return new JsonResponseWriter([
            'id' => $row['id'],
            'title' => $row['titel'],
            'time' => $row['speelduur'],
            'age_rating' => $row['kijkwijzer'],
            'genre' => $row['genre']
        ]);
    }
}