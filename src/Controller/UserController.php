<?php

declare(strict_types=1);

namespace App\Controller;

use App\CustomResponse as Response;
use App\Service\UserService;
use Exception;
use Pimple\Psr11\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

final class UserController
{
  private UserService $userService;

  public function __construct(private Container $container)
  {
    $this->userService = $this->container->get('userService');
  }

  public function getAll(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->userService->getAll();
      return $response->withJson($result);
    } catch (Exception $e) {
      return $response->withJson(['error' => $e->getMessage()], 500);
    }
  }

  public function getOne(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->userService->getOne((string) $args['id']);
      return $response->withJson($result);
    } catch (Exception $e) {
      return $response->withJson(['error' => $e->getMessage()], 404);
    }
  }

  public function create(Request $request, Response $response, array $args): Response
  {
    try {
      $input = $request->getParsedBody();
      $requiredFields = [];

      $validator = v::key('username', v::stringType())
       ->key('email', v::stringType())
       ->key('password', v::stringType())
       ->key('role', v::stringType());

      $dto = [
          'id' => $input['id'],
  'username' => $input['username'],
  'email' => $input['email'],
  'password' => $input['password'],
  'role' => $input['role'],
      ];

      foreach ($requiredFields as $key) {
        if ($input[$key] === null) {
          unset($dto[$key]);
        }
      }

      $validator->assert($dto);

     $this->userService->create($dto);
     return $response->withStatus(201);
    } catch (Exception $e) {
      $duplicateErrorCode = 1062;
      $foreignErrorCode = 1452;

      if ($e instanceof NestedValidationException) {
        return $response->withJson(['error' => $e->getMessages()], 400);
      } else if ($e->getCode() === $duplicateErrorCode) {
        return $response->withJson(['error' => 'The data you try to insert already exists'], 409);
      } else if ($e->getCode() === $foreignErrorCode) {
        $matches = [];
        preg_match("/FOREIGN KEY \(`(\w+)`\) REFERENCES `(\w+)` \(`(\w+)`\)/", $e->getMessage(), $matches);
        if (count($matches) >= 4) {
          $childColumnName = $matches[1];
          $parentTableName = $matches[2];
          $parentColumnName = $matches[3];
        }
        return $response->withJson(['error' => "The '{$childColumnName}' does not exist in the '{$parentTableName} table' column' '{$parentColumnName}'."], 404);
      } else {
        return $response->withJson(['error' => $e->getMessage()], 400);
      }
    }
  }

  public function update(Request $request, Response $response, array $args): Response
  {
    try {
      $input = $request->getParsedBody();
      $validator = v::key('username', v::optional(v::stringType()))
       ->key('email', v::optional(v::stringType()))
       ->key('password', v::optional(v::stringType()))
       ->key('role', v::optional(v::stringType()));

      $dtoForValidation = [
          'id' => $input['id'] ?? '',
  'username' => $input['username'] ?? '',
  'email' => $input['email'] ?? '',
  'password' => $input['password'] ?? '',
  'role' => $input['role'] ?? '',
      ];

      $validator->assert($dtoForValidation);

      $dto = array_filter($dtoForValidation, fn($value) => $value !== '');

      $this->userService->update((string) $args['id'], $dto);
      return $response->withStatus(204);
    } catch (Exception $e) {
      $duplicateErrorCode = 1062;
      $foreignErrorCode = 1452;

      if ($e instanceof NestedValidationException) {
        return $response->withJson(['error' => $e->getMessages()], 400);
      } else {
        return $response->withJson(['error' => $e->getMessage()], 500);
      }
    }
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
   // try {
   //   $result = $this->userService->delete((string) $args['id']);
   //   return $response->withJson($result);
   // } catch (Exception $e) {
   //   return $response->withJson(['error' => $e->getMessage()], 400);
   // }
   return $response->withJson(['error' => 'Disabled'], 400); // uncomment above code to enable delete
  }

}