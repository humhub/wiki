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

    public bool $wikiNumberingEnabledDefault = false;

    public bool $overviewNumberingEnabledDefault = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->contentHiddenDefault = $this->getModule()->contentHiddenGlobalDefault;
        $this->hideNavigationEntryDefault = $this->getModule()->hideNavigationEntryDefault;
        $this->wikiNumberingEnabledDefault = $this->getModule()->wikiNumberingEnabledDefault;
        $this->overviewNumberingEnabledDefault = $this->getModule()->overviewNumberingEnabledDefault;
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
            [['contentHiddenDefault', 'hideNavigationEntryDefault','wikiNumberingEnabledDefault','overviewNumberingEnabledDefault'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'hideNavigationEntryDefault' => Yii::t('WikiModule.base', 'Hide Navigation Entries of this module globally by default'),
            'wikiNumberingEnabledDefault'=> Yii::t('WikiModule.base', 'Enable wiki page header numbering for all users by default'),
            'overviewNumberingEnabledDefault'=> Yii::t('WikiModule.base', 'Enable overview numbering for all users by default'),
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->getModule()->settings->set('contentHiddenGlobalDefault', $this->contentHiddenDefault);
        $this->getModule()->settings->set('hideNavigationEntryDefault', $this->hideNavigationEntryDefault);
        $this->getModule()->settings->set('wikiNumberingEnabledDefault', $this->wikiNumberingEnabledDefault);
        $this->getModule()->settings->set('overviewNumberingEnabledDefault', $this->overviewNumberingEnabledDefault);

        return true;
    }
}
