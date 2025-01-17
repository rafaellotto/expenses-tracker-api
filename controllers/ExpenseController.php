<?php

namespace app\controllers;

use app\models\Expense;
use bizley\jwt\JwtHttpBearerAuth;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\rest\ActiveController;

class ExpenseController extends ActiveController
{
    public $modelClass = Expense::class;

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
        ];

        $behaviors['access'] = [
            'class' => AccessControl::class,
            'rules' => [
                ['allow' => true, 'roles' => ['@']],
            ],
        ];

        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = []): void
    {
        if ($action === 'view' || $action === 'update' || $action === 'delete' ) {
            if ($model->user_id !== \Yii::$app->user->id) {
                throw new \yii\web\ForbiddenHttpException(
                    sprintf('You can only %s expenses that you\'ve created.', $action)
                );
            }
        }
    }

    public function actions(): array
    {
        $actions = parent::actions();

        unset($actions['index']);

        return $actions;
    }

    public function actionIndex(): ActiveDataProvider
    {
        $query = $this->modelClass::find();

        $query->andWhere(['user_id' => \Yii::$app->user->id]);

        $params = \Yii::$app->request->queryParams;

        if (! empty($params['month'])) {
            if (preg_match('/^\d{4}-\d{2}$/', $params['month'])) {
                $startDate = date('Y-m-d', strtotime("first day of " . $params['month']));
                $endDate = date('Y-m-d', strtotime("last day of " . $params['month']));

                $query->andWhere(['between', 'date', $startDate, $endDate]);
            } else {
                throw new \yii\web\BadRequestHttpException('Invalid period format. Use Y-m (e.g. 2025-01).');
            }
        }

        if (! empty($params['category'])) {
            $query->andWhere(['like', 'category', $params['category']]);
        }

        $query->orderBy('id DESC');

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => \Yii::$app->params['paginationPageSize'],
            ],
        ]);
    }
}
