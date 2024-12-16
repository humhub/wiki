<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\wiki\models;

use humhub\modules\wiki\Module;
use Yii;
use yii\base\Model;

class ConfigForm extends Model
{
    public bool $contentHiddenDefault = false;

    public bool $hideNavigationEntryDefault = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->contentHiddenDefault = $this->getModule()->contentHiddenGlobalDefault;
        $this->hideNavigationEntryDefault = $this->getModule()->hideNavigationEntryDefault;
    }

    public function getModule(): Module
    {
        return Yii::$app->getModule('wiki');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contentHiddenDefault', 'hideNavigationEntryDefault'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'hideNavigationEntryDefault' => Yii::t('WikiModule.base', 'Hide Navigation Entries of this module globally by default'),
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->getModule()->settings->set('contentHiddenGlobalDefault', $this->contentHiddenDefault);
        $this->getModule()->settings->set('hideNavigationEntryDefault', $this->hideNavigationEntryDefault);

        return true;
    }
}
