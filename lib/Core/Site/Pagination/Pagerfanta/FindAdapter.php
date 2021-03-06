<?php

namespace Netgen\EzPlatformSiteApi\Core\Site\Pagination\Pagerfanta;

use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use Netgen\EzPlatformSiteApi\API\FindService;

/**
 * Pagerfanta adapter performing search using FindService.
 *
 * @see \Netgen\EzPlatformSiteApi\API\FindService
 */
final class FindAdapter extends BaseAdapter
{
    /**
     * @var \Netgen\EzPlatformSiteApi\API\FindService
     */
    private $findService;

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Query $query
     * @param \Netgen\EzPlatformSiteApi\API\FindService $findService
     */
    public function __construct(Query $query, FindService $findService)
    {
        parent::__construct($query);

        $this->findService = $findService;
    }

    protected function executeQuery(Query $query)
    {
        if ($query instanceof LocationQuery) {
            return $this->findService->findLocations($query);
        }

        return $this->findService->findContent($query);
    }
}
