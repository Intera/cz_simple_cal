<?php
declare(strict_types=1);

namespace Tx\CzSimpleCal\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class IndexEventsCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Update event index')
            ->setHelp('Refereshes the index of events that have not been updated since minIndexAge')
            ->addOption(
                'minIndexAge',
                'a',
                InputOption::VALUE_REQUIRED,
                'UID of a specific task',
                '-1 month'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandHandler = GeneralUtility::makeInstance(IndexEventsCommandHandler::class);
        $commandHandler->execute($input->getOption('minIndexAge'));
    }
}
