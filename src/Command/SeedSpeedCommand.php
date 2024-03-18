<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Tests\Util\DataProvider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(name: 'app:seed-speed')]
class SeedSpeedCommand extends Command
{
    private DataProvider $dataProvider;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();

        $this->dataProvider = new DataProvider($this->entityManager);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $userGroups = $this->prepareUserGroups($io);
        $userIds = $this->prepareUsers($io, $userGroups);
        $productSkus = $this->prepareProducts($io);

        $this->prepareContractList($io, $userIds, $productSkus);
        //$this->preparePriceList($io, $userGroups, $productSkus);

        return Command::SUCCESS;
    }

    private function preparePriceList(SymfonyStyle $io, array $userGroups, array $productSkus):void{
        $count = count($userGroups) * count($productSkus);

        $io->writeln("\n".'Price Lists');
        $bar = $io->createProgressBar($count);

        foreach ($userGroups as $userGroup){
            foreach ($productSkus as $productSku){
                $price = rand(1, 100000) .'.' . rand(1, 99);

                $this->dataProvider->createPriceList($userGroup, $productSku, $price, false);

                $bar->advance();
            }
        }

        $this->entityManager->flush();

        $bar->finish();
    }

    private function prepareContractList(SymfonyStyle $io, $userIds, $productSkus):void
    {
        $count = count($userIds) * count($productSkus);

        //create 1000 users
        $io->writeln("\n".'Contract Lists');
        $bar = $io->createProgressBar($count);

        $i = 0;

        $millisecondsAll = floor(microtime(true) * 1000);
        $milliseconds = floor(microtime(true) * 1000);
        $contactLists = [];
        foreach ($userIds as $userId){
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            foreach ($productSkus as $productSku){
                $price = rand(1, 100000) .'.' . rand(1, 99);
                /** @var Product $product */

                $contactLists[] = $this->dataProvider->createContractList($user, $productSku, $price, false);
                $i++;

                $bar->advance();
            }

            if($i > 10000){
                $this->entityManager->flush();
                $this->entityManager->clear();

                $io->writeln('ms=' . (floor(microtime(true) * 1000)) - $milliseconds);
                $i = 0;
                $milliseconds = floor(microtime(true) * 1000);
            }
        }

        $this->entityManager->flush();

        $io->writeln('ms All=' . (floor(microtime(true) * 1000)) - $millisecondsAll);

        $bar->finish();
    }

    /** @return User[] */
    private function prepareUsers(SymfonyStyle $io, $userGroups):array
    {
        $count = 1000;

        //create 1000 users
        $io->writeln("\n".'Users');
        $bar = $io->createProgressBar($count);

        $users = [];
        for ($i=0 ; $i < $count; $i++){
            $users[] = $this->dataProvider->createUser($userGroups[array_rand($userGroups)], false);
            $bar->advance();
        }

        $this->entityManager->flush();

        $usersIds = [];
        foreach ($users as $user){
            $usersIds[] = $user->getId();
        }

        $bar->finish();

        return $usersIds;
    }

    /** @return UserGroup[] */
    private function prepareUserGroups(SymfonyStyle $io):array
    {
        $count = 100;

        $io->writeln("\n".'User groups');
        $bar = $io->createProgressBar($count);

        $userGroups = [];
        for ($i=0 ; $i < $count; $i++){
            $userGroups[] = $this->dataProvider->createUserGroup(Uuid::uuid4(), false);
            $bar->advance();
        }

        $this->entityManager->flush();

        $bar->finish();

        return $userGroups;
    }

    /** @return Product[] */
    private function prepareProducts(SymfonyStyle $io):array
    {
        $count = 2000;

        $io->writeln("\n".'Products');
        //create 20k products
        $bar = $io->createProgressBar($count);

        $productSkus = [];
        for ($i=0 ; $i < $count; $i++){
            $sku = Uuid::uuid4();
            $price = rand(1, 100000) .'.' . rand(1, 99);
            $productSkus[] = $this->dataProvider->createProduct($price, $sku, null, false)->getSku();
            $bar->advance();
        }

        $this->entityManager->flush();

        $bar->finish();

        return $productSkus;
    }
}
