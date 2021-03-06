<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\QueryType;

use eZ\Publish\API\Repository\Values\Content\Query;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Base implementation for Content search QueryTypes.
 */
abstract class Content extends Base
{
    final protected function configureBaseOptions(OptionsResolver $resolver)
    {
        parent::configureBaseOptions($resolver);
    }

    protected function buildQuery()
    {
        return new Query();
    }
}
