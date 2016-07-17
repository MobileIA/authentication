<?php

namespace MobileIA\Auth;

/**
 * Description of MobileiaUserTest
 *
 * @author matiascamiletti
 */
class MobileiaAuthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that true does in fact equal true
     */
    public function testInit()
    {
        $this->assertTrue(true);
    }
    
    public function testIsValidAccessToken()
    {
        // Iniciamos la libreria
        $library = new MobileiaAuth(2, '$2y$10$yfxndt.xX5OatbEC38JTOeMBUEA114poy4kXYJ5ALuYlN2kCHaDTy');
        // Verificamos un Access Token valido
        $this->assertTrue($library->isValidAccessToken('85532e2b761b5597e15f4dd7c5c1f90d090febbf'));
        // Verificamos un Access Token invalido
        $this->assertFalse($library->isValidAccessToken('tbEC38JTOeMBUE'));
    }
}