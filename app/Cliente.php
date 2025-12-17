<?php

namespace App;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

// Aseguramos que la carpeta logs exista (opcional, pero útil)
if (!is_dir(__DIR__ . '/../logs')) {
    mkdir(__DIR__ . '/../logs', 0777, true);
}

class Cliente
{
    // ATRIBUTOS
    public $nombre;
    public $email; // ← ¡Necesario! Lo usas en el constructor.
    public $numero;
    private $soportesAlquilados = [];
    private $numSoportesAlquilados = 0;
    private $maxAlquilerConcurrente;
    private $logger;

    // NUEVOS ATRIBUTOS: user y password
    private $user;
    private $password;

    // CONSTRUCTOR
    public function __construct($nombre, $email)
    {
        $this->nombre = $nombre;
        $this->email = $email;

        // ✅ Inicializar Monolog: canal "VideoclubLogger"
        $this->logger = new Logger('VideoclubLogger');

        // ✅ Handler que escribe en logs/videoclub.log (nivel DEBUG)
        $handler = new StreamHandler(__DIR__ . '/../logs/videoclub.log', Logger::DEBUG);

        // ✅ Formato legible (incluye fecha, nivel, mensaje)
        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message%\n",
            null,
            true,   // permite arrays en contexto
            true    // deja escapar HTML/saltos (útil si depuras)
        );
        $handler->setFormatter($formatter);

        $this->logger->pushHandler($handler);
    }

    // GETTERS
    public function getNumero()
    {
        return $this->numero;
    }

    public function getNumSoportesAlquilados()
    {
        return $this->numSoportesAlquilados;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getMaxAlquilerConcurrente()
    {
        return $this->maxAlquilerConcurrente;
    }

    // SETTERS
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function setMaxAlquilerConcurrente($max)
    {
        $this->maxAlquilerConcurrente = $max;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    // MÉTODOS DE GESTIÓN

    public function tieneAlquilado(Soporte $s): bool
    {
        foreach ($this->soportesAlquilados as $soporteAlquilado) {
            if ($soporteAlquilado->getNumero() == $s->getNumero()) {
                return true;
            }
        }
        return false;
    }

    public function alquilar(Soporte $s): Cliente
    {
        // ✅ Log de warning antes de excepción
        if ($this->tieneAlquilado($s)) {
            $this->logger->warning("Intento de alquilar soporte ya alquilado: {$s->titulo} por cliente {$this->nombre}");
            throw new SoporteYaAlquiladoException("El cliente ya tiene alquilado el soporte: " . $s->titulo);
        }

        if ($this->numSoportesAlquilados >= $this->maxAlquilerConcurrente) {
            $this->logger->warning("Cupo de alquiler superado para cliente {$this->nombre} (máx: {$this->maxAlquilerConcurrente})");
            throw new CupoSuperadoException("Este cliente tiene " . $this->maxAlquilerConcurrente . " elementos alquilados. No puede alquilar más");
        }

        $this->soportesAlquilados[] = $s;
        $this->numSoportesAlquilados++;
        $s->setAlquilado(true);

        // ✅ Reemplazar echo por log->info (no está en muestraResumen)
        $this->logger->info("Soporte '{$s->titulo}' alquilado a cliente: {$this->nombre}");

        return $this;
    }

    public function devolver(int $numSoporte): Cliente
    {
        foreach ($this->soportesAlquilados as $indice => $soporte) {
            if ($soporte->getNumero() == $numSoporte) {
                unset($this->soportesAlquilados[$indice]);
                $this->soportesAlquilados = array_values($this->soportesAlquilados);
                $this->numSoportesAlquilados--;
                $soporte->setAlquilado(false);

                // ✅ Log en lugar de echo
                $this->logger->info("{$this->nombre} devolvió el soporte: {$soporte->titulo} (Nº {$numSoporte})");
                return $this;
            }
        }

        // ✅ Log en lugar de echo
        $this->logger->info("{$this->nombre} intentó devolver soporte inexistente (Nº {$numSoporte})");
        return $this;
    }

    public function listarAlquileres(): void
    {
        // ✅ Todos los echo → logger->info
        $this->logger->info("{$this->nombre} tiene {$this->numSoportesAlquilados} soporte(s) alquilado(s)");

        if ($this->numSoportesAlquilados > 0) {
            foreach ($this->soportesAlquilados as $soporte) {
                $this->logger->info(" - {$soporte->titulo} (Nº: {$soporte->getNumero()})");
            }
        } else {
            $this->logger->info("No hay soportes alquilados actualmente.");
        }
    }

    public function getAlquileres(): array
    {
        return $this->soportesAlquilados;
    }

    // ✅ MÉTODO EXCEPCIÓN: se permite usar echo aquí
    public function muestraResumen()
    {
        echo "<strong>Nombre:</strong> " . $this->nombre . "<br>";
        echo "<strong>Número de cliente:</strong> " . $this->numero . "<br>";
        echo "<strong>Usuario:</strong> " . $this->user . "<br>";
        echo "<strong>Cantidad de alquileres:</strong> " . $this->numSoportesAlquilados . "<br>";

        if ($this->numSoportesAlquilados > 0) {
            echo "<strong>Soportes alquilados:</strong><br>";
            foreach ($this->soportesAlquilados as $soporte) {
                echo " - " . $soporte->titulo . " (Nº: " . $soporte->getNumero() . ")<br>";
            }
        } else {
            echo "<strong>Soportes alquilados:</strong> Ninguno<br>";
        }
    }
}