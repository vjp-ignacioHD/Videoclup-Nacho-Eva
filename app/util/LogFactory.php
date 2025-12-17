<?php

namespace Dwes\VideoClub\Util;

use Monolog\Logger;
use Monolog\LoggerInterface; // ✅ Importamos la interfaz
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class LogFactory
{
    public static function createLogger(string $channel = 'VideoclubLogger'): LoggerInterface // ✅ Tipo de retorno cambiado
    {
        $logger = new Logger($channel);
        $logPath = __DIR__ . '/../../logs/videoclub.log';

        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }

        $handler = new StreamHandler($logPath, Logger::DEBUG);
        $formatter = new LineFormatter("[%datetime%] %channel%.%level_name%: %message% %context%\n");
        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger; // ✅ Logger implementa LoggerInterface → válido
    }
}