<?php

namespace App\Command;

use App\Manager\ORM\WyndpayObjectManager;
use App\Model\Currency;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppDatabaseAbstractSimpleCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'database:abstract-simple';
    /**
     * @var WyndpayObjectManager
     */
    private $wyndpayObjectManager;

    public function __construct(WyndpayObjectManager $wyndpayObjectManager)
    {
        parent::__construct(self::$defaultName);
        $this->wyndpayObjectManager = $wyndpayObjectManager;
    }

    protected function configure()
    {
        $this
            ->setDescription('Test with simple model')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        /** @var WyndpayObjectManager $entityManager */

        $currency = new Currency();
        $currency->setCode('EUR');
        $currency->setIso('EUR');

        $this->wyndpayObjectManager->persist($currency);
        $this->wyndpayObjectManager->flush();

        $currencyModelRepository = $this->wyndpayObjectManager->getRepository(Currency::class);
        $currency = $currencyModelRepository->findOneByCode('EUR');

        $io->success($currency->getId().' '.$currency->getCode());
    }
}
