<?php

namespace Ibtikar\ShareEconomyToolsBundle\Command;

use Ibtikar\ShareEconomyToolsBundle\Command\SingleRunCommandInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
abstract class ContinuousRunningCommand extends ContainerAwareCommand implements SingleRunCommandInterface
{

    /**
     * @var ObjectManager $em
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {

    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->writeln($this->commandLogPrefix . 'Started with proccess id ' . getmypid() . '.');
        }
        if (!gc_enabled()) {
            $output->writeln('<comment>Garbage collector is disabled trying to enable.</comment>');
            gc_enable();
            $output->writeln('<info>Garbage collector enabled.</info>');
        }
        $this->em = $this->getContainer()->get('doctrine')->getManager();
        while (true) {
            // sleep for .5 second
            usleep(500000);
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
