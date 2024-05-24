<?php

require __DIR__ . '/vendor/autoload.php';

include __DIR__ . '/config/templates.php';

use config\Connection;
use controllers\ContactController;

$response = [];
$connection = new Connection();
$pdo = $connection->pdo_connect_mysql();
if (isset($_GET['id'])) {
    $stmt = $pdo->prepare('SELECT * FROM contacts WHERE id = ?');
    $stmt->execute([$_GET['id']]);
    $contact = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$contact) {
        exit('Contact doesn\'t exist with that ID!');
    }
    if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
        $contactController = new ContactController();
        $response = $contactController->deleteContact($_GET['id'], $_GET['confirm']);
    } else if (isset($_GET['confirm']) && $_GET['confirm'] == 'no') {
        header('Location: read.php');
    }
}
?>


<?= template_header('Deletar') ?>

<div class="content delete">
    <h2>Deletar Contato #<?= $contact['id'] ?></h2>
    <?php if (!empty($response)): ?>
        <div style="margin-top:25px">
            <div class="alert alert-<?= $response["success"] ? 'success' : 'danger'; ?>" role="alert">
                <?= $response["message"] ?>
            </div>
        </div>
        <a href="read.php">
            <button type="button" class="btn btn-primary">Voltar para a listagem</button>
        </a>
    <?php else: ?>
        <p>Tem certeza que quer deleter este contato? #<?= $contact['id'] . ' - ' . $contact['name'] ?>?</p>
        <div class="yesno">
            <a href="delete.php?id=<?= $contact['id'] ?>&confirm=yes">Yes</a>
            <a href="delete.php?id=<?= $contact['id'] ?>&confirm=no">No</a>
        </div>
    <?php endif; ?>
</div>

<?= template_footer() ?>
