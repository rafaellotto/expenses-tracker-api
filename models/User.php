<?php

namespace app\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;

class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    public function rules(): array
    {
        return [
            [['email', 'password'], 'required'],
            ['email', 'email'],
            ['email', 'unique'],
            ['password', 'string', 'min' => 8],
        ];
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors[] = [
            'class' => AttributeBehavior::class,
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'password',
                ActiveRecord::EVENT_BEFORE_UPDATE => 'password',
            ],
            'value' => fn () => Yii::$app->getSecurity()->generatePasswordHash($this->password),
        ];

        $behaviors[] = [
            'class' => TimestampBehavior::class,
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'created_at',
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

        unset($fields['created_at']);

        $fields['createdAt'] = 'created_at';

        return $fields;
    }

    public static function findIdentity($id)
    {
        //
    }

    public static function findIdentityByAccessToken($token, $type = null): ?User
    {
        $claims = \Yii::$app->jwt->parse($token)->claims();
        $uid = $claims->get('uid');

        if (! is_numeric($uid)) {
            throw new ForbiddenHttpException('Invalid token provided');
        }

        return static::findOne(['id' => $uid]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        //
    }

    public function validateAuthKey($authKey)
    {
        //
    }

    public static function register(array $post): self
    {
        $user = new self;
        $user->attributes = $post;
        $user->save();

        return $user;
    }
}
