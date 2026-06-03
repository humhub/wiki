<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\permissions;

use humhub\modules\space\models\Space;
use humhub\modules\user\models\User;
use Yii;

/**
 * Edit page Permission
 */
class EditPages extends \humhub\libs\BasePermission
{
    /**
     * @inheritdoc
     */
    public $defaultAllowedGroups = [
        Space::USERGROUP_OWNER,
        Space::USERGROUP_ADMIN,
        Space::USERGROUP_MODERATOR,
        Space::USERGROUP_MEMBER,
        User::USERGROUP_SELF,
    ];

    /**
     * @inheritdoc
     */
    protected $fixedGroups = [
        Space::USERGROUP_USER,
        Space::USERGROUP_GUEST,
        User::USERGROUP_FRIEND,
        User::USERGROUP_GUEST,
        User::USERGROUP_USER,
    ];

    /**
     * @inheritdoc
     */
    public function getTitle()
    {
        return Yii::t('WikiModule.base', 'Edit pages');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('WikiModule.base', 'Allows the user to edit or revert pages');
    }

    /**
     * @inheritdoc
     */
    protected $moduleId = 'wiki';

}
