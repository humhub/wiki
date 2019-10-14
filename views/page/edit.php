<?php

use humhub\libs\Html;
use humhub\modules\wiki\widgets\WikiEditor;
use yii\bootstrap\ActiveForm;
use humhub\modules\wiki\widgets\WikiLinkModal;
use humhub\modules\wiki\widgets\WikiMenu;
use humhub\modules\wiki\widgets\WikiContent;
use humhub\widgets\Button;
use humhub\modules\topic\widgets\TopicPicker;

/* @var $this \humhub\components\View */
/* @var $model \humhub\modules\wiki\models\forms\PageEditForm */
/* @var $contentContainer \humhub\modules\content\components\ContentContainerActiveRecord */

humhub\modules\wiki\assets\Assets::register($this);

$canAdminister = $model->canAdminister();

?>
<div class="panel panel-default">
    <div class="panel-body">
        <div class="row <?= $model->page->is_category ? 'wiki-category-page-edit' : '' ?>">

            <?php WikiContent::begin(['title' => $model->getTitle(), 'id' => 'wiki-page-edit']) ?>

                <?php $form = ActiveForm::begin(['enableClientValidation' => false]); ?>

                    <?= $form->field($model->page, 'title')
                        ->textInput([
                            'placeholder' => Yii::t('WikiModule.views_page_edit', 'New page title'),
                            'disabled' => $model->isDisabledField('title')
                        ])->label(false); ?>

                    <?= $form->field($model->revision, 'content')->widget(WikiEditor::class)->label(false) ?>

                    <?= $form->field($model->page, 'is_home')->checkbox([
                             'title' => Yii::t('WikiModule.base', 'Overwrite the wiki index start page?'),
                            'disabled' => $model->isDisabledField('is_home')]); ?>
                    <?= $form->field($model->page, 'admin_only')->checkbox([
                            'title' => Yii::t('WikiModule.base', 'Disable edit access for non wiki administrators?'),
                            'disabled' => $model->isDisabledField('admin_only')]); ?>
                    <?= $form->field($model->page, 'is_category')->checkbox(['disabled' => $model->isDisabledField('is_category')]); ?>
                    <?= $form->field($model, 'isPublic')->checkbox([
                            'title' => Yii::t('WikiModule.base', 'Enable read access for non space members?'),
                            'disabled' => $model->isDisabledField('isPublic')]); ?>

                    <?php if(!$model->isDisabledField('is_category') || $model->page->parent_page_id) :?>
                        <?= $form->field($model->page, 'parent_page_id')
                            ->dropDownList($model->getCategoryList(), ['disabled' => $model->isDisabledField('parent_page_id')]); ?>
                    <?php endif; ?>




                    <?= $form->field($model, 'topics')->widget(TopicPicker::class, ['options' => ['disabled' =>  $model->isDisabledField('topics')]])->label(false) ?>

                    <?php if(!$canAdminister) : ?>
                        <div class="alert alert-warning">
                            <?= Yii::t('WikiModule.base',
                                'In order to edit all fields, you need the permission to administer wiki pages.'); ?>
                        </div>
                    <?php endif; ?>

                    <hr>

                    <?= Button::save()->submit() ?>

                <?php ActiveForm::end(); ?>

            <?php WikiContent::end() ?>

            <?= WikiMenu::widget(['page' => $model->page, 'edit' => true]) ?>

        </div>
    </div>
</div>

<?= WikiLinkModal::widget(['contentContainer' => $contentContainer]) ?>

<?= Html::beginTag('script')?>

    $(document).one('humhub:ready', function() {
        var $checkboxes = $('.regular-checkbox-container');
        $checkboxes.each(function() {
            var $this = $(this);
            $checkbox = $this.find('[type="checkbox"][title]');
            if($checkbox.length) {
                $this.find('label').addClass('tt').attr('title', $checkbox.attr('title'));
            }

            humhub.require('ui.additions').apply($this, 'tooltip');
        })
    });



    $('#wikipage-is_category').click(function () {
        <?php if ($model->page->is_category): ?>
        if ($(this).is(":not(:checked)")) {
            if (!confirm('<?= Yii::t('WikiModule.base', 'Are you really sure? All existing category page assignments will be removed!'); ?>')) {
                $(this).prop('checked', true);
            }
        }
        <?php endif; ?>
        hideCategorySelect();
    });

    hideCategorySelect();

    function hideCategorySelect() {
        if ($('#wikipage-is_category').is(":not(:checked)")) {
            $('.field-wikipage-parent_page_id').show();
        } else {
            $('.field-wikipage-parent_page_id').hide();
        }
    }
<?= Html::endTag('script') ?>