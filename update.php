<?php

require __DIR__ . '/vendor/autoload.php';

include __DIR__ . '/config/templates.php';

use config\Connection;
use controllers\ContactController;

$response = [];
if (isset($_GET['id'])) {
    if (!empty($_POST)) {
        $contactController = new ContactController();
        $response = $contactController->updateContact($_POST, $_GET['id']);
    }
    $connection = new Connection();
    $pdo = $connection->pdo_connect_mysql();
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$contact) {
        exit('Contact doesn\'t exist with that ID!');
    }
}
?>

<?= template_header('Update') ?>

<div class="content update">
    <div style="margin-top:25px">
        <?php if (!empty($response)): ?>
            <div class="alert alert-<?= $response["success"] ? 'success' : 'danger'; ?>" role="alert">
                <?= $response["message"] ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="container mt-5">
        <h2>Update Contact #<?= $contact['id'] ?></h2>
        <form action="update.php?id=<?= $contact['id'] ?>" method="post">
            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" class="form-control" name="name" placeholder="John Doe"
                       value="<?= $contact['name'] ?>" id="name" required>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="johndoe@example.com"
                           value="<?= $contact['email'] ?>" id="email" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="phone">Telefone</label>
                    <input type="text" class="form-control" name="phone" placeholder="44 99998-9999"
                           value="<?= $contact['phone'] ?>" id="phone" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="title">Titulo</label>
                    <input type="text" class="form-control" name="title" placeholder="Employee"
                           value="<?= $contact['title'] ?>" id="title" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="created">Criado Em</label>
                    <input type="datetime-local" class="form-control" name="created"
                           value="<?= date('Y-m-d\TH:i', strtotime($contact['created'])) ?>" id="created" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>


<?= template_footer() ?>

