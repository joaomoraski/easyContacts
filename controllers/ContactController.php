<?php

namespace controllers;

use Exception;
use \model\Contact as Contact;
use \config\Connection;
use PDO;

class ContactController
{
    private array $contacts = [
        ["id" => 1, "name" => "John Doe", "email" => "john@example.com", "phone" => "123456789", "title" => "Manager", "created" => "2024-02-19 12:00:00"],
        ["id" => 2, "name" => "Jane Doe", "email" => "jane@example.com", "phone" => "987654321", "title" => "Developer", "created" => "2024-02-19 13:00:00"],
        ["id" => 3, "name" => 'Sam White', "email" => 'samwhite@example.com', "phone" => '2004550121', "title" => 'Employee', "created" => '2019-05-08 17:29:27'],
        ["id" => 4, "name" => 'Colin Chaplin', "email" => 'colinchaplin@example.com', "phone" => '2022550178', "title" => 'Supervisor', "created" => '2019-05-08 17:29:27'],
        ["id" => 5, "name" => 'Ricky Waltz', "email" => 'rickywaltz@example.com', "phone" => '7862342390', "title" => 'Stunt man', "created" => '2019-05-09 19:16:00'],
        ["id" => 6, "name" => 'Arnold Hall', "email" => 'arnoldhall@example.com', "phone" => '5089573579', "title" => 'Manager', "created" => '2019-05-09 19:17:00'],
        ["id" => 7, "name" => 'Toni Adams', "email" => 'alvah1981@example.com', "phone" => '2603668738', "title" => 'Crazy Man', "created" => '2019-05-09 19:19:00'],
        ["id" => 8, "name" => 'Donald Perry', "email" => 'donald1983@example.com', "phone" => '7019007916', "title" => 'Employee', "created" => '2019-05-09 19:20:00'],
        ["id" => 9, "name" => 'Joe McKinney', "email" => 'nadia.doole0@example.com', "phone" => '6153353674', "title" => 'Employee', "created" => '2019-05-09 19:20:00'],
        ["id" => 10, "name" => 'Angela Horst', "email" => 'angela1977@example.com', "phone" => '3094234980', "title" => 'Assistant', "created" => '2019-05-09 19:21:00'],
        ["id" => 11, "name" => 'James Jameson', "email" => 'james1965@example.com', "phone" => '4002349823', "title" => 'Assistant', "created" => '2019-05-09 19:32:00'],
        ["id" => 12, "name" => 'Daniel Deacon', "email" => 'danieldeacon@example.com', "phone" => '5003423549', "title" => 'Manager', "created" => '2019-05-09 19:33:00'],
        ["id" => 13, "name" => 'David Deacon', "email" => 'daviddeacon@example.com', "phone" => '2025550121', "title" => 'Employee', "created" => '2019-05-08 17:28:44']
    ];

    function mountContactEntity(array $postData): Contact
    {
        $contact = new Contact();
        $contact->setName($postData['name'] ?? '');
        $contact->setEmail($postData['email'] ?? '');
        $contact->setPhone($postData['phone'] ?? '');
        $contact->setTitle($postData['title'] ?? '');
        $contact->setCreated($postData['created'] ?? date('Y-m-d H:i:s'));
        return $contact;
    }

