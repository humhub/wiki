<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\widgets\WikiListHeader;
use humhub\modules\wiki\widgets\WikiListTable;
use yii\data\ActiveDataProvider;

/* @var $contentContainer ContentContainerActiveRecord */
/* @var $dataProvider ActiveDataProvider */

Assets::register($this);
?>
<div class="panel panel-default wiki-page-list-table">
    <div class="panel-body">
        <?= WikiListHeader::widget([
            'title' => Yii::t('WikiModule.base', 'Last Edited'),
            'contentContainer' => $contentContainer,
        ]) ?>

        <?= WikiListTable::widget(['dataProvider' => $dataProvider]) ?>
    </div>
</div>