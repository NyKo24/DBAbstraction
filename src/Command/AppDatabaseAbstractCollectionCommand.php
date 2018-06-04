<?php

namespace App\Command;

use App\Manager\ORM\WyndpayObjectManager;
use App\Model\Operation;
use App\Model\Wallet;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppDatabaseAbstractCollectionCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'database:abstract-collection';
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
            ->setDescription('Test with collection model')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $walletUid = uniqid();

        $operation1 = new Operation();
        $operation1->setStatus('good');
        $operation1->setLabel('mon ope 1');
        $operation1->setAmount(123.00);

        $operation2 = new Operation();
        $operation2->setStatus('fail');
        $operation2->setLabel('mon ope 2');
        $operation2->setAmount(321.12);

        $wallet = new Wallet();
        $wallet->setStatus('ok');
        $wallet->setLabel('Je suis un wallet');
        $wallet->setType('CPP');
        $wallet->setOverdraft(true);
        $wallet->setBalance(0.00);
        $wallet->setUuid($walletUid);
        $wallet->addOperation($operation1);
        $wallet->addOperation($operation2);

        $this->wyndpayObjectManager->persist($wallet);
        $this->wyndpayObjectManager->flush();

        $walletModelRepository = $this->wyndpayObjectManager->getRepository(Wallet::class);
        /** @var Wallet $wallet */
        $wallet = $walletModelRepository->findOneByUuid($walletUid);

        $io->success(sprintf('Wallet %s has %s operations', $wallet->getId(), $wallet->getOperations()->count()));

        /** @var Operation $operation */
        foreach ($wallet->getOperations() as $operation) {
            $io->success(sprintf('Operation %s amount %s', $operation->getId(), $operation->getAmount()));
        }
    }
}
