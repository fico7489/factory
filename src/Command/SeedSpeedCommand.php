<?php

namespace App\Command;

use App\Entity\Product;
use App\Entity\User;
use App\Entity\UserGroup;
use App\Tests\Util\DataProvider;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
        $categories = $this->prepareCategories($io);
        $productSkus = $this->prepareProducts($io, $categories);

        $this->prepareContractList($io, $userIds, $productSkus);
        // $this->preparePriceList($io, $userGroups, $productSkus);

        return Command::SUCCESS;
    }

    /** @return UserGroup[] */
    private function prepareUserGroups(SymfonyStyle $io): array
    {
        $userGroups = [];

        $userGroups[] = $this->dataProvider->createUserGroup('Repairman', false);
        $userGroups[] = $this->dataProvider->createUserGroup('Gold', false);

        $count = 100;

        $io->writeln("\n".'User groups');
        $bar = $io->createProgressBar($count);

        for ($i = 0; $i < $count; ++$i) {
            $userGroups[] = $this->dataProvider->createUserGroup(Uuid::uuid4(), false);
            $bar->advance();
        }

        $this->entityManager->flush();

        $bar->finish();

        return $userGroups;
    }

    private function prepareUsers(SymfonyStyle $io, $userGroups): array
    {
        $userGroupRepairman = $this->entityManager->getRepository(UserGroup::class)->findOneBy(['name' => 'Repairman']);
        $userGroupGold = $this->entityManager->getRepository(UserGroup::class)->findOneBy(['name' => 'Gold']);

        $users = [];
        $users[] = $this->dataProvider->createUser([], 'admin@example.com', false);
        $users[] = $this->dataProvider->createUser([], 'regular@example.com', false);
        $users[] = $this->dataProvider->createUser([$userGroupRepairman], 'repairman@example.com', false);
        $users[] = $this->dataProvider->createUser([$userGroupGold], 'gold@example.com', false);
        $users[] = $this->dataProvider->createUser([$userGroupRepairman, $userGroupGold], 'gold_and_repairman@example.com', false);

        $count = 1000;

        // create 1000 users
        $io->writeln("\n".'Users');
        $bar = $io->createProgressBar($count);

        for ($i = 0; $i < $count; ++$i) {
            $users[] = $this->dataProvider->createUser($userGroups[array_rand($userGroups)], false);
            $bar->advance();
        }

        $this->entityManager->flush();

        $usersIds = [];
        foreach ($users as $user) {
            $usersIds[] = $user->getId();
        }

        $bar->finish();

        return $usersIds;
    }

    private function prepareCategories(SymfonyStyle $io): array
    {
        $categories = [];
        $categories[] = $this->dataProvider->createCategory('PC');
        $categories[] = $this->dataProvider->createCategory('Laptop', $categories[0]);
        $categories[] = $this->dataProvider->createCategory('Gaming', $categories[1]);
        $categories[] = $this->dataProvider->createCategory('For Work', $categories[1]);
        $categories[] = $this->dataProvider->createCategory('Desktop', $categories[0]);
        $categories[] = $this->dataProvider->createCategory('Cell Phone');
        $categories[] = $this->dataProvider->createCategory('Smartphone', $categories[5]);
        $categories[] = $this->dataProvider->createCategory('Charger', $categories[5]);
        $categories[] = $this->dataProvider->createCategory('Monitor');

        return $categories;
    }

    private function prepareProducts(SymfonyStyle $io, array $categories): array
    {
        $productSkus = [];
        $productSkus[] = $this->dataProvider->createProduct(1500, 'aaaa-1111', 'LENOVO ThinkPad T14s', null, false)->getSku();

        $count = 2000;

        $io->writeln("\n".'Products');
        // create 20k products
        $bar = $io->createProgressBar($count);

        for ($i = 0; $i < $count; ++$i) {
            $sku = Uuid::uuid4();
            $price = rand(1, 100000).'.'.rand(1, 99);
            $productSkus[] = $this->dataProvider->createProduct($price, $sku, 'test', null, false)->getSku();
            $bar->advance();
        }

        $this->entityManager->flush();

        $bar->finish();

        return $productSkus;
    }

    private function preparePriceList(SymfonyStyle $io, array $userGroups, array $productSkus): void
    {
        $count = count($userGroups) * count($productSkus);

        $io->writeln("\n".'Price Lists');
        $bar = $io->createProgressBar($count);

        foreach ($userGroups as $userGroup) {
            foreach ($productSkus as $productSku) {
                $price = rand(1, 100000).'.'.rand(1, 99);

                $this->dataProvider->createPriceList($userGroup, $productSku, $price, false);

                $bar->advance();
            }
        }

        $this->entityManager->flush();

        $bar->finish();
    }

    private function prepareContractList(SymfonyStyle $io, $userIds, $productSkus): void
    {
        $count = count($userIds) * count($productSkus);

        // create 1000 users
        $io->writeln("\n".'Contract Lists');
        $bar = $io->createProgressBar($count);

        $i = 0;

        $msStart = floor(microtime(true) * 1000);
        $contactLists = [];
        foreach ($userIds as $userId) {
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            foreach ($productSkus as $productSku) {
                $price = rand(1, 100000).'.'.rand(1, 99);
                /* @var Product $product */

                $contactLists[] = $this->dataProvider->createContractList($user, $productSku, $price, false);

                $bar->advance();
            }
        }

        $this->entityManager->flush();

        $msFinish = floor(microtime(true) * 1000);
        $io->writeln('ms All='.($msFinish - $msStart));

        $bar->finish();
    }
}
