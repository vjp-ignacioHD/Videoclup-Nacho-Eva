<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../vendor/autoload.php';

class SimpleVideoclubTest extends TestCase
{
    public function testVideoclubExists()
    {
        $this->assertTrue(class_exists('App\Videoclub'));
    }
    
    public function testCreateVideoclub()
    {
        $videoclub = new App\Videoclub('Test');
        $this->assertInstanceOf('App\Videoclub', $videoclub);
    }
    
    public function testBasicFunctionality()
    {
        $videoclub = new App\Videoclub('Test');
        
        // Incluir socio
        $videoclub->incluirSocio('Juan');
        $this->assertEquals(1, $videoclub->getNumSocios());
        
        // Incluir DVD
        $videoclub->incluirDvd('Película', 10, 'ES', '16:9');
        $this->assertEquals(1, $videoclub->getNumProductos());
        
        // Alquilar
        $videoclub->alquilaSocioProducto(1, 0);
        $this->assertEquals(1, $videoclub->getNumProductosAlquilados());
    }
    
    public function testExceptionSocioNoExiste()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Socio');
        
        $videoclub = new App\Videoclub('Test');
        $videoclub->incluirDvd('Película', 10, 'ES', '16:9');
        $videoclub->alquilaSocioProducto(999, 0);
    }
}