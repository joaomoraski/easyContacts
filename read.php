<?php

require __DIR__ . '/vendor/autoload.php';

include __DIR__ . '/config/templates.php';

use controllers\ContactController;

$contactController = new ContactController();

$response = $contactController->readContacts(isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1);
?>

<?= template_header('Read') ?>

<div class="content read">
    <h2>Listagem de contatos</h2>
    <a href="create.php" class="create-contact">Criar contato</a>
    <table>
        <thead>
        <tr>
            <td>#</td>
            <td>Nome</td>
            <td>Email</td>
            <td>Telefone</td>
            <td>Titulo</td>
            <td>Criado em</td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($response["contacts"] as $contact): ?>
            <tr>
                <td><?= $contact['id'] ?></td>
                <td><?= $contact['name'] ?></td>
                <td><?= $contact['email'] ?></td>
                <td><?= $contact['phone'] ?></td>
                <td><?= $contact['title'] ?></td>
                <td><?= $contact['created'] ?></td>
                <td class="actions">
                    <a href="update.php?id=<?= $contact['id'] ?>" class="edit"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete.php?id=<?= $contact['id'] ?>" class="trash"><i class="fas fa-trash fa-xs"></i></a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="pagination">
        <?php if ($response["page"] > 1): ?>
            <a href="read.php?page=<?= $response["page"] - 1 ?>"><i class="fas fa-angle-double-left fa-sm"></i></a>
        <?php endif; ?>
        <?php if ($response["page"] * $response["records_per_page"] < $response["num_contacts"]): ?>
            <a href="read.php?page=<?= $response["page"] + 1 ?>"><i class="fas fa-angle-double-right fa-sm"></i></a>
        <?php endif; ?>
    </div>
</div>

<?= template_footer() ?>
