<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2017 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by FukycraM (marc.fun)
 * Date: 18.12.2019
 * Time: 13:00
 */

namespace humhub\modules\wiki\models;

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerSettingsManager;
use humhub\modules\wiki\Module;
use Yii;
use yii\base\Model;

class DefaultSettings extends Model
{
    public const SETTING_MODULE_LABEL = 'defaults.moduleLabel';
    public const SETTING_CONTENT_HIDDEN_DEFAULT = 'contentHiddenDefault';
    public const SETTING_HIDE_NAVIGATION_ENTRY = 'hideNavigationEntry';

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var string
     */
    public $module_label;

    /**
     * @var bool
     */
    public bool $contentHiddenDefault = false;

    public bool $hideNavigationEntry = false;

    /**
     * @var Module
     */
    public $module;

    public function init()
    {
        $this->module = Yii::$app->getModule('wiki');

        $this->module_label = $this->getSettings()->get(
            self::SETTING_MODULE_LABEL,
            Yii::t('WikiModule.base', 'Wiki'),
        );

        $this->contentHiddenDefault = $this->getSettings()->get(
            self::SETTING_CONTENT_HIDDEN_DEFAULT,
            $this->module->contentHiddenGlobalDefault,
        );

        $this->hideNavigationEntry = $this->getSettings()->get(
            self::SETTING_HIDE_NAVIGATION_ENTRY,
            $this->module->hideNavigationEntryDefault,
        );

    }

    private function getSettings(): ContentContainerSettingsManager
    {
        return $this->module->settings->contentContainer($this->contentContainer);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['module_label'], 'string'],
            [['contentHiddenDefault', 'hideNavigationEntry'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'module_label' => Yii::t('WikiModule.base', 'Module name'),
            'hideNavigationEntry' => Yii::t('WikiModule.base', 'Hide Navigation Entry'),
        ];
    }
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->getSettings()->set(self::SETTING_MODULE_LABEL, $this->module_label);
        $this->getSettings()->set(self::SETTING_CONTENT_HIDDEN_DEFAULT, $this->contentHiddenDefault);
        $this->getSettings()->set(self::SETTING_HIDE_NAVIGATION_ENTRY, $this->hideNavigationEntry);

        return true;
    }

    public function getSubmitUrl()
    {
        return $this->contentContainer->createUrl('/wiki/container-config');
    }
}
