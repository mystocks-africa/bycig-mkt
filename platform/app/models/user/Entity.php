<?php
namespace App\Models\User;

class Entity
{
    public string $email;
    public string $pwd;
    public ?string $clusterLeader; // nullable
    public string $fullName;
    public float $balance;
    public readonly string $role;

    public function __construct(
        string $email, 
        string $pwd, 
        ?string $clusterLeader, 
        string $fullName,
        float $balance = 500.00 // it is 500 dollars by default
    )
    {
        $this->email = $email;
        $this->pwd = $pwd;
        $this->clusterLeader = $clusterLeader;
        $this->fullName = $fullName;
        $this->role = "user";
        $this->balance = $balance;
    }
}
