<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\permissions;

use humhub\modules\space\models\Space;

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
    ];

    /**
     * @inheritdoc
     */
    protected $title = 'View History';

    /**
     * @inheritdoc
     */
    protected $description = 'Allows the user to view the history of wiki pages';

    /**
     * @inheritdoc
     */
    protected $moduleId = 'wiki';
}
