<?php

namespace Ibtikar\ShareEconomyToolsBundle\Command;

use Ibtikar\ShareEconomyToolsBundle\Command\SingleRunCommandInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
abstract class ContinuousRunningCommand extends ContainerAwareCommand implements SingleRunCommandInterface
{

    /* @var $defaultSleepTime string */
    public static $defaultSleepTime = 500000;

    /**
     * @var ObjectManager $em
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->addOption('sleepTime', 's', InputOption::VALUE_REQUIRED, 'Halt time in microseconds. A microsecond is one millionth of a second.', ContinuousRunningCommand::$defaultSleepTime);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sleepTime = ContinuousRunningCommand::$defaultSleepTime;
        if ($input->hasOption('sleepTime')) {
            $userSleepTime = $input->getOption('sleepTime');
            if (is_numeric($userSleepTime) && $userSleepTime >= ContinuousRunningCommand::$defaultSleepTime) {
                $sleepTime = $userSleepTime;
            } else {
                $output->writeln("<error>sleepTime must be integer larger than or equal $sleepTime entered value is => $userSleepTime ignoring the entered value and using the default one => $sleepTime</error>");
            }
        }
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln('Started with proccess id ' . getmypid() . '.');
        }
        if (!gc_enabled()) {
            $output->writeln('<comment>Garbage collector is disabled trying to enable.</comment>');
            gc_enable();
            $output->writeln('<info>Garbage collector enabled.</info>');
        }
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        while (true) {
            // sleep for .5 second
            usleep($sleepTime);
            $this->commandLogic();
            // free the memory from all previous objects
            $this->em->clear();
            gc_collect_cycles();
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract protected function commandLogic();
}
