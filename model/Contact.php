<?php

namespace model;

class Contact
{
    private int|null $id;
    private string $name;
    private string $email;
    private string $phone;
    private string $title;
    private mixed $created;


    public function getId(): int|null
    {
        return $this->id;
    }

    public function setId(int|null $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getCreated(): mixed
    {
        return $this->created;
    }

    public function setCreated(mixed $created): void
    {
        $this->created = $created;
    }
}