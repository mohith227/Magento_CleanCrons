<?php
/**
 * Created by PhpStorm.
 * User: mohith
 * Date: 27/4/22
 * Time: 4:23 PM
 */

namespace Mohith\CleanCrons\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Mohith\CleanCrons\Model\Config;
use Psr\Log\LoggerInterface;

/**
 * Class CleanCrons
 * @package Mohith\CleanCrons\Cron
 */
class CleanCrons
{

    /**
     * @var Config
     */
    private $config;
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * Directory List
     *
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * CleanCrons constructor.
     * @param Config $config
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        DirectoryList $directoryList,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->directoryList = $directoryList;
    }

    public function execute()
    {
        try {
            if ($this->config->getIsActive()) {
                $rootPath = $this->directoryList->getRoot();
                $command = "php " . $rootPath . "/bin/magento mohith:clean:crons";
                $access_log = $rootPath . "/var/log/mohith_cleanCrons_access.log";
                $error_log = $rootPath . "/var/log/mohith_cleanCrons_error.log";
                shell_exec($command . " > $access_log 2> $error_log &");
            }
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('Cron cleanup error: %s', $e->getMessage()));
        }
    }
}
