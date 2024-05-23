<?php

namespace AddressFinder\Repository;

class UserRequestRepository
{
    public function __construct(
        private \PDO $pdo
    ) {}

    public function save(string $address, string $userId): int
    {
        $sql = 'INSERT INTO test_task.user_requests (user_id, request) 
                VALUES (:userId, :address)';

        $st = $this->pdo->prepare($sql);
        $st->execute([
            'userId' => $userId,
            'address' => $address
        ]);

        return $this->pdo->lastInsertId();
    }

    public function get(string $address, string $userId): array
    {
        $sql = 'SELECT id, user_id userId, request FROM test_task.user_requests
                WHERE user_id = :userId AND request = :request';

        $st = $this->pdo->prepare($sql);
        $st->execute([
            'userId' => $userId,
            'request' => $address
        ]);

        return $st->fetch(\PDO::FETCH_ASSOC) ?: [];
    }
}