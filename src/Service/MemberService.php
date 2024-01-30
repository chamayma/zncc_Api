<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Exception;

final class MemberService
{
  public function __construct(private Connection $conn)
  {
  }
  public function getAll(): array
  {
    return $this->conn->fetchAllAssociative(
      'SELECT id, companyName, companyEmail, companyPhone, email, region, district, ownerName, ownerEmail, ownerPhone, representativeName, gender, position, representativeEmail, representativePhone, businessType, businessCluster, businessActivity, registrationCertificate, representativeCV
       FROM member
       ORDER BY id ASC'
    );
  }


  public function getOne(string $id): array
  {
    $result = $this->conn->fetchAssociative(
      'SELECT id, companyName, companyEmail, companyPhone, email, region, district, ownerName, ownerEmail, ownerPhone, representativeName, gender, position, representativeEmail, representativePhone, businessType, businessCluster, businessActivity, registrationCertificate, representativeCV 
       FROM member 
       WHERE id = ?',
       [$id]
    );

    if (!$result) {
      throw new Exception('Member not found');
    }
    return $result;
  }
  public function create($data): int|string
  {
    return $this->conn->insert('member', $data);
  }

  public function update(string $id, $data): int|string
  {
    return $this->conn->update('member', $data, ['id' => $id]);
  }

  public function delete(string $id): int|string
  {
    return $this->conn->delete('member', ['id' => $id]);
  }
}
