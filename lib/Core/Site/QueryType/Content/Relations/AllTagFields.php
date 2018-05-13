<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content\Relations;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\MatchNone;
use Netgen\EzPlatformSiteApi\API\Values\Content as SiteContent;
use Netgen\EzPlatformSiteApi\Core\Site\QueryType\Content;
use Netgen\TagsBundle\API\Repository\Values\Content\Query\Criterion\TagId;
use Netgen\TagsBundle\API\Repository\Values\Tags\Tag as TagValue;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * QueryType for finding all Tag relations in a given Content.
 */
final class AllTagFields extends Content
{
    public static function getName()
    {
        return 'SiteAPI:Content/Relations/AllTagFields';
    }

    /**
     * @inheritdoc
     *
     * @throws \Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException
     * @throws \Symfony\Component\OptionsResolver\Exception\AccessException
     */
    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('content');
        $resolver->setAllowedTypes('content', SiteContent::class);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    protected function getFilterCriteria(array $parameters)
    {
        /** @var \Netgen\EzPlatformSiteApi\API\Values\Content $content */
        $content = $parameters['content'];
        $tagIds = $this->extractTagIds($content);

        if (empty($tagIds)) {
            return new MatchNone();
        }

        return new TagId($tagIds);
    }

    protected function getQueryCriteria(array $parameters)
    {
        return null;
    }

    protected function getFacetBuilders(array $parameters)
    {
        return [];
    }

    /**
     * Extract all Tag IDs from the given $content.
     *
     * @param \Netgen\EzPlatformSiteApi\API\Values\Content $content
     *
     * @return int[]|string[]
     */
    private function extractTagIds(SiteContent $content)
    {
        $tagsIdsGrouped = [[]];

        foreach ($content->fields as $field) {
            if ($field->fieldTypeIdentifier !== 'eztags') {
                continue;
            }

            /** @var \Netgen\TagsBundle\Core\FieldType\Tags\Value $value */
            $value = $field->value;
            $tagsIdsGrouped[] = array_map(function (TagValue $tag) {return $tag->id;}, $value->tags);
        }

        return array_merge(...$tagsIdsGrouped);
    }

    protected function registerCriterionBuilders()
    {
        // do nothing
    }
}
