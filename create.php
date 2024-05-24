<?php

require __DIR__ . '/vendor/autoload.php';

use controllers\ContactController;

include __DIR__ . '/config/templates.php';

$response = [];
if (!empty($_POST)) {
    $contactController = new ContactController();
    $response = $contactController->createContact($_POST);
}
?>

<?= template_header('Create') ?>

<div class="content update">
    <div style="margin-top:25px">
        <?php if (!empty($response)): ?>
            <div class="alert alert-<?= $response["success"] ? 'success' : 'danger'; ?>" role="alert">
                <?= $response["message"] ?>
            </div>
        <?php endif; ?>
    </div>
    <div class="container mt-5">
        <h2>Create Contact</h2>
        <form action="create.php" method="post">
            <div class="form-group">
                <label for="name">Nome</label>
                <input type="text" class="form-control" name="name" placeholder="John Doe" id="name" required>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" name="email" placeholder="johndoe@example.com" id="email" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="phone">Telefone</label>
                    <input type="text" class="form-control" name="phone" placeholder="44 99899-9999" id="phone" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="title">Titulo</label>
                    <input type="text" class="form-control" name="title" placeholder="Employee" id="title" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="created">Criado Em</label>
                    <input type="datetime-local" class="form-control" name="created" value="<?= date('Y-m-d\TH:i') ?>" id="created" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
        </form>
    </div>

</div>

<?= template_footer() ?>
