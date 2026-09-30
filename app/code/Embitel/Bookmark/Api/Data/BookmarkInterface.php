<?php
declare(strict_types=1);

namespace Embitel\Bookmark\Api\Data;

interface BookmarkInterface
{
    public const ENTITY_ID = 'entity_id';
    public const USER_ID = 'user_id';
    public const BOOKMARK_ID = 'bookmark_id';
    public const CHILD_ID = 'child_id';
    public const CONTENT_TYPE = 'content_type';
    public const CONTENT_NAME = 'content_name';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public function getEntityId();

    public function setEntityId($entityId);

    public function getUserId();

    public function setUserId($userId);

    public function getBookmarkId();

    public function setBookmarkId($bookmarkId);

    public function getChildId();

    public function setChildId($childId);

    public function getContentType();

    public function setContentType($contentType);

    public function getContentName();

    public function setContentName($contentName);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);

    public function getUpdatedAt();

    public function setUpdatedAt($updatedAt);
}
