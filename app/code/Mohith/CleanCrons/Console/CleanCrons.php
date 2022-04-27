<?php
/**
 * Created by PhpStorm.
 * User: mohith
 * Date: 27/4/22
 * Time: 4:22 PM
 */

namespace Mohith\CleanCrons\Console;

use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanCrons
 * @package Mohith\CleanCrons\Console
 */
class CleanCrons extends Command
{
    /**
     * @var ResourceConnection
     */
    protected $connection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * CleanCrons constructor.
     * @param ResourceConnection $connection
     * @param LoggerInterface $logger
     * @param null $name
     */
    public function __construct(
        ResourceConnection $connection,
        LoggerInterface $logger,
        $name = null
    )
    {
        $this->connection = $connection;
        $this->logger = $logger;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('mohith:clean:crons');
        $this->setDescription('Cleans the crons that had been executed successfully in the last 24 hrs');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $dbConnection = $this->connection->getConnection();
            $sql = "DELETE FROM cron_schedule WHERE  scheduled_at < Date_sub(Now(), interval 24 hour) AND status = \"success\";";
            $dbConnection->query($sql);
            $output->writeln("Database table cron_schedule cleaned");
        } catch (\Exception $e) {
            $this->logger->critical(sprintf('Cron cleanup error: %s', $e->getMessage()));
        }
    }
}