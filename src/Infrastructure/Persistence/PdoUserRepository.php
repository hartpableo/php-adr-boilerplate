<?php

namespace App\Infrastructure\Persistence;

use App\Domain\User\UserEntity;
use App\Domain\User\UserRepository;

final class PdoUserRepository implements UserRepository {
  public function __construct(private readonly \PDO $pdo) {
  }

  public function findAll(): array {
    $stmt = $this->pdo->query('SELECT id, name FROM users');
    return array_map(
      fn(array $row) => new UserEntity($row['id'], $row['name']),
      $stmt->fetchAll(\PDO::FETCH_ASSOC)
    );
  }

  public function findById(int $id): ?UserEntity {
    $stmt = $this->pdo->prepare('SELECT id, name, email FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $row ? new UserEntity($row['id'], $row['name']) : NULL;
  }

  public function save(UserEntity $user): UserEntity {
    $stmt = $this->pdo->prepare('INSERT INTO users (name) VALUES (?, ?)');
    $stmt->execute([$user->name]);
    return new UserEntity((int)$this->pdo->lastInsertId(), $user->name);
  }

  public function delete(int $id): void {
    $stmt = $this->pdo->prepare('DELETE FROM users WHERE id = ?');
    $stmt->execute([$id]);
  }
}