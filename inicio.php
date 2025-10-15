<?php
include "Soporte.php";
include "CintaVideo.php";
include "Dvd.php";

// Probamos con un DVD
$miDvd = new Dvd("Origen", 24, 15, "es,en,fr", "16:9");

echo "<strong>" . $miDvd->titulo . "</strong><br>";
echo "Precio: " . $miDvd->getPrecio() . " euros<br>";
echo "Precio IVA incluido: " . $miDvd->getPrecioConIva() . " euros<br>";
$miDvd->muestraResumen();
?>