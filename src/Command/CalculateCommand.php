<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Order\Price\OrderItemPrice;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Service\ProductPriceUserFetcher;
use App\Tests\Util\DataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(name: 'app:calculate')]
class CalculateCommand extends Command
{
    public function __construct(
        private readonly ProductPriceUserFetcher $productPriceUserFetcher,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $products = $this->entityManager->getRepository(Product::class)->findBy([]);

        $bar = $io->createProgressBar(count($products));
        
        $user = $this->entityManager->getRepository(User::class)->find(199);

        /** @var OrderItemPrice[] $prices */
        $prices = [];
        foreach ($products as $product){
            $price = $this->productPriceUserFetcher->fetch($user, $product);

            $prices[$product->getId()] = $price->getPrice();

            $bar->advance();
        }

        $bar->finish();

        sort($prices, SORT_NUMERIC);

        return Command::SUCCESS;
    }

}
