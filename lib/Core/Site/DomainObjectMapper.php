<?php

namespace Netgen\EzPlatformSiteApi\Core\Site;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\Content\Content as APIContent;
use eZ\Publish\API\Repository\Values\Content\Field as APIField;
use eZ\Publish\API\Repository\Values\Content\Location as APILocation;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use Netgen\EzPlatformSiteApi\API\Values\Content as SiteContent;
use Netgen\EzPlatformSiteApi\API\Site as SiteInterface;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Content;
use Netgen\EzPlatformSiteApi\Core\Site\Values\ContentInfo;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Field;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Location;
use Netgen\EzPlatformSiteApi\Core\Site\Values\Node;
use Netgen\EzPlatformSiteApi\Core\Site\Values\TranslatableTrait;

/**
 * @internal
 *
 * Domain object mapper is an internal service that maps eZ Platform Repository objects
 * to the native domain objects
 */
final class DomainObjectMapper
{
    use TranslatableTrait;

    /**
     * @var \Netgen\EzPlatformSiteApi\API\Site
     */
    private $site;

    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    private $contentTypeService;

    /**
     * @var \eZ\Publish\API\Repository\ContentService
     */
    private $contentService;

    /**
     * @param \Netgen\EzPlatformSiteApi\API\Site $site
     * @param \eZ\Publish\API\Repository\ContentService $contentService
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\API\Repository\FieldTypeService $fieldTypeService
     */
    public function __construct(
        SiteInterface $site,
        ContentService $contentService,
        ContentTypeService $contentTypeService,
        FieldTypeService $fieldTypeService
    ) {
        $this->site = $site;
        $this->contentService = $contentService;
        $this->contentTypeService = $contentTypeService;
        $this->fieldTypeService = $fieldTypeService;
    }

    /**
     * Maps Repository Content to the Site Content.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\Content
     */
    public function mapContent(VersionInfo $versionInfo, $languageCode)
    {
        $contentInfo = $versionInfo->contentInfo;
        $contentType = $this->contentTypeService->loadContentType(
            $versionInfo->contentInfo->contentTypeId
        );

        return new Content(
            [
                'id' => $contentInfo->id,
                'contentTypeId' => $contentInfo->contentTypeId,
                'sectionId' => $contentInfo->sectionId,
                'currentVersionNo' => $contentInfo->currentVersionNo,
                'published' => $contentInfo->published,
                'ownerId' => $contentInfo->ownerId,
                'modificationDate' => $contentInfo->modificationDate,
                'publishedDate' => $contentInfo->publishedDate,
                'alwaysAvailable' => $contentInfo->alwaysAvailable,
                'remoteId' => $contentInfo->remoteId,
                'mainLanguageCode' => $contentInfo->mainLanguageCode,
                'mainLocationId' => $contentInfo->mainLocationId,
                'name' => $versionInfo->getName($languageCode),
                'languageCode' => $languageCode,
                'contentTypeIdentifier' => $contentType->identifier,
                'contentTypeName' => $this->getTranslatedString($languageCode, (array)$contentType->getNames()),
                'contentTypeDescription' => $this->getTranslatedString($languageCode, (array)$contentType->getDescriptions()),
                'contentInfo' => $this->mapContentInfo(
                    $versionInfo,
                    $languageCode,
                    $contentType
                ),
                'innerContentType' => $contentType,
                'innerVersionInfo' => $versionInfo,
                'site' => $this->site,
                'domainObjectMapper' => $this,
                'contentService' => $this->contentService,
                'fieldTypeService' => $this->fieldTypeService,
            ]
        );
    }

    /**
     * Maps Repository ContentInfo to the Site ContentInfo.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $languageCode
     * @param \eZ\Publish\API\Repository\Values\ContentType\ContentType|null $contentType
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\ContentInfo
     */
    public function mapContentInfo(
        VersionInfo $versionInfo,
        $languageCode,
        ContentType $contentType = null
    ) {
        $contentInfo = $versionInfo->contentInfo;

        if ($contentType === null) {
            $contentType = $this->contentTypeService->loadContentType($contentInfo->contentTypeId);
        }

        return new ContentInfo(
            [
                'name' => $versionInfo->getName($languageCode),
                'languageCode' => $languageCode,
                'contentTypeIdentifier' => $contentType->identifier,
                'contentTypeName' => $this->getTranslatedString($languageCode, (array)$contentType->getNames()),
                'contentTypeDescription' => $this->getTranslatedString($languageCode, (array)$contentType->getDescriptions()),
                'innerContentInfo' => $versionInfo->contentInfo,
                'innerContentType' => $contentType,
                'site' => $this->site,
            ]
        );
    }

    /**
     * Maps Repository Location to the Site Location.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\Content\VersionInfo $versionInfo
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSiteApi\API\Values\Location
     */
    public function mapLocation(APILocation $location, VersionInfo $versionInfo, $languageCode)
    {
        return new Location(
            [
                'contentInfo' => $this->mapContentInfo($versionInfo, $languageCode),
                'innerLocation' => $location,
                'site' => $this->site,
            ]
        );
    }

    /**
     * Maps Repository Content and Location to the Site Node.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string $languageCode
     *
     * @return \Netgen\EzPlatformSiteApi\Core\Site\Values\Node
     */
    public function mapNode(APILocation $location, APIContent $content, $languageCode)
    {
        return new Node(
            [
                'contentInfo' => $this->mapContentInfo($content->versionInfo, $languageCode),
                'innerLocation' => $location,
                'content' => $this->mapContent($content->versionInfo, $languageCode),
                'site' => $this->site,
            ]
        );
    }

    public function mapField(APIField $apiField, SiteContent $content)
    {
        $contentType = $content->innerContentType;
        $fieldDefinition = $contentType->getFieldDefinition($apiField->fieldDefIdentifier);
        $fieldTypeIdentifier = $fieldDefinition->fieldTypeIdentifier;
        $isEmpty = $this->fieldTypeService->getFieldType($fieldTypeIdentifier)->isEmptyValue(
            $apiField->value
        );

        return new Field([
            'isEmpty' => $isEmpty,
            'innerField' => $apiField,
            'innerFieldDefinition' => $fieldDefinition,
            'content' => $content,
            'name' => $this->getTranslatedString(
                $content->languageCode,
                (array)$fieldDefinition->getNames()
            ),
            'description' => $this->getTranslatedString(
                $content->languageCode,
                (array)$fieldDefinition->getDescriptions()
            ),
            'fieldTypeIdentifier' => $fieldTypeIdentifier,
        ]);
    }
}
