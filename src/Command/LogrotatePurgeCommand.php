<?php

declare(strict_types=1);

namespace Touchdesign\LogrotateBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Touchdesign\Logrotate\Loader\LogfileLoader;
use Touchdesign\Logrotate\Worker\PurgeWorker;

/**
 * @author Christin Gruber
 */
class LogrotatePurgeCommand extends Command
{
    /**
     * @var string path to log file
     */
    protected $logfile;

    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('touch:logrotate:purge')
            ->setDescription('Purge log files.')
            ->addArgument(
                'logfile',
                InputArgument::REQUIRED,
                'Path to log file.'
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->logfile = $input->getArgument('logfile');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $worker = new PurgeWorker(
            $loader = (new LogfileLoader($this->logfile))
        );

        if ($count = \iterator_count($loader->all())) {
            if (!$io->confirm(
                sprintf(
                    'Sure you will purge "%d" log files for "%s"?',
                    $count,
                    $this->logfile
                ),
                false
            )) {
                return Command::FAILURE;
            }
        }

        $worker->run();

        $io->success(
            sprintf(
                'Successful purged log files for "%s".',
                $this->logfile
            )
        );

        return Command::SUCCESS;
    }
}
