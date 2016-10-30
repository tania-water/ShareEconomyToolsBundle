<?php

namespace Ibtikar\ShareEconomyToolsBundle\Listener;

use Ibtikar\ShareEconomyToolsBundle\Command\SingleRunCommandInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Mahmoud Mostafa <mahmoud.mostafa@ibtikar.net.sa>
 */
class SingleRunCommandConsoleSubscriber implements EventSubscriberInterface
{

    /**
     * @var Filesystem
     */
    private $fileSystem;

    /**
     * @var string
     */
    private $commandsRunDirectory;

    /**
     * @var string
     */
    private $commandProccessIdFileName;

    /**
     * @var boolean
     */
    private $previousCommandRunning = false;

    public function __construct($kernelRootDirectory)
    {
        $this->commandsRunDirectory = $kernelRootDirectory . '/../var/run/';
        $this->fileSystem = new Filesystem();
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onStart(ConsoleCommandEvent $event)
    {
        $runningCommand = $event->getCommand();
        if ($runningCommand instanceof SingleRunCommandInterface) {
            $this->commandProccessIdFileName = $this->commandsRunDirectory . str_replace(':', '-', $runningCommand->getName()) . '.pid';
            if ($this->fileSystem->exists($this->commandProccessIdFileName)) {
                $this->previousCommandRunning = true;
                throw new \Exception('Command arlready started with proccess id ' . file_get_contents($this->commandProccessIdFileName));
            } else {
                $this->fileSystem->dumpFile($this->commandProccessIdFileName, getmypid());
            }
        }
    }

    /**
     * @param ConsoleCommandEvent $event
     */
    public function onTerminate(ConsoleTerminateEvent $event)
    {
        if (!$this->previousCommandRunning) {
            $this->fileSystem->remove($this->commandProccessIdFileName);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ConsoleEvents::COMMAND => 'onStart',
            ConsoleEvents::TERMINATE => 'onTerminate'
        );
    }
}
