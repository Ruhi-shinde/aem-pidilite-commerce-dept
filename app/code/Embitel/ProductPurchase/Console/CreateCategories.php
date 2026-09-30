<?php
/**
 * Copyright © ANE, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Embitel\ProductPurchase\Console;

use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCategories extends Command
{
    /**
     * Create sample category construct
     *
     * @param CategoryFactory $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     * @param StoreManagerInterface $storeManager
     * @param State $state
     */
    public function __construct(
        private readonly CategoryFactory $categoryFactory,
        private readonly CategoryRepositoryInterface $categoryRepository,
        private readonly StoreManagerInterface $storeManager,
        private readonly State $state
    ) {
        parent::__construct();
    }

    /**
     * Create sample category command configuration
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('embitel:category:create')
            ->setDescription('Create Sample Category Tree');

        parent::configure();
    }

    /**
     * Create sample category function
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode('adminhtml');
        } catch (\Exception $e) {
            throw new LocalizedException(
                __('Unable to set area code: %1', $e->getMessage())
            );
        }

        $rootCategoryId = $this->storeManager
            ->getStore()
            ->getRootCategoryId();

        $parentCat1 = $this->createCategory(
            $rootCategoryId,
            'Parent Category 1'
        );
        $parentCat2 = $this->createCategory(
            $rootCategoryId,
            'Parent Category 2'
        );
        $parentCat3 = $this->createCategory(
            $rootCategoryId,
            'Parent Category 3'
        );

        $interior = $this->createCategory(
            $parentCat1->getId(),
            'Interior Paint'
        );
        $exterior = $this->createCategory(
            $parentCat1->getId(),
            'Exterior Paint'
        );

        $this->createCategory(
            $interior->getId(),
            'Luxury Interior'
        );
        $this->createCategory(
            $interior->getId(),
            'Premium Interior'
        );

        $this->createCategory(
            $exterior->getId(),
            'Weather Coat'
        );
        $this->createCategory(
            $exterior->getId(),
            'Wall Shield'
        );

        $westernDress = $this->createCategory(
            $parentCat2->getId(),
            'Western Dress'
        );
        $casualDress = $this->createCategory(
            $parentCat2->getId(),
            'Casual Dress'
        );

        $this->createCategory(
            $westernDress->getId(),
            'Luxury Dress'
        );
        $this->createCategory(
            $westernDress->getId(),
            'Premium Dress'
        );

        $this->createCategory(
            $casualDress->getId(),
            'Office Dress'
        );
        $this->createCategory(
            $casualDress->getId(),
            'Seminar Dress'
        );

        $output->writeln('<info>Categories created successfully.</info>');

        return Command::SUCCESS;
    }

    /**
     * Create category function
     *
     * @param int $parentId
     * @param string $name
     * @return \Magento\Catalog\Model\Category
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function createCategory(
        int $parentId,
        string $name
    ) {
        $category = $this->categoryFactory->create();

        $category->setName($name);
        $category->setIsActive(true);
        $category->setParentId($parentId);
        $category->setIncludeInMenu(true);

        $this->categoryRepository->save($category);

        return $category;
    }
}
