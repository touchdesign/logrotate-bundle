<?php

declare(strict_types=1);

namespace Touchdesign\LogrotateBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Touchdesign\Logrotate\Loader\LogfileLoader;
use Touchdesign\Logrotate\Worker\RotateWorker;

/**
 * @author Christin Gruber
 */
class LogrotateRotateCommand extends Command
{
    /**
     * @var string path to log file
     */
    protected $logfile;

    /**
     * @var int number of log file to keep
     */
    protected $keep;

    public function __construct(int $keep)
    {
        $this->keep = $keep;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('touch:logrotate:rotate')
            ->setDescription('Rotate log file.')
            ->addArgument(
                'logfile',
                InputArgument::REQUIRED,
                'Path to log file.'
            )
            ->addOption(
                'keep',
                'k',
                InputOption::VALUE_OPTIONAL,
                'Number of log files to keep.',
                $this->keep
            );
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->logfile = $input->getArgument('logfile');
        $this->keep = (int) ($input->getOption('keep'));
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $worker = new RotateWorker(
            (new LogfileLoader($this->logfile))
        );

        $worker->run($this->keep);

        $io->success(
            sprintf(
                'Log file "%s" successful rotated, keep "%d" versions.',
                $this->logfile,
                $this->keep
            )
        );

        return Command::SUCCESS;
    }
}
