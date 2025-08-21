<?php

namespace App\Models\Entity;

class UserEntity
{
    public string $email;
    public string $pwd;
    public ?string $clusterLeader; // nullable
    public string $fullName;
    public readonly string $role;

    public function __construct(string $email, string $pwd, ?string $clusterLeader, string $fullName)
    {
        $this->email = $email;
        $this->pwd = $pwd;
        $this->clusterLeader = $clusterLeader;
        $this->fullName = $fullName;
        $this->role = "user";
    }
}
