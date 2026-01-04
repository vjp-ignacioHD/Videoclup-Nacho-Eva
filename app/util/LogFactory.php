<?php

namespace Dwes\VideoClub\Util;

use Monolog\Logger;
use Monolog\LoggerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class LogFactory
{
    public static function createLogger(string $channel = 'VideoclubLogger'): Logger // ✅ Cambiar a Logger, no LoggerInterface
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

        return $logger; // ✅ Esto está bien porque Logger implementa LoggerInterface
    }
}