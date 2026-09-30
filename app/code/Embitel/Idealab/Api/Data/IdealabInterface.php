<?php
declare(strict_types=1);

namespace Embitel\Idealab\Api\Data;

interface IdealabInterface
{
    public const IDEALAB_ID = 'idealab_id';
    public const USER_ID = 'user_id';
    public const STUDENT_FIRST_NAME = 'student_first_name';
    public const STUDENT_LAST_NAME = 'student_last_name';
    public const STUDENT_DATE_OF_BIRTH = 'student_date_of_birth';
    public const STUDENT_SCHOOL_NAME = 'student_school_name';
    public const GENDER = 'gender';
    public const GRADE_GROUP = 'grade_group';
    public const PARENT_EMAIL = 'parent_email';
    public const PARENT_PHONE = 'parent_phone';
    public const PROJECT_TITLE = 'project_title';
    public const VIDEO_URL = 'video_url';
    public const CRAFT_IMAGE = 'craft_image';
    public const PROJECT_DESCRIPTION = 'project_description';
    public const I_ACCEPT = 'i_accept';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    public function getIdealabId();

    public function setIdealabId($idealabId);

    public function getUserId();

    public function setUserId($userId);

    public function getStudentFirstName();

    public function setStudentFirstName($studentFirstName);

    public function getStudentLastName();

    public function setStudentLastName($studentLastName);

    public function getStudentDateOfBirth();

    public function setStudentDateOfBirth($studentDateOfBirth);

    public function getStudentSchoolName();

    public function setStudentSchoolName($studentSchoolName);

    public function getGender();

    public function setGender($gender);

    public function getGradeGroup();

    public function setGradeGroup($gradeGroup);

    public function getParentEmail();

    public function setParentEmail($parentEmail);

    public function getParentPhone();

    public function setParentPhone($parentPhone);

    public function getProjectTitle();

    public function setProjectTitle($projectTitle);

    public function getVideoUrl();

    public function setVideoUrl($videoUrl);

    public function getCraftImage();

    public function setCraftImage($craftImage);

    public function getProjectDescription();

    public function setProjectDescription($projectDescription);

    public function getIAccept();

    public function setIAccept($iAccept);

    public function getCreatedAt();

    public function setCreatedAt($createdAt);

    public function getUpdatedAt();

    public function setUpdatedAt($updatedAt);
}
