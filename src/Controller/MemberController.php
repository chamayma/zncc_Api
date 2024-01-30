<?php

declare(strict_types=1);

namespace App\Controller;

use App\CustomResponse as Response;
use App\Service\MemberService;
use Exception;
use Pimple\Psr11\Container;
use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

final class MemberController
{
  private MemberService $memberService;

  public function __construct(private Container $container)
  {
    $this->memberService = $this->container->get('memberService');
  }

  public function getAll(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->memberService->getAll();
      return $response->withJson($result);
    } catch (Exception $e) {
      return $response->withJson(['error' => $e->getMessage()], 500);
    }
  }

  public function getOne(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->memberService->getOne((string) $args['id']);
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

      $validator = v::key('companyName', v::stringType())
       ->key('companyEmail', v::stringType())
       ->key('companyPhone', v::stringType())
       ->key('email', v::stringType())
       ->key('region', v::stringType())
       ->key('district', v::stringType())
       ->key('ownerName', v::stringType())
       ->key('ownerEmail', v::stringType())
       ->key('ownerPhone', v::stringType())
       ->key('representativeName', v::stringType())
       ->key('gender', v::stringType())
       ->key('position', v::stringType())
       ->key('representativeEmail', v::stringType())
       ->key('representativePhone', v::stringType())
       ->key('businessType', v::stringType())
       ->key('businessCluster', v::stringType())
       ->key('businessActivity', v::stringType())
       ->key('registrationCertificate', v::stringType())
       ->key('representativeCV', v::stringType());

      $dto = [
          'id' => $input['id'],
  'companyName' => $input['companyName'],
  'companyEmail' => $input['companyEmail'],
  'companyPhone' => $input['companyPhone'],
  'email' => $input['email'],
  'region' => $input['region'],
  'district' => $input['district'],
  'ownerName' => $input['ownerName'],
  'ownerEmail' => $input['ownerEmail'],
  'ownerPhone' => $input['ownerPhone'],
  'representativeName' => $input['representativeName'],
  'gender' => $input['gender'],
  'position' => $input['position'],
  'representativeEmail' => $input['representativeEmail'],
  'representativePhone' => $input['representativePhone'],
  'businessType' => $input['businessType'],
  'businessCluster' => $input['businessCluster'],
  'businessActivity' => $input['businessActivity'],
  'registrationCertificate' => $input['registrationCertificate'],
  'representativeCV' => $input['representativeCV'],
      ];

      foreach ($requiredFields as $key) {
        if ($input[$key] === null) {
          unset($dto[$key]);
        }
      }

      $validator->assert($dto);

     $this->memberService->create($dto);
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
      $validator = v::key('companyName', v::optional(v::stringType()))
       ->key('companyEmail', v::optional(v::stringType()))
       ->key('companyPhone', v::optional(v::stringType()))
       ->key('email', v::optional(v::stringType()))
       ->key('region', v::optional(v::stringType()))
       ->key('district', v::optional(v::stringType()))
       ->key('ownerName', v::optional(v::stringType()))
       ->key('ownerEmail', v::optional(v::stringType()))
       ->key('ownerPhone', v::optional(v::stringType()))
       ->key('representativeName', v::optional(v::stringType()))
       ->key('gender', v::optional(v::stringType()))
       ->key('position', v::optional(v::stringType()))
       ->key('representativeEmail', v::optional(v::stringType()))
       ->key('representativePhone', v::optional(v::stringType()))
       ->key('businessType', v::optional(v::stringType()))
       ->key('businessCluster', v::optional(v::stringType()))
       ->key('businessActivity', v::optional(v::stringType()))
       ->key('registrationCertificate', v::optional(v::stringType()))
       ->key('representativeCV', v::optional(v::stringType()));

      $dtoForValidation = [
          'id' => $input['id'] ?? '',
  'companyName' => $input['companyName'] ?? '',
  'companyEmail' => $input['companyEmail'] ?? '',
  'companyPhone' => $input['companyPhone'] ?? '',
  'email' => $input['email'] ?? '',
  'region' => $input['region'] ?? '',
  'district' => $input['district'] ?? '',
  'ownerName' => $input['ownerName'] ?? '',
  'ownerEmail' => $input['ownerEmail'] ?? '',
  'ownerPhone' => $input['ownerPhone'] ?? '',
  'representativeName' => $input['representativeName'] ?? '',
  'gender' => $input['gender'] ?? '',
  'position' => $input['position'] ?? '',
  'representativeEmail' => $input['representativeEmail'] ?? '',
  'representativePhone' => $input['representativePhone'] ?? '',
  'businessType' => $input['businessType'] ?? '',
  'businessCluster' => $input['businessCluster'] ?? '',
  'businessActivity' => $input['businessActivity'] ?? '',
  'registrationCertificate' => $input['registrationCertificate'] ?? '',
  'representativeCV' => $input['representativeCV'] ?? '',
      ];

      $validator->assert($dtoForValidation);

      $dto = array_filter($dtoForValidation, fn($value) => $value !== '');

      $this->memberService->update((string) $args['id'], $dto);
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
   //   $result = $this->memberService->delete((string) $args['id']);
   //   return $response->withJson($result);
   // } catch (Exception $e) {
   //   return $response->withJson(['error' => $e->getMessage()], 400);
   // }
   return $response->withJson(['error' => 'Disabled'], 400); // uncomment above code to enable delete
  }

}