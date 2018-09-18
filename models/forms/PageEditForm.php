<?php


namespace humhub\modules\wiki\models\forms;

use humhub\modules\topic\models\Topic;
use humhub\modules\wiki\models\WikiPageRevision;
use humhub\modules\wiki\permissions\AdministerPages;
use humhub\modules\wiki\widgets\WikiEditor;
use humhub\modules\wiki\widgets\WikiRichText;
use Yii;
use yii\base\Model;
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
     * @var
     */
    public $topics = [];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            ['topics', 'safe']
        ];
    }

    /**
     * @param int $id
     * @param string $title
     * @param int $categoryId
     * @throws HttpException
     */
    public function forPage($id = null, $title = null, $categoryId = null)
    {
        $this->page = WikiPage::find()->contentContainer($this->container)->readable()->where(['wiki_page.id' => $id])->one();

        if(!$this->page && !$this->canCreatePage()) {
            throw new HttpException(403);
        }

        if($this->page && !$this->page->content->canEdit()) {
            throw new HttpException(403);
        }

        if(!$this->page) {
            $this->page = new WikiPage($this->container, ['title' => $title, 'scenario' => WikiPage::SCENARIO_CREATE]);
        } else {
            $this->topics = $this->page->content->getTags(Topic::class)->all();
        }

        if ($this->canAdminister()) {
            $this->page->scenario = WikiPage::SCENARIO_ADMIN_EDIT ;
        }

        if($categoryId) {
            $category = WikiPage::find()->contentContainer($this->container)->readable()->where(['wiki_page.id' => $categoryId, 'is_category' => 1])->one();
            if($category) {
                $this->page->parent_page_id = $categoryId;
            }
        }

        $this->revision = $this->page->createRevision();

        return $this;
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

        return WikiPage::getDb()->transaction(function($db) {
            if ($this->page->save()) {
                $this->revision->wiki_page_id = $this->page->id;

                if ($this->revision->save()) {
                    $this->page->fileManager->attach(Yii::$app->request->post('fileList'));
                    Topic::attach($this->page->content, $this->topics);
                    WikiRichText::postProcess($this->revision->content, $this->page);
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * @return array
     * @throws \yii\base\Exception
     */
    public function getCategoryList()
    {
        $categories = [];

        $query = WikiPage::findCategories($this->container);

        if (!$this->isNewPage()) {
            $query->andWhere(['!=', 'wiki_page.id', $this->page->id]);
        }

        $categories[] = '';

        foreach ($query->all() as $category) {
            $categories[$category->id] = $category->title;
        }

        return $categories;
    }

    public function getTitle()
    {
        return ($this->isNewPage())
            ? Yii::t('WikiModule.views_page_edit', '<strong>Create</strong> new page')
            : Yii::t('WikiModule.views_page_edit', '<strong>Edit</strong> page');
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
}