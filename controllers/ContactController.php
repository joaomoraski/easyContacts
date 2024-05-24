<?php

namespace controllers;

use Exception;
use \model\Contact as Contact;
use \config\Connection;
use PDO;

class ContactController
{
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
        if (!empty($postData)) {
            try {
                $contact = $this->mountContactEntity($postData);
                $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone, title, created) VALUES (?, ?, ?, ?, ?)');
                $stmt->execute([$contact->getName(),
                    $contact->getEmail(),
                    $contact->getPhone(),
                    $contact->getTitle(),
                    $contact->getCreated()
                ]);
                return [
                    "success" => true,
                    "message" => "Contato criado com sucesso."
                ];
            } catch (Exception $exception) {
                return [
                    "success" => false,
                    "message" => "Erro ao criar contato, provavel ID existente." . $exception->getMessage()
                ];
            }
        }
        return [
            "success" => false,
            "message" => "Erro ao criar contato."
        ];
    }

    function readContacts(int $page): array
    {
        $pdo = $this->getConnection();
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
    }

    function updateContact(array $postData, int $id): array
    {
        try {
            $pdo = $this->getConnection();
            if (!empty($postData)) {
                $name = $postData['name'] ?? '';
                $email = $postData['email'] ?? '';
                $phone = $postData['phone'] ?? '';
                $title = $postData['title'] ?? '';
                $created = $postData['created'] ?? date('Y-m-d H:i:s');
                // Update the record
                $stmt = $pdo->prepare('UPDATE contacts SET name = ?, email = ?, phone = ?, title = ?, created = ? WHERE id = ?');
                $stmt->execute([$name, $email, $phone, $title, $created, $id]);
                return $this->fillResponse(true, "Contato atualizado com sucesso.");
            }
        } catch (Exception $ex) {
            return $this->fillResponse(false, "Erro ao atualizar contato, provavel ID existente." . $ex->getMessage());
        }
        return $this->fillResponse(false, "Erro ao atualizar contato, provavel ID existente.");
    }

    function deleteContact(int $id, string $confirm): array
    {
        $pdo = $this->getConnection();
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
                    return $this->fillResponse(false, "Excluido com súcesso.");
                } else {
                    header('Location: read.php');
                    exit;
                }
            }
        } else {
            return $this->fillResponse(false, "ID Não especificado.");
        }
        return $this->fillResponse(false, "Erro ao excluir contato.");
    }

    function fillResponse($success, $message): array
    {
        return [
            "success" => $success,
            "message" => $message
        ];
    }
    function getConnection(): PDO
    {
        $connection = new Connection();
        return $connection->pdo_connect_mysql();
    }

}