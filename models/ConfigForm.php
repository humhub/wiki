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

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->contentHiddenDefault = $this->getModule()->getContentHiddenGlobalDefault();
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
            [['contentHiddenDefault'], 'boolean'],
        ];
    }

    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }

        $this->getModule()->settings->set('contentHiddenGlobalDefault', $this->contentHiddenDefault);

        return true;
    }
}
