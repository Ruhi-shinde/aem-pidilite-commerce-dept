<?php
declare(strict_types=1);

namespace Embitel\CustomerSuggestion\Api\Data;

interface CustomerSuggestionInterface
{
    public const ENTITY_ID = 'entity_id';
    public const USER_ID = 'user_id';
    public const USER_NAME = 'user_name';
    public const SUGGESTION = 'suggestion';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public function getEntityId();

    public function setEntityId($entityId);

    public function getUserId();

    public function setUserId($userId);

    public function getUserName();

    public function setUserName($userName);

    public function getSuggestion();

    public function setSuggestion($suggestion);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);

    public function getUpdatedAt();

    public function setUpdatedAt($updatedAt);
}