    function createContact(array $postData): array
    {
        $pdo = $this->getConnection();
        if (!$pdo) {
            $newId = count($this->contacts) + 1;
            $postData['id'] = $newId;
            $this->contacts[] = $postData;
            return $this->fillResponse(true, "Contato criado com sucesso no array local.");
        }

        try {
            $contact = $this->mountContactEntity($postData);
            $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone, title, created) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$contact->getName(), $contact->getEmail(), $contact->getPhone(), $contact->getTitle(), $contact->getCreated()]);
            return $this->fillResponse(true, "Contato criado com sucesso.");
        } catch (Exception $exception) {
            return $this->fillResponse(false, "Erro ao criar contato: " . $exception->getMessage());
        }
    }

    function getContactById(int $id): array
    {
        return $this->contacts[$id-1];
    }

    function updateContact(array $postData, int $id): array
    {
        $pdo = $this->getConnection();
        if (!$pdo) {
            foreach ($this->contacts as &$contact) {
                if ($contact['id'] == $id) {
                    $contact = array_merge($contact, $postData);
                    return $this->fillResponse(true, "Contato atualizado com sucesso no array local.");
                }
            }
            return $this->fillResponse(false, "Contato não encontrado no array local.");
        }

        try {
            $stmt = $pdo->prepare('UPDATE contacts SET name = ?, email = ?, phone = ?, title = ?, created = ? WHERE id = ?');
            $stmt->execute([$postData['name'] ?? '', $postData['email'] ?? '', $postData['phone'] ?? '', $postData['title'] ?? '', $postData['created'] ?? date('Y-m-d H:i:s'), $id]);
            return $this->fillResponse(true, "Contato atualizado com sucesso.");
        } catch (Exception $exception) {
            return $this->fillResponse(false, "Erro ao atualizar contato: " . $exception->getMessage());
        }
    }

    function readContacts(int $page): array
    {
        $pdo = $this->getConnection();
        if (!$pdo) {
            return [
                "contacts" => $this->contacts,
                "num_contacts" => count($this->contacts),
                "records_per_page" => 5,
                "page" => $page
            ];
        }

        try {
            $records_per_page = 5;
            $stmt = $pdo->prepare('SELECT * FROM contacts ORDER BY id LIMIT :current_page, :record_per_page');
            $stmt->bindValue(':current_page', ($page - 1) * $records_per_page, PDO::PARAM_INT);
            $stmt->bindValue(':record_per_page', $records_per_page, PDO::PARAM_INT);
            $stmt->execute();
            $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $num_contacts = $pdo->query('SELECT COUNT(*) FROM contacts')->fetchColumn();

            return [
                "contacts" => $contacts,
                "num_contacts" => $num_contacts,
                "records_per_page" => $records_per_page,
                "page" => $page
            ];
        } catch (Exception $e) {
            return $this->fillResponse(false, "Erro ao buscar contatos.");
        }
    }


    function deleteContact(int $id, string $confirm): array
    {
        $pdo = $this->getConnection();
        if (!$pdo) {
            // Se não houver conexão PDO, use o array interno
            foreach ($this->contacts as $key => $contact) {
                if ($contact['id'] == $id) {
                    if ($confirm == 'yes') {
                        // Remove o contato do array
                        unset($this->contacts[$key-1]);
                        return $this->fillResponse(true, "Contato excluído com sucesso no array local.");
                    } else {
                        return $this->fillResponse(false, "Exclusão cancelada.");
                    }
                }
            }
            return $this->fillResponse(false, "Contato não encontrado no array local.");
        }

        // Se houver conexão PDO, continue com a lógica original
        if ($id != null) {
            $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
            $stmt->execute([$id]);
            $contact = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$contact) {
                return $this->fillResponse(false, "Contato não encontrado.");
            }
            if ($confirm != null && $confirm != '') {
                if ($confirm == 'yes') {
                    // User clicked the "Yes" button, delete record
                    $stmt = $pdo->prepare('DELETE FROM contacts WHERE id = ?');
                    $stmt->execute([$id]);
                    return $this->fillResponse(true, "Contato excluído com sucesso.");
                } else {
                    return $this->fillResponse(false, "Exclusão cancelada.");
                }
            }
        } else {
            return $this->fillResponse(false, "ID Não especificado.");
        }
        return $this->fillResponse(false, "Erro ao excluir contato.");
    }

    function getConnection(): ?PDO
    {
        try {
            $connection = new Connection();
            return $connection->pdo_connect_mysql();
        } catch (Exception $e) {
            return null;
        }
    }

    function fillResponse($success, $message): array
    {
        return [
            "success" => $success,
            "message" => $message
        ];
    }
}
