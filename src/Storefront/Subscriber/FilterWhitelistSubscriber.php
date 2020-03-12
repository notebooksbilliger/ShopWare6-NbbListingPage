<?php

declare(strict_types=1);

namespace NbbListingPage\Storefront\Subscriber;

use Shopware\Core\Content\Category\CategoryEntity;
use Shopware\Core\Content\Product\Events\ProductListingCriteriaEvent;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FilterWhitelistSubscriber implements EventSubscriberInterface
{
    public const CUSTOM_FILTERS_GROUP_IDS = 'custom_filters_group_ids';

    /**
     * @var EntityRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @param EntityRepositoryInterface $categoryRepository
     */
    public function __construct(EntityRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            ProductListingCriteriaEvent::class => [
                ['whitelistFilters', 200],
            ]
        ];
    }

    public function whitelistFilters(ProductListingCriteriaEvent $event)
    {
        $categoryId = $event->getRequest()->get('navigationId');
        $result = $this->categoryRepository->search(new Criteria([$categoryId]), $event->getContext());

        /** @var CategoryEntity $category */
        $category = $result->getEntities()->first();
        if (isset($category->getCustomFields()[self::CUSTOM_FILTERS_GROUP_IDS])) {
            $criteria = $event->getCriteria();
            $criteria->addFilter(
                new EqualsAnyFilter(
                    'product.properties.group.id',
                    $category->getCustomFields()[self::CUSTOM_FILTERS_GROUP_IDS]
                )
            );
        }
    }
}
