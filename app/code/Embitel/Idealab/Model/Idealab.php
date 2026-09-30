<?php
declare(strict_types=1);

namespace Embitel\Idealab\Model;

use Embitel\Idealab\Api\Data\IdealabInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Idealab extends AbstractModel implements IdealabInterface
{
    private const ALLOWED_GENDERS = ['Male', 'Female', 'Prefer Not to Say'];
    private const ALLOWED_GRADE_GROUPS = ['Grade 1–4', 'Grade 5–8'];

    protected function _construct()
    {
        $this->_init(\Embitel\Idealab\Model\ResourceModel\Idealab::class);
    }

    public function beforeSave()
    {
        $this->validateAndNormalize();
        return parent::beforeSave();
    }

    private function validateAndNormalize(): void
    {
        /*$requiredFields = [
            self::USER_ID,
            self::STUDENT_FIRST_NAME,
            self::STUDENT_LAST_NAME,
            self::STUDENT_DATE_OF_BIRTH,
            self::STUDENT_SCHOOL_NAME,
            self::GENDER,
            self::GRADE_GROUP,
            self::PARENT_EMAIL,
            self::PARENT_PHONE,
            self::PROJECT_TITLE,
            self::PROJECT_DESCRIPTION,
            self::I_ACCEPT
        ];

        foreach ($requiredFields as $field) {
            $value = $this->getData($field);
            if ($value === null || $value === '') {
                throw new LocalizedException(__('Field "%1" is required.', $field));
            }
        }

        if ((int)$this->getUserId() <= 0) {
            throw new LocalizedException(__('user_id must be a positive integer.'));
        }

        $studentDateOfBirth = (string)$this->getStudentDateOfBirth();
        $date = \DateTime::createFromFormat('Y-m-d', $studentDateOfBirth);
        if (!$date || $date->format('Y-m-d') !== $studentDateOfBirth) {
            throw new LocalizedException(__('student_date_of_birth must be in Y-m-d format.'));
        }

        $gender = (string)$this->getGender();
        if (!in_array($gender, self::ALLOWED_GENDERS, true)) {
            throw new LocalizedException(__('gender must be one of: Male, Female, Prefer Not to Say.'));
        }

        $gradeGroup = (string)$this->getGradeGroup();
        if (!in_array($gradeGroup, self::ALLOWED_GRADE_GROUPS, true)) {
            throw new LocalizedException(__('grade_group must be one of: Grade 1–4, Grade 5–8.'));
        }

        if (!filter_var((string)$this->getParentEmail(), FILTER_VALIDATE_EMAIL)) {
            throw new LocalizedException(__('parent_email is not a valid email address.'));
        }

        $projectDescription = (string)$this->getProjectDescription();
        if (mb_strlen($projectDescription) > 500) {
            throw new LocalizedException(__('project_description must be 500 characters or less.'));
        }*/

        $this->setData(self::I_ACCEPT, (int)(bool)$this->getData(self::I_ACCEPT));

        $craftImage = $this->getCraftImage();
        if ($craftImage !== null && $craftImage !== '') {
            if (is_array($craftImage)) {
                if (count($craftImage) > 3) {
                    throw new LocalizedException(__('craft_image can contain a maximum of 3 image paths.'));
                }
                $this->setCraftImage(json_encode(array_values($craftImage), JSON_UNESCAPED_SLASHES));
                return;
            }

            $decoded = json_decode((string)$craftImage, true);
            if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                throw new LocalizedException(__('craft_image must be a JSON array.'));
            }

            if (count($decoded) > 3) {
                throw new LocalizedException(__('craft_image can contain a maximum of 3 image paths.'));
            }
        }
    }

    public function getIdealabId()
    {
        return $this->getData(self::IDEALAB_ID);
    }

    public function setIdealabId($idealabId)
    {
        return $this->setData(self::IDEALAB_ID, $idealabId);
    }

    public function getUserId()
    {
        return $this->getData(self::USER_ID);
    }

    public function setUserId($userId)
    {
        return $this->setData(self::USER_ID, $userId);
    }

    public function getStudentFirstName()
    {
        return $this->getData(self::STUDENT_FIRST_NAME);
    }

    public function setStudentFirstName($studentFirstName)
    {
        return $this->setData(self::STUDENT_FIRST_NAME, $studentFirstName);
    }

    public function getStudentLastName()
    {
        return $this->getData(self::STUDENT_LAST_NAME);
    }

    public function setStudentLastName($studentLastName)
    {
        return $this->setData(self::STUDENT_LAST_NAME, $studentLastName);
    }

    public function getStudentDateOfBirth()
    {
        return $this->getData(self::STUDENT_DATE_OF_BIRTH);
    }

    public function setStudentDateOfBirth($studentDateOfBirth)
    {
        return $this->setData(self::STUDENT_DATE_OF_BIRTH, $studentDateOfBirth);
    }

    public function getStudentSchoolName()
    {
        return $this->getData(self::STUDENT_SCHOOL_NAME);
    }

    public function setStudentSchoolName($studentSchoolName)
    {
        return $this->setData(self::STUDENT_SCHOOL_NAME, $studentSchoolName);
    }

    public function getGender()
    {
        return $this->getData(self::GENDER);
    }

    public function setGender($gender)
    {
        return $this->setData(self::GENDER, $gender);
    }

    public function getGradeGroup()
    {
        return $this->getData(self::GRADE_GROUP);
    }

    public function setGradeGroup($gradeGroup)
    {
        return $this->setData(self::GRADE_GROUP, $gradeGroup);
    }

    public function getParentEmail()
    {
        return $this->getData(self::PARENT_EMAIL);
    }

    public function setParentEmail($parentEmail)
    {
        return $this->setData(self::PARENT_EMAIL, $parentEmail);
    }

    public function getParentPhone()
    {
        return $this->getData(self::PARENT_PHONE);
    }

    public function setParentPhone($parentPhone)
    {
        return $this->setData(self::PARENT_PHONE, $parentPhone);
    }

    public function getProjectTitle()
    {
        return $this->getData(self::PROJECT_TITLE);
    }

    public function setProjectTitle($projectTitle)
    {
        return $this->setData(self::PROJECT_TITLE, $projectTitle);
    }

    public function getVideoUrl()
    {
        return $this->getData(self::VIDEO_URL);
    }

    public function setVideoUrl($videoUrl)
    {
        return $this->setData(self::VIDEO_URL, $videoUrl);
    }

    public function getCraftImage()
    {
        return $this->getData(self::CRAFT_IMAGE);
    }

    public function setCraftImage($craftImage)
    {
        return $this->setData(self::CRAFT_IMAGE, $craftImage);
    }

    public function getProjectDescription()
    {
        return $this->getData(self::PROJECT_DESCRIPTION);
    }

    public function setProjectDescription($projectDescription)
    {
        return $this->setData(self::PROJECT_DESCRIPTION, $projectDescription);
    }

    public function getIAccept()
    {
        return $this->getData(self::I_ACCEPT);
    }

    public function setIAccept($iAccept)
    {
        return $this->setData(self::I_ACCEPT, $iAccept);
    }

    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
