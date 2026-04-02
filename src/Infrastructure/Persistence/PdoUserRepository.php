<?php

namespace App\Infrastructure\Persistence;

use App\Domain\User\UserEntity;
use App\Domain\User\UserRepository;

final class PdoUserRepository implements UserRepository {
  public function __construct(private readonly \PDO $pdo) {
  }

  public function findAll(): array {
    $stmt = $this->pdo->query('SELECT id, name, email, password FROM user');
    return array_map(
      fn(array $row) => new UserEntity($row['id'], $row['name'], $row['email'], $row['password']),
      $stmt->fetchAll(\PDO::FETCH_ASSOC)
    );
  }

  public function findById(int $id): ?UserEntity {
    $stmt = $this->pdo->prepare('SELECT id, name, email, password FROM user WHERE id = ?');
    $stmt->execute([$id]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $row ? new UserEntity($row['id'], $row['name'], $row['email'], $row['password']) : NULL;
  }

  public function findByEmail(string $email): ?UserEntity {
    $stmt = $this->pdo->prepare('SELECT id, name, email, password FROM user WHERE email = ?');
    $stmt->execute([$email]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $row ? new UserEntity($row['id'], $row['name'], $row['email'], $row['password']) : NULL;
  }

  public function save(UserEntity $user): UserEntity {
    if ($user->id === NULL) {
      $stmt = $this->pdo->prepare('INSERT INTO user (name, email, password) VALUES (?, ?, ?)');
      $stmt->execute([$user->name, $user->email, $user->password]);
      return new UserEntity((int)$this->pdo->lastInsertId(), $user->name, $user->email, $user->password);
    }

    $stmt = $this->pdo->prepare('UPDATE user SET name = ?, email = ?, password = ? WHERE id = ?');
    $stmt->execute([$user->name, $user->email, $user->password, $user->id]);
    return $user;
  }

  public function delete(int $id): void {
    $stmt = $this->pdo->prepare('DELETE FROM user WHERE id = ?');
    $stmt->execute([$id]);
  }
}