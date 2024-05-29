<?php

namespace tests\contactTests;

use controllers\ContactController;
use model\Contact;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../vendor/autoload.php';

/**
 *
 */
class ContactControllerTest extends TestCase
{
    function mountExpectedContact(): Contact
    {
        $contact = new Contact();
        $contact->setName("John Doe");
        $contact->setEmail("john@doe.com");
        $contact->setPhone("0123456789");
        $contact->setTitle("Dad");
        $contact->setCreated("2024-05-20 02:30:00");
        return $contact;
    }

    #[TestDox("Testa se o ContactController pode ser instanciado")]
    public function testIfContactControllerCanBeInstantiated()
    {
        $controller = new ContactController();
        $this->assertInstanceOf(ContactController::class, $controller, "ContactController instance should be created");
    }

    #[TestDox("Testa se a entidade de Contato está valida")]
    public function testValidMountContactEntity()
    {
        $contactController = new ContactController();
        $contactResult = $contactController->mountContactEntity(
            [
                "name" => "John Doe",
                "email" => "john@doe.com",
                "phone" => "0123456789",
                "title" => "Dad",
                "created" => "2024-05-20 02:30:02"
            ]
        );
        $this->assertEquals($this->mountExpectedContact(), $contactResult);
    }

}
