<?php

namespace app\controllers;

use app\models\User;
use Yii;
use yii\rest\Controller;

class AuthController extends Controller
{
    public function actionRegister(): User
    {
        $user = User::register(Yii::$app->request->post());

        Yii::$app->response->setStatusCode(201);
        unset($user->password);
        return $user;
    }

    public function actionLogin(): array
    {
        $email = Yii::$app->request->post('email');
        $password = Yii::$app->request->post('password');

        $user = User::findOne(['email' => $email]);

        $passwordWasValidated = $user && Yii::$app->getSecurity()
            ->validatePassword($password, $user->password);

        if (! $user || ! $passwordWasValidated) {
            Yii::$app->response->setStatusCode(401);
            return ['message' => 'Invalid email or password.'];
        }

        return [
            'message' => 'Logged in successfully.',
            'token' => $user->generateToken(),
        ];
    }
}