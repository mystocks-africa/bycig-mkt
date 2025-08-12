<?php  

interface IModel
{
    public function create(int $id): void;
    public function update(int $id): void;
    public function delete(int $id): void;
    public function getAll(): array;
    public function get(int $id): array;
}
