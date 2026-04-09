<?php

class UserModel extends BaseModel
{
    public function findByUsername(string $username)
    {
        $stmt = $this->db->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.username = ?');
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findById(int $id)
    {
        $stmt = $this->db->prepare('SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id WHERE u.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function all()
    {
        $stmt = $this->db->query('SELECT u.id,u.username,u.fullname,u.email,r.name as role_name,u.role_id FROM users u JOIN roles r ON u.role_id = r.id');
        return $stmt->fetchAll();
    }

    public function update(int $id, array $data)
    {
        $fields = [];
        $params = [];

        if (!empty($data['fullname'])) {
            $fields[] = 'fullname = ?';
            $params[] = $data['fullname'];
        }
        if (!empty($data['email'])) {
            $fields[] = 'email = ?';
            $params[] = $data['email'];
        }
        if (!empty($data['username'])) {
            $fields[] = 'username = ?';
            $params[] = $data['username'];
        }
        if (!empty($data['password'])) {
            $fields[] = 'password = ?';
            $params[] = password_hash($data['password'], PASSWORD_BCRYPT);
        }
        if (!empty($data['role_id'])) {
            $fields[] = 'role_id = ?';
            $params[] = $data['role_id'];
        }

        if (empty($fields)) {
            return false;
        }

        $params[] = $id;
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete(int $id)
    {
        $stmt = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function findByEmail(string $email)
    {
        $stmt = $this->db->prepare('SELECT id, username, email FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findAdminEmail()
    {
        $stmt = $this->db->prepare('SELECT u.email FROM users u JOIN roles r ON u.role_id = r.id WHERE LOWER(r.name) = ? LIMIT 1');
        $stmt->execute(['admin']);
        $result = $stmt->fetch();
        return $result['email'] ?? null;
    }

    public function savePasswordReset(int $userId, string $token, int $expiresInMinutes = 30)
    {
        $expiresAt = date('Y-m-d H:i:s', time() + ($expiresInMinutes * 60));
        $stmt = $this->db->prepare('UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?');
        return $stmt->execute([$token, $expiresAt, $userId]);
    }

    public function findByResetToken(string $token)
    {
        $stmt = $this->db->prepare('SELECT id, username, email, reset_token_expires FROM users WHERE reset_token = ? AND reset_token_expires > NOW() LIMIT 1');
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function clearResetToken(int $userId)
    {
        $stmt = $this->db->prepare('UPDATE users SET reset_token = NULL, reset_token_expires = NULL WHERE id = ?');
        return $stmt->execute([$userId]);
    }
}
