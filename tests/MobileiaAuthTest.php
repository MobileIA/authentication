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
        // Verificamos si el Access Token es valido
        $this->assertTrue($library->isValidAccessToken('tbEC38JTOeMBUE'));
    }
}