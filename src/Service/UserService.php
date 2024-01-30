<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Exception;

final class UserService
{
  public function __construct(private Connection $conn)
  {
  }
  public function getAll(): array
  {
    return $this->conn->fetchAllAssociative(
      'SELECT id, username, email, password, role
       FROM user
       ORDER BY id ASC'
    );
  }


  public function getOne(string $id): array
  {
    $result = $this->conn->fetchAssociative(
      'SELECT id, username, email, password, role 
       FROM user 
       WHERE id = ?',
       [$id]
    );

    if (!$result) {
      throw new Exception('User not found');
    }
    return $result;
  }
  public function create($data): int|string
  {
    return $this->conn->insert('user', $data);
  }

  public function update(string $id, $data): int|string
  {
    return $this->conn->update('user', $data, ['id' => $id]);
  }

  public function delete(string $id): int|string
  {
    return $this->conn->delete('user', ['id' => $id]);
  }
}
