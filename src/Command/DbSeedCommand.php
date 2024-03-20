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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:db:seed')]
class DbSeedCommand extends Command
{
    private DataProvider $dataProvider;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct();

        $this->dataProvider = new DataProvider($this->entityManager);
    }

    protected function configure(): void
    {
        $this
            ->addOption('count-product', null, InputOption::VALUE_OPTIONAL, '', 100)
            ->addOption('count-user', null, InputOption::VALUE_OPTIONAL, '', 100)
            ->addOption('count-user-group', null, InputOption::VALUE_OPTIONAL, '', 100)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $countProduct = $input->getOption('count-product');
        $countUser = $input->getOption('count-user');
        $countUserGroup = $input->getOption('count-user-group');

        $io = new SymfonyStyle($input, $output);

        $userGroups = $this->prepareUserGroups($io, $countUserGroup);
        $userIds = $this->prepareUsers($io, $countUser, $userGroups);
        $categories = $this->prepareCategories($io);
        $productSkus = $this->prepareProducts($io, $countProduct, $categories);

        $this->prepareContractList($io, $userIds, $productSkus);
        $this->preparePriceList($io, $userGroups, $productSkus);

        $io->writeln("\n");

        return Command::SUCCESS;
    }

    /** @return UserGroup[] */
    private function prepareUserGroups(SymfonyStyle $io, $count): array
    {
        $userGroups = [];

        $userGroups[] = $this->dataProvider->createUserGroup('Admin', false);
        $userGroups[] = $this->dataProvider->createUserGroup('Repairman', false);
        $userGroups[] = $this->dataProvider->createUserGroup('Gold', false);

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

    private function prepareUsers(SymfonyStyle $io, $count, $userGroups): array
    {
        $userGroupAdmin = $this->entityManager->getRepository(UserGroup::class)->findOneBy(['name' => 'Admin']);
        $userGroupRepairman = $this->entityManager->getRepository(UserGroup::class)->findOneBy(['name' => 'Repairman']);
        $userGroupGold = $this->entityManager->getRepository(UserGroup::class)->findOneBy(['name' => 'Gold']);

        $users = [];
        $users[] = $this->dataProvider->createUser([$userGroupAdmin], 'admin@example.com', false);
        $users[] = $this->dataProvider->createUser([], 'regular@example.com', false);
        $users[] = $this->dataProvider->createUser([$userGroupRepairman], 'repairman@example.com', false);
        $users[] = $this->dataProvider->createUser([$userGroupGold], 'gold@example.com', false);
        $users[] = $this->dataProvider->createUser([$userGroupRepairman, $userGroupGold], 'gold_and_repairman@example.com', false);

        $io->writeln("\n".'Users');
        $bar = $io->createProgressBar($count);

        for ($i = 0; $i < $count; ++$i) {
            $users[] = $this->dataProvider->createUser([$userGroups[array_rand($userGroups)]], false);
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
        $io->writeln("\n".'Categories');

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

    private function prepareProducts(SymfonyStyle $io, $count, array $categories): array
    {
        $productSkus = [];
        $productSkus[] = $this->dataProvider->createProduct(1500, 'aaaa-1111', 'LENOVO ThinkPad T14s', [$categories[2], $categories[3]], false)->getSku();
        $productSkus[] = $this->dataProvider->createProduct(1600, 'aaaa-2222', 'ACER Aspire 3 NX.ADDEX', [$categories[3]], false)->getSku();
        $productSkus[] = $this->dataProvider->createProduct(1700, 'aaaa-3333', 'Laptop ACER Nitro 5 NH.QH1EX.00V', [$categories[2]], false)->getSku();
        $productSkus[] = $this->dataProvider->createProduct(1000, 'aaaa-4444', 'Samsung S23', [$categories[6]], false)->getSku();
        $productSkus[] = $this->dataProvider->createProduct(1100, 'aaaa-5555', 'Samsung S24', [$categories[6]], false)->getSku();
        $productSkus[] = $this->dataProvider->createProduct(1100, 'aaaa-6666', 'Charger USB3 - UNIVERSAL', [$categories[7]], false)->getSku();
        $productSkus[] = $this->dataProvider->createProduct(1100, 'aaaa-7777', 'Monitor 31.5" ACER Nitro XV322Q', [$categories[8]], false)->getSku();

        $io->writeln("\n".'Products');
        // create 20k products
        $bar = $io->createProgressBar($count);

        for ($i = 0; $i < $count; ++$i) {
            $sku = Uuid::uuid4();
            $productSkus[] = $this->dataProvider->createProduct($this->randPrice(), $sku, 'test', [$categories[array_rand($categories)]], false)->getSku();
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
            /* @var UserGroup $userGroup */
            foreach ($productSkus as $productSku) {
                if (1 == $userGroup->getId() and 'aaaa-1111' == $productSku) {
                    // $this->dataProvider->createPriceList($userGroup, $productSku, 1400, false);
                }

                if (1 == $userGroup->getId()) {
                    continue;
                }

                $this->dataProvider->createPriceList($userGroup, $productSku, $this->randPrice(), false);

                $bar->advance();
            }
        }

        $this->entityManager->flush();

        $bar->finish();
    }

    private function prepareContractList(SymfonyStyle $io, $userIds, $productSkus): void
    {
        $count = count($userIds) * count($productSkus);

        $io->writeln("\n".'Contract Lists');
        $bar = $io->createProgressBar($count);

        $items = '';
        $i = 1;
        foreach ($userIds as $userId) {
            $user = $this->entityManager->getRepository(User::class)->find($userId);
            foreach ($productSkus as $productSku) {
                if (1 == $userId and 'aaaa-2222' == $productSku) {
                    $this->dataProvider->createContractList($user, $productSku, 1500, false);
                }

                if (1 == $userId) {
                    continue;
                }

                /* @var Product $product */

                $this->dataProvider->createContractList($user, $productSku, $this->randPrice(), false);
                $items .= $i++.','.$userId.','.$this->randPrice().','.$productSku."\n";

                $bar->advance();
            }
        }

        $this->entityManager->flush();
        // file_put_contents('var/mysql/product_contract_list.txt', $items);

        $bar->finish();
    }

    private function randPrice(): float
    {
        return (float) (rand(1, 1000).'.'.rand(1, 99));
    }
}
