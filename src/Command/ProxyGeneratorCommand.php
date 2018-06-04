<?php

namespace App\Command;

use App\Manager\ORM\Proxy\ProxyManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ProxyGeneratorCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'proxy:generator';

    protected function configure()
    {
        $this
            ->setDescription('Generate all proxies class for all models')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->getContainer()->get(ProxyManager::class)->warmUp();

        $io->success('All proxies was generated ! ENJOY');
    }
}
