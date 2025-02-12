<?php

use humhub\libs\Html;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\ui\form\widgets\ContentHiddenCheckbox;
use humhub\modules\ui\form\widgets\ContentVisibilitySelect;
use humhub\modules\ui\view\components\View;
use humhub\modules\wiki\assets\Assets;
use humhub\modules\wiki\helpers\Url;
use humhub\modules\wiki\models\forms\PageEditForm;
use humhub\modules\wiki\widgets\WikiEditor;
use humhub\modules\ui\form\widgets\ActiveForm;
use humhub\modules\wiki\widgets\WikiLinkModal;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\modules\wiki\widgets\WikiPagePicker;
use humhub\modules\wiki\widgets\WikiPath;
use humhub\widgets\Button;
use humhub\modules\topic\widgets\TopicPicker;

/* @var $this View */
/* @var $model PageEditForm */
/* @var $contentContainer ContentContainerActiveRecord */
/* @var $requireConfirmation bool */
/* @var $displayFieldCategory bool */
/* @var $diffUrl string */
/* @var $discardChangesUrl string */

Assets::register($this);
?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row<?= $model->page->isCategory ? ' wiki-category-page-edit' : '' ?>">

            <?php WikiContent::begin([
                'id' => 'wiki-page-edit',
                'cssClass' => 'wiki-page-content',
            ]) ?>

            <div class="wiki-headline">
                <div class="wiki-headline-top">
                    <?= WikiPath::widget(['page' => $model->page]) ?>
                    <?php if (!$requireConfirmation) : ?>
                        <div>
                            <?= Button::defaultType(Yii::t('WikiModule.base', 'Cancel'))
                                ->link($model->page->isNewRecord ? Url::toOverview($model->container) : Url::toWiki($model->page)) ?>
                            <?= Button::save()->action('wiki.Form.submit') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="wiki-page-title"><?= $model->getTitle() ?></div>
            </div>

            <?php $form = ActiveForm::begin([
                'enableClientValidation' => false,
                'options' => [
                    'data-ui-widget' => 'wiki.Form',
                    'data-change-category-confirm' => Yii::t('WikiModule.base', 'Are you really sure? All existing category page assignments will be removed!'),
                    'data-is-category' => $model->page->isCategory,
                    'data-ui-init' => '1',
                ],
                'acknowledge' => true,
            ]); ?>

            <?= $form->field($model, 'latestRevisionNumber')->hiddenInput()->label(false); ?>
            <?php if ($requireConfirmation) : ?>
                <div class="alert alert-danger">
                    <?= Yii::t(
                        'WikiModule.base',
                        '<strong>Warning!</strong><br><br>Another user has updated this page since you have started editing it. Please confirm that you want to overwrite those changes.<br>:linkToCompare',
                        [':linkToCompare' => Button::asLink('<i class="fa fa-arrow-right"></i>&nbsp;' . Yii::t('WikiModule.base', 'Compare changes'))->action('compareOverwriting', $diffUrl)->cssClass('colorDanger')],
                    ); ?>
                </div>
                <?= $form->field($model, 'backOverwriting')->hiddenInput()->label(false); ?>
                <?= $form->field($model, 'confirmOverwriting')->checkbox()->label(); ?>

                <?= Button::save(Yii::t('WikiModule.base', 'Overwrite'))->submit() ?>

                <?= Button::defaultType(Yii::t('WikiModule.base', 'Back'))->action('backOverwriting')->icon('back')->loader(false); ?>

                <div class="pull-right">
                    <?= Button::danger(Yii::t('WikiModule.base', 'Discard my changes'))->link($discardChangesUrl)->icon('close')->loader(true); ?>
                </div>
            <?php else : ?>
                <?= $form->field($model, 'confirmOverwriting')->hiddenInput()->label(false); ?>
            <?php endif; ?>

            <div<?php if ($requireConfirmation) : ?> style="display:none"<?php endif; ?>>

                <?= $form->field($model->page, 'title')
                    ->textInput([
                        'placeholder' => Yii::t('WikiModule.base', 'New page title'),
                        'disabled' => $model->isDisabledField('title'),
                    ])->label(false); ?>

                <?= $form->field($model->revision, 'content')->widget(WikiEditor::class)->label(false) ?>

                <?php $category = $form->field($model->page, 'parent_page_id') ?>
                <?= $displayFieldCategory
                    ? $category->widget(WikiPagePicker::class, [
                        'model' => $model->page,
                        'maxInput' => 30,
                        'disabled' => $model->isDisabledField('parent_page_id'),
                    ])
                    : $category->hiddenInput() ?>

                <?= $form->beginCollapsibleFields(Yii::t('WikiModule.base', 'Advanced settings')); ?>

                <?php if (!$model->canAdminister()) : ?>
                    <div class="alert alert-info">
                        <?= Yii::t(
                            'WikiModule.base',
                            'In order to edit all fields, you need the permission to administer wiki pages.',
                        ); ?>
                    </div>
                <?php endif; ?>

                <?= $form->field($model->page, 'is_home')->checkbox([
                    'title' => Yii::t('WikiModule.base', 'Overwrite the wiki index start page?'),
                    'disabled' => $model->isDisabledField('is_home'),
                ]) ?>

                <?= $form->field($model, 'isPublic')->widget(ContentVisibilitySelect::class, [
                    'readonly' => $model->isDisabledField('isPublic'),
                ]) ?>

                <?= $form->field($model->page, 'admin_only')->checkbox([
                    'title' => Yii::t('WikiModule.base', 'Disable edit access for non wiki administrators?'),
                    'disabled' => $model->isDisabledField('admin_only'),
                ]) ?>

                <?= $form->field($model->page, 'is_container_menu')->checkbox([
                    'disabled' => $model->isDisabledField('is_container_menu'),
                ]) ?>
                <div id="container_menu_order_field"<?php if (!$model->page->is_container_menu) : ?> style="display: none"<?php endif; ?>>
                    <?= $form->field($model->page, 'container_menu_order')->textInput([
                        'disabled' => $model->isDisabledField('container_menu_order'),
                    ]) ?>
                </div>

                <?= $form->field($model, 'hidden')->widget(ContentHiddenCheckbox::class) ?>

                <?= $form->endCollapsibleFields(); ?>

                <?= $form->field($model, 'topics')->widget(TopicPicker::class, ['options' => ['disabled' => $model->isDisabledField('topics')]])->label(false) ?>

                <hr>

                <?= Button::defaultType(Yii::t('WikiModule.base', 'Cancel'))
                    ->link($model->page->isNewRecord ? Url::toOverview($model->container) : Url::toWiki($model->page)) ?>
                <?= Button::save()->submit() ?>
            </div>

            <?php ActiveForm::end(); ?>

            <?php WikiContent::end() ?>

        </div>
    </div>
</div>

<?= WikiLinkModal::widget(['contentContainer' => $contentContainer]) ?>

<script <?= Html::nonce() ?>>
    $('input[name="WikiPage[is_container_menu]"]').click(function () {
        $('#container_menu_order_field').toggle($(this).prop('checked'));
    })
</script>
