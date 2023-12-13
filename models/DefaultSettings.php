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
    const SETTING_MODULE_LABEL = 'defaults.moduleLabel';
    const SETTING_CONTENT_HIDDEN_DEFAULT = 'contentHiddenDefault';

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

    /**
     * @var Module
     */
    public $module;

    public function init()
    {
        $this->module = Yii::$app->getModule('wiki');

        $this->module_label = $this->getSettings()->get(
            self::SETTING_MODULE_LABEL,
            Yii::t('WikiModule.base', 'Wiki')
        );

        $this->contentHiddenDefault = $this->getSettings()->get(
            self::SETTING_CONTENT_HIDDEN_DEFAULT,
            $this->module->getContentHiddenGlobalDefault()
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
            [['contentHiddenDefault'], 'boolean'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'module_label' => Yii::t('WikiModule.base', 'Module name'),
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->getSettings()->set(self::SETTING_MODULE_LABEL, $this->module_label);
        $this->getSettings()->set(self::SETTING_CONTENT_HIDDEN_DEFAULT, $this->contentHiddenDefault);

        return true;
    }

    public function getSubmitUrl()
    {
        return $this->contentContainer->createUrl('/wiki/container-config');
    }
}
