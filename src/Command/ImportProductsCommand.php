<?php
// src/Command/ImportProductsCommand.php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Service\ProductImportService;

#[AsCommand(
    name: 'import-products',
    description: 'Add a short description for your command',
)]




class ImportProductsCommand extends Command
{
    private ProductImportService $productImportService;



    public function __construct(ProductImportService $productImportService)
    {
        $this->productImportService = $productImportService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:import-products')
            ->setDescription('Import products from API');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Importing products...');
        $this->productImportService->importProductsFromUrl('https://tech.dev.ats-digital.com/api/products?size=5000');
        $output->writeln('Products imported successfully.');

        return Command::SUCCESS;
    }

}
