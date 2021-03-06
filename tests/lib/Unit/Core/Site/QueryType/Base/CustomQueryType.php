<?php

namespace Netgen\EzPlatformSiteApi\Tests\Unit\Core\Site\QueryType\Base;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\DateMetadata;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\FullText;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\SectionId;
use eZ\Publish\API\Repository\Values\Content\Query\FacetBuilder\SectionFacetBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\SectionIdentifier;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause\SectionName;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Base;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\CriterionDefinition;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Test stub for custom QueryType.
 *
 * @see \Netgen\EzPlatformSiteApi\Core\Site\QueryType\Base
 */
class CustomQueryType extends Base
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'prefabrication_date',
        ]);

        $resolver->setAllowedTypes('prefabrication_date', ['int', 'string', 'array']);
    }

    protected function registerCriterionBuilders()
    {
        $this->registerCriterionBuilder(
            'prefabrication_date',
            function (CriterionDefinition $definition) {
                return new DateMetadata(
                    DateMetadata::MODIFIED,
                    $definition->operator,
                    $definition->value
                );
            }
        );
    }

    protected function getFilterCriteria(array $parameters)
    {
        return new SectionId(42);
    }

    protected function buildQuery()
    {
        return new LocationQuery();
    }

    public static function getName()
    {
        return 'Test:Custom';
    }

    protected function getQueryCriterion(array $parameters)
    {
        return new FullText('one AND two OR three');
    }

    protected function getFacetBuilders(array $parameters)
    {
        return [
            new SectionFacetBuilder(),
        ];
    }

    protected function parseCustomSortString($string)
    {
        switch ($string) {
            case 'section':
                return new SectionIdentifier();
            case 'whatever':
                return new SectionName();
        }

        return null;
    }
}
