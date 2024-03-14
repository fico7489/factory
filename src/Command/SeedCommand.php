<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand(name: 'app:seed')]
class SeedCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly KernelInterface $kernel,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $application->run(new ArrayInput(['command' => 'doctrine:schema:drop', '--force' => true, '--full-database' => 'true']));
        $application->run(new ArrayInput(['command' => 'doctrine:migrations:migrate', '-n' => true]));

        $this->prepareCategories();
        $this->prepareProducts();

        /*for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('Name_' . Uuid::uuid4());
            $product->setPrice((float)(random_int(1, 1000).'.'.random_int(1,99)));
            $product->setPublished(random_int(0,1));
            $product->setDescription(Uuid::uuid4());
            $product->setSku(Uuid::uuid4());

            $this->entityManager->persist($product);
        }

        $this->entityManager->flush();*/


        return Command::SUCCESS;
    }

    private function prepareCategories():void
    {
        $categoryPC = new Category();
        $categoryPC->setName('PC');
        $this->entityManager->persist($categoryPC);

        $categoryLaptop = new Category();
        $categoryLaptop->setName('Laptop');
        $categoryLaptop->setParent($categoryPC);
        $this->entityManager->persist($categoryLaptop);

        $categoryGaming = new Category();
        $categoryGaming->setName('Gaming');
        $categoryGaming->setParent($categoryLaptop);
        $this->entityManager->persist($categoryGaming);

        $categoryForWork = new Category();
        $categoryForWork->setName('For Work');
        $categoryForWork->setParent($categoryLaptop);
        $this->entityManager->persist($categoryForWork);

        $categoryDesktop = new Category();
        $categoryDesktop->setName('Desktop');
        $categoryDesktop->setParent($categoryPC);
        $this->entityManager->persist($categoryDesktop);

        $categoryCellPhone= new Category();
        $categoryCellPhone->setName('Cell Phone');
        $this->entityManager->persist($categoryCellPhone);

        $categorySmartphone= new Category();
        $categorySmartphone->setName('Smartphone');
        $categorySmartphone->setParent($categoryCellPhone);
        $this->entityManager->persist($categorySmartphone);

        $categoryCharger = new Category();
        $categoryCharger->setName('Charger');
        $categoryCharger->setParent($categoryCellPhone);
        $this->entityManager->persist($categoryCharger);

        $categoryMonitor = new Category();
        $categoryMonitor->setName('Monitor');
        $this->entityManager->persist($categoryMonitor);

        $this->entityManager->flush();
    }

    private function prepareProducts():void{
        $categoryGaming = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Gaming']);
        $categoryForWork = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'For Work']);
        $categorySmartphone = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Smartphone']);

        $product = new Product();
        $product->setName('LENOVO ThinkPad T14s');
        $product->setSku('aaaa-1111');
        $product->setDescription('Test...');
        $product->setPrice(1500);
        $product->setPublished(true);
        $product->setCategories(new ArrayCollection([$categoryGaming, $categoryForWork]));
        $this->entityManager->persist($product);

        $product = new Product();
        $product->setName('ACER Aspire 3 NX.ADDEX');
        $product->setSku('aaaa-2222');
        $product->setDescription('Test2...');
        $product->setPrice(1600);
        $product->setPublished(true);
        $product->setCategories(new ArrayCollection([$categoryForWork]));
        $this->entityManager->persist($product);

        $product = new Product();
        $product->setName('Laptop ACER Nitro 5 NH.QH1EX.00V');
        $product->setSku('aaaa-3333');
        $product->setDescription('Test3...');
        $product->setPrice(1700);
        $product->setPublished(true);
        $product->setCategories(new ArrayCollection([$categoryGaming]));
        $this->entityManager->persist($product);

        $product = new Product();
        $product->setName('Samsung S23');
        $product->setSku('aaaa-4444');
        $product->setDescription('Test4...');
        $product->setPrice(1000);
        $product->setPublished(true);
        $product->setCategories(new ArrayCollection([$categorySmartphone]));
        $this->entityManager->persist($product);

        $product = new Product();
        $product->setName('Samsung S24');
        $product->setSku('aaaa-5555');
        $product->setDescription('Test5...');
        $product->setPrice(1100);
        $product->setPublished(true);
        $product->setCategories(new ArrayCollection([$categorySmartphone]));
        $this->entityManager->persist($product);

        $this->entityManager->flush();
    }
}
