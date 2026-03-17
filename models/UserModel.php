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
        $stmt = $this->db->query('SELECT u.id,u.username,u.fullname,u.email,r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id');
        return $stmt->fetchAll();
    }
}
