<?php

namespace app\models;

use app\enums\CategoryEnum;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

class Expense extends ActiveRecord
{
    public function rules(): array
    {
        $categoryValues = CategoryEnum::values();

        $message = 'Allowed values are ';
        $message .= join(', ', $categoryValues);

        return [
            [['description', 'category', 'amount', 'date'], 'required'],
            ['description', 'string', 'max' => 255],
            ['amount', 'number', 'min' => 0.01],
            ['date', 'date', 'format' => 'php:Y-m-d'],
            ['category', 'in', 'range' => CategoryEnum::values(), 'message' => $message],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => TimestampBehavior::class,
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
            ],
            'value' => function () {
                $now = new \DateTime('now', new \DateTimeZone(Yii::$app->timeZone));
                return $now->format('Y-m-d H:i:s');
            },
        ];

        return $behaviors;
    }

    public function fields(): array
    {
        $fields = parent::fields();

        $fields['createdAt'] = 'created_at';
        $fields['updatedAt'] = 'updated_at';

        unset($fields['user_id']);
        unset($fields['created_at']);
        unset($fields['updated_at']);

        return $fields;
    }

    public function beforeSave($insert): bool
    {
        if(parent::beforeSave($insert)) {
            if ($insert) {
                $this->user_id = Yii::$app->user->id;
            }

            return true;
        }

        return false;
    }

}