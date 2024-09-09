<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2022 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

use humhub\libs\Html;
use humhub\modules\content\search\ResultSet;
use humhub\widgets\LinkPager;
use yii\web\View;

/* @var View $this */
/* @var array $options */
/* @var ResultSet $resultSet */
?>
<?= Html::beginTag('div', $options) ?>

<table class="table table-hover">
    <tbody>
    <?php if ($resultSet instanceof ResultSet && !empty($resultSet->results)) : ?>
        <?php foreach ($resultSet->results as $content) : ?>
            <tr>
                <td><?= $this->render('wikiListTableRow', ['wikiPage' => $content->getPolymorphicRelation()]) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr>
            <td><?= Yii::t('WikiModule.base', 'No wiki pages found.') ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<?php if ($resultSet instanceof ResultSet) : ?>
    <div class="pagination-container">
        <?= LinkPager::widget(['pagination' => $resultSet->pagination]) ?>
    </div>
<?php endif; ?>

<?= Html::endTag('div') ?>
