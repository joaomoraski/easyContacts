<?php

namespace utilsTests;

use controllers\EnvReader;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\TestCase;
use Mockery as m;


class EnvVariableTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    #[TestDox("Testa se as variaveis de ambiente estão sendo lidas.")]

    public function testGetEnvVariable()
    {
        // Mocking the file_exists function
        m::mock('alias:file_exists')
            ->shouldReceive('__invoke')
            ->with(m::type('string'))
            ->andReturnUsing(function($path) {
                if ($path === __DIR__ . '/../.env') {
                    return true;
                }
                return false;
            });

        // Mocking the file function
        m::mock('alias:file')
            ->shouldReceive('__invoke')
            ->with(m::type('string'), m::type('int'))
            ->andReturnUsing(function($path) {
                if ($path === __DIR__ . '/../.env.example') {
                    return [
                        'DATABASE_HOST=',
                        'DATABASE_USER=',
                        'DATABASE_PASSWORD=',
                        'DATABASE_NAME='
                    ];
                }
                return [];
            });

        $envReader = new EnvReader();
        $this->assertEquals('', $envReader->getEnvVariable('DATABASE_HOST', true));
        $this->assertEquals('', $envReader->getEnvVariable('DATABASE_USER', true));
        $this->assertEquals('', $envReader->getEnvVariable('DATABASE_PASSWORD', true));
        $this->assertEquals('', $envReader->getEnvVariable('DATABASE_NAME', true));
        $this->assertNull($envReader->getEnvVariable('NON_EXISTENT_KEY', true));
    }
}
