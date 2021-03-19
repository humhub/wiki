<?php
/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2018 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 *
 */

/**
 * Created by PhpStorm.
 * User: davidborn
 */

namespace humhub\modules\wiki\models\forms;

use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\TableSchema;
use yii\db\Transaction;

abstract class ItemDrop extends Model
{
    /**
     * @var ActiveRecord
     */
    private $model;

    /**
     * @var string
     */
    public $modelClass;

    /**
     * @var integer
     */
    public $index;

    /**
     * @var integer
     */
    public $id;

    public $targetId;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'index', 'targetId'], 'integer']
        ];
    }

    public function formName()
    {
        return 'ItemDrop';
    }

    public function save()
    {
        try {
            $this->moveItemIndex($this->id, $this->index);
            return true;
        } catch (\Throwable $e) {
            Yii::error($e);
        }

        return false;
    }

    /**
     * @param $id
     * @param $newIndex
     * @return bool
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    protected function moveItemIndex($id, $newIndex)
    {

        /** @var $transaction Transaction */
        $transaction = $this->beginTransaction();

        try {
            $model = $this->getModel($id);
            $tableName = $this->getTableName();

            // Load all items to sort and exclude the model we want to resort
            $itemsToSort = $this->getSortItemsQuery()->andWhere(['!=', $tableName.'.id', $id])->all();

            $newIndex = $this->validateIndex($newIndex, $itemsToSort);

            if($this->getSortOrder($model) === $newIndex) {
                $transaction->rollBack();
                return true;
            }

            $this->updateTarget();

            // Add our model to the new index
            array_splice($itemsToSort, $newIndex, 0, [$model]);

            foreach ($itemsToSort as $index => $item) {
                $this->updateSortOrder($item, $index);
            }

            $transaction->commit();
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    /**
     * @return Transaction
     */
    protected function beginTransaction()
    {
        return call_user_func($this->modelClass.'::getDb')->beginTransaction();
    }

    protected function validateIndex($newIndex, $itemsToSort)
    {
        // make sure no invalid index is given
        if ($newIndex < 0) {
            return 0;
        } else if ($newIndex >= count($itemsToSort) + 1) {
           return count($itemsToSort) - 1;
        }

        return $newIndex;
    }

    protected function getSortOrder($model)
    {
        return $model->sort_order;
    }

    protected function updateSortOrder($model, $sortOrder)
    {
        return $model->updateAttributes(['sort_order' => $sortOrder]);
    }

    protected function getTableName()
    {
        /* @var $schema TableSchema */
        $schema = call_user_func($this->modelClass.'::getTableSchema');
        return $schema->fullName;
    }

    /**
     * @param $id mixed
     * @return ActiveRecord
     */
    protected function getModel()
    {
        if(!$this->model) {
            $this->model = $this->loadModel();
        }

        return $this->model;
    }

    /**
     * @param $id
     * @return ActiveRecord
     */
    protected function loadModel()
    {
        return call_user_func($this->modelClass.'::findOne', ['id' => $this->id]);
    }

    /**
     * @return ActiveQuery
     */
    protected abstract function getSortItemsQuery();
    protected abstract function updateTarget();


    public function getSortableModel()
    {
        if(!$this->model) {
            $this->model = call_user_func("$this->modelClass::findOne", ['id' => $this->modelId]);
        }

        return $this->model;
    }
}