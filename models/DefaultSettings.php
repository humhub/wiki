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

use humhub\components\SettingsManager;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\content\components\ContentContainerSettingsManager;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

class DefaultSettings extends Model
{
    const SETTING_MODULE_LABEL = 'defaults.moduleLabel';

    /**
     * @var ContentContainerActiveRecord
     */
    public $contentContainer;

    /**
     * @var string
     */
    public $module_label;

    public $module;


    public function init()
    {
        $this->module = Yii::$app->getModule('wiki');
        $this->module_label = $this->getSettings()->get(
            self::SETTING_MODULE_LABEL,
            Yii::t('WikiModule.base', 'Wiki')
        );
    }

    /**
     * @return SettingsManager
     */
    private function getSettings()
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
        ];
    }

    public function attributeLabels()
    {
        return [
            'module_label' => Yii::t('WikiModule.config', 'Module name'),
        ];
    }

    public function save()
    {
        $this->getSettings()->set(
            self::SETTING_MODULE_LABEL,
            $this->module_label
        );
        return true;
    }

    public function getSubmitUrl()
    {
        return $this->contentContainer->createUrl('/wiki/container-config');
    }
}
