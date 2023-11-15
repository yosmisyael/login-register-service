<?php

namespace Yosev\Login\Management\Repository;

use Yosev\Login\Management\Domain\Session;

class SessionRepository
{
    private \PDO $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }
    public function store(Session $session): Session
    {
        $statement = $this->db->prepare("INSERT INTO sessions (id, user_id) VALUES (?, ?)");
        $statement->execute([$session->id, $session->userId]);
        return $session;
    }

    public function findById(string $id): ?Session
    {
        $statement = $this->db->prepare("SELECT id, user_id FROM sessions WHERE id = ?");
        $statement->execute([$id]);

        try {
            if ($row = $statement->fetch()) {
                $session = new Session();
                $session->id = $row['id'];
                $session->userId = $row['user_id'];
                
                return $session;
            } else {
                return null;
            }
        } finally {
            $statement->closeCursor();
        }
    }

    public function delete(string $id): void
    {
        $statement = $this->db->prepare("DELETE FROM sessions WHERE id = ?");
        $statement->execute([$id]);
    }

    public function destroyAll(): void
    {
        $this->db->exec("DELETE FROM sessions");
    }

} 
