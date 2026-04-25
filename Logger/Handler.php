<?php

declare(strict_types=1);

namespace MageMatch\CustomerPasswordResetCli\Logger;

use Monolog\Logger as MonologLogger;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    protected $loggerType = MonologLogger::INFO;

    protected $fileName = '/var/log/passwordreset.log';
}
