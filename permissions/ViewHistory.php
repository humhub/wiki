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
class ViewHistory extends \humhub\libs\BasePermission
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
    public function getTitle()
    {
        return Yii::t('WikiModule.base', 'View History');
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return Yii::t('WikiModule.base', 'Allows the user to view the history of wiki pages');
    }

    /**
     * @inheritdoc
     */
    protected $moduleId = 'wiki';

}
