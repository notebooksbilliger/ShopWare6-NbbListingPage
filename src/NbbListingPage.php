<?php declare(strict_types=1);

namespace NbbListingPage;

use NbbListingPage\Storefront\Subscriber\FilterWhitelistSubscriber;
use Shopware\Core\Content\Property\PropertyGroupEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class NbbListingPage extends Plugin
{
    private const CUSTOM_FIELD_SET_ID = '2334f159ce6445d8b58ab8a83b904cf8';

    public function install(InstallContext $installContext): void
    {
        /** @var EntityRepositoryInterface $customFieldSetRepository */
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $options = $this->generateOptions($installContext->getContext());

        $customFieldSetRepository->upsert(
            [
                [
                    'id'           => self::CUSTOM_FIELD_SET_ID,
                    'name'         => 'nbb_custom_filters',
                    'config'       => ['label' => ['en-GB' => 'Filter configuration']],
                    'relations'    => [
                        ['entityName' => 'category']
                    ],
                    'customFields' => [
                        [
                            'name'   => FilterWhitelistSubscriber::CUSTOM_FILTERS_GROUP_IDS,
                            'type'   => CustomFieldTypes::SELECT,
                            'config' => [
                                'label'               => [
                                    'en-GB' => 'Available filters'
                                ],
                                'options'             => $options,
                                'helpText'            => [
                                    'en-GB' => null
                                ],
                                'placeholder'         => [
                                    'en-GB' => null
                                ],
                                'componentName'       => 'sw-multi-select',
                                'customFieldType'     => 'select',
                                'customFieldPosition' => 1
                            ]
                        ],
                    ]
                ]
            ],
            $installContext->getContext()
        );
    }

    private function generateOptions(Context $context): array
    {
        $options = [];

        /** @var EntityRepositoryInterface $propertyGroupRepo */
        $propertyGroupRepo = $this->container->get('property_group.repository');
        $result = $propertyGroupRepo->search(new Criteria(), $context);

        /** @var PropertyGroupEntity $propertyGroup */
        foreach ($result as $propertyGroup) {
            $options[] = [
                'label' => ['en-GB' => $propertyGroup->getName()],
                'value' => $propertyGroup->getId()
            ];
        }

        return $options;
    }
}
