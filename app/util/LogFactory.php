<?php

namespace Dwes\VideoClub\Util;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class LogFactory
{
    public static function createLogger(string $channel = 'VideoclubLogger'): Logger
    {
        // Crear el logger
        $logger = new Logger($channel);

        // Crear el handler para escribir en logs/videoclub.log
        $logPath = __DIR__ . '/../../logs/videoclub.log';
        
        // Asegurarse de que la carpeta logs exista
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $handler = new StreamHandler($logPath, Logger::DEBUG);

        // Formato legible
        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context%\n"
        );
        $handler->setFormatter($formatter);

        $logger->pushHandler($handler);

        return $logger;
    }
}