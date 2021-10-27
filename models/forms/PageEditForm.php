<?php


namespace humhub\modules\wiki\models\forms;

use humhub\modules\content\models\Content;
use humhub\modules\topic\models\Topic;
use humhub\modules\topic\permissions\AddTopic;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\widgets\WikiRichText;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use humhub\modules\content\components\ContentContainerActiveRecord;
use humhub\modules\wiki\models\WikiPage;
use humhub\modules\wiki\permissions\CreatePage;

class PageEditForm extends Model
{
    /**
     * @var WikiPage
     */
    public $page;

    /**
     * @var ContentContainerActiveRecord
     */
    public $container;

    /**
     * @var WikiPageRevision
     */
    public $revision;

    /**
     * @var int
     */
    public $latestRevisionNumber;

    /**
     * @var bool
     */
    public $confirmOverwriting = 0;

    /**
     * @var bool
     */
    public $backOverwriting = 0;

    /**
     * @var bool
     */
    public $isPublic;

    /**
     * @var
     */
    public $topics = [];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['topics', 'safe'],
            [['isPublic', 'confirmOverwriting', 'backOverwriting'], 'integer'],
            ['latestRevisionNumber', 'validateLatestRevisionNumber']
        ];
    }

    /**
     * Validate wiki page before saving in order to don't overwrite the latest revision by mistake
     *
     * @param string $attribute
     */
    public function validateLatestRevisionNumber($attribute)
    {
        if ($this->isNewPage()) {
            return;
        }

        if ($this->confirmOverwriting || $this->$attribute == $this->getLatestRevisionNumber()) {
            return;
        }

        if ($this->backOverwriting) {
            // Revert back to edit form with not saved page content after not confirmed overwrite
            $this->addError('backOverwriting', '');
            return;
        }

        // Mark the confirmation checkbox with red style and display it on edit form
        $this->addError('confirmOverwriting', '');
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        $labels = [
            'isPublic' => Yii::t('WikiModule.base', 'Is Public'),
        ];

        if (!$this->isNewPage()) {
            $labels['confirmOverwriting'] = Yii::t('WikiModule.base', 'Overwrite all changes made by :userName on :dateTime.', [
                ':dateTime' => Yii::$app->formatter->asDate($this->page->content->updated_at, 'medium') . ' - ' . Yii::$app->formatter->asTime($this->page->content->updated_at, 'short'),
                ':userName' => $this->page->content->updatedBy ? $this->page->content->updatedBy->displayName : ''
            ]);
        }

        return $labels;
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[WikiPage::SCENARIO_CREATE] = ['topics'];
        $scenarios[WikiPage::SCENARIO_EDIT] =  $this->page->isOwner() ? ['topics', 'latestRevisionNumber', 'confirmOverwriting', 'backOverwriting'] : ['latestRevisionNumber', 'confirmOverwriting', 'backOverwriting'];
        $scenarios[WikiPage::SCENARIO_ADMINISTER] = ['topics', 'isPublic', 'latestRevisionNumber', 'confirmOverwriting', 'backOverwriting'];
        return $scenarios;
    }

    /**
     * @param int $id
     * @param string $title
     * @param int $categoryId
     * @return PageEditForm
     * @throws HttpException
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     * @throws \Throwable
     */
    public function forPage($id = null, $title = null, $categoryId = null)
    {
        $this->page = WikiPage::find()->contentContainer($this->container)->readable()->where(['wiki_page.id' => $id])->one();

        if(!$this->page && !$this->canCreatePage()) {
            throw new HttpException(403);
        }

        if($this->page && !$this->page->canEditWikiPage()) {
            throw new HttpException(403);
        }

        if(!$this->page) {
            $this->page = new WikiPage($this->container, ['title' => $title]);
            $this->setScenario(WikiPage::SCENARIO_CREATE);
        } else {
            $this->setScenario(WikiPage::SCENARIO_EDIT);
            $this->topics = $this->page->content->getTags(Topic::class)->all();
        }

        if ($this->canAdminister()) {
            $this->setScenario(WikiPage::SCENARIO_ADMINISTER) ;
        }

        $category = null;
        if($categoryId) {
            $category = WikiPage::find()->contentContainer($this->container)->readable()->where(['wiki_page.id' => $categoryId, 'is_category' => 1])->one();
            if($category) {
                $this->page->parent_page_id = $categoryId;
            }
        }

        $this->isPublic = $this->getPageVisibility($category);
        $this->revision = $this->page->createRevision();
        $this->latestRevisionNumber = $this->getLatestRevisionNumber();

        return $this;
    }

    private function getLatestRevisionNumber(): int
    {
        /* @var $latestRevision WikiPageRevision */
        $latestRevision = $this->page->latestRevision;
        return $latestRevision instanceof WikiPageRevision ? $latestRevision->revision : 0;
    }

    private function getPageVisibility($category = null)
    {
        if($this->page->isNewRecord && $category) {
            return $category->content->visibility;
        }

        return $this->page->content->visibility;
    }

    public function setScenario($value)
    {
        $this->page->setScenario($value);
        parent::setScenario($value); // TODO: Change the autogenerated stub
    }

    public function load($data, $formName = null)
    {
        return $this->page->load($data) | $this->revision->load($data) | parent::load($data);
    }

    /**
     * @return bool
     * @throws \Throwable
     */
    public function save()
    {
        if(!$this->validate()) {
            return false;
        }

        if($this->isPublic !== null) {
            $this->page->content->visibility = $this->isPublic ? Content::VISIBILITY_PUBLIC : Content::VISIBILITY_PRIVATE;
        }

        return WikiPage::getDb()->transaction(function($db) {
            if ($this->page->save()) {
                $this->revision->wiki_page_id = $this->page->id;

                if ($this->revision->save()) {
                    $this->page->fileManager->attach(Yii::$app->request->post('fileList'));

                    // This check is required because of a bug prior to HumHub v1.3.18 (https://github.com/humhub/humhub-modules-wiki/issues/103)
                    if($this->page->content->container->can(AddTopic::class)) {
                        Topic::attach($this->page->content, $this->topics);
                    }

                    WikiRichText::postProcess($this->revision->content, $this->page);
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @param int|null $parentCategoryId Id of the parent category
     * @param int $level
     * @return array
     * @throws \yii\base\Exception
     */
    public function getCategoryList(int $parentCategoryId = null, int $level = 0)
    {
        $categories = [];

        $query = WikiPage::findCategories($this->container);

        if ($parentCategoryId) {
            $query->andWhere(['wiki_page.parent_page_id' => $parentCategoryId]);
        } else {
            $query->andWhere(['IS', 'wiki_page.parent_page_id', new Expression('NULL')]);
        }

        if (!$this->isNewPage()) {
            $query->andWhere(['!=', 'wiki_page.id', $this->page->id]);
        }

        if (!$parentCategoryId) {
            $categories[] = Yii::t('WikiModule.base', 'None');
        }

        foreach ($query->all() as $category) {
            /* @var WikiPage $category */
            $categories[$category->id] = str_repeat('-', $level) . ' ' . $category->title;
            if ($subCategories = $this->getCategoryList($category->id, ++$level)) {
                $categories = ArrayHelper::merge($categories, $subCategories);
            }
            $level--;
        }

        return $categories;
    }

    public function getTitle()
    {
        return ($this->isNewPage())
            ? Yii::t('WikiModule.base', '<strong>Create</strong> new page')
            : Yii::t('WikiModule.base', '<strong>Edit</strong> page');
    }

    public function isNewPage()
    {
        return $this->page->isNewRecord;
    }

    /**
     * @return boolean can create new wiki site
     * @throws \yii\base\InvalidConfigException
     */
    public function canCreatePage()
    {
        return $this->container->can(CreatePage::class);
    }

    /**
     * @return boolean can manage wiki sites?
     * @throws \yii\base\InvalidConfigException
     */
    public function canAdminister()
    {
        return $this->container->permissionManager->can(AdministerPages::class);
    }

    public function isDisabledField($field)
    {
        return !($this->isEnabledFieldOnModel($this, $field) || $this->isEnabledFieldOnModel($this->page, $field));
    }

    /**
     * @param $model Model
     * @param $field
     * @return bool
     */
    private function isEnabledFieldOnModel($model, $field)
    {
        $scenarios = $model->scenarios();

        if(!isset($scenarios[$model->scenario])) {
            return false;
        }

        $allowed = $scenarios[$model->scenario];

        return in_array($field, $allowed);
    }
}
