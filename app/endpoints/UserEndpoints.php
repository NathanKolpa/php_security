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

require_once "core/handling/RequestHandler.php";
require_once "core/handling/writers/JsonResponseWriter.php";
require_once "core/crypto/PasswordHash.php";
require_once "core/crypto/JwtToken.php";

class CreateUserHandler implements RequestHandler
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    function handle(Request $req): ResponseWriter
    {
        $body = $req->getMiddlewareData("body");
        $password = $body['password'];
        $email = $body['email'];

        $result = $this->database->query("SELECT * FROM users WHERE email LIKE ?", "s", $email);
        if ($result->fetch_assoc())
            return new ErrorWriter(409, "Email already exists", "try another email", new JsonErrorFormatWriter());

        $passwordHash = PasswordHash::fromPassword($password);

        $this->database->insert("INSERT INTO users(email, password_hash) VALUES(?, ?)", "ss", $email, $passwordHash->getHash());

        return new JsonResponseWriter([]);
    }
}

class LoginHandler implements RequestHandler
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    function handle(Request $req): ResponseWriter
    {
        $body = $req->getMiddlewareData("body");
        $password = $body['password'];
        $email = $body['email'];

        $result = $this->database->query("SELECT * FROM users WHERE email LIKE ?", "s", $email);
        if (!($row = $result->fetch_assoc()))
            return new ErrorWriter(404, "User does not exists", "try another email", new JsonErrorFormatWriter());

        $passwordHash = new PasswordHash($row['password_hash']);

        if (!$passwordHash->check($password))
            return new ErrorWriter(401, "Invalid password", "try another password", new JsonErrorFormatWriter());

        $expiration = new \DateTime();
        $expiration->modify("+ 1 hour");

        $jwt = new JwtToken([
            'user_id' => $row['id'],
            'auth_level' => $row['auth_level']
        ], $expiration);

        return new JsonResponseWriter([
            "login_token" => $jwt->toToken(),
            'expiration' => $expiration->format('Y-m-d H:i:s'),
            'user' => [
                'user_id' => $row['id'],
                'email' => $row['email'],
                'auth_level' => $row['auth_level']
            ],
        ]);
    }
}
