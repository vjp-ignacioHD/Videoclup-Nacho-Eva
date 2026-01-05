<?php
require_once 'vendor/autoload.php';
use App\Cliente;
use App\Soporte;

$c = new Cliente("Ana", "ana@test.com");
$c->setNumero(1);
$c->setMaxAlquilerConcurrente(1);
$c->setUser("ana123");
$c->setPassword("123");

$s = new Soporte("Matrix", 101);
$c->alquilar($s);
$c->muestraResumen(); // ← esto debe aparecer en pantalla