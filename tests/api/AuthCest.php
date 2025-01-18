<?php

namespace tests\api;

use ApiTester;
use app\models\User;

class AuthCest
{
    public string $email;
    public string $password = 'password';

    public function _before(): void
    {
        $this->email = time().'@example.com';
    }

    public function registrationWithoutPassword(ApiTester $I): void
    {
        $I->wantTo('Try to register without password');

        $I->sendPostAsJson('/auth/register', [
            'email' => $this->email,
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'field' => 'password',
                'message' => 'Password cannot be blank.',
            ]
        ]);
    }

    public function registrationWithoutEmail(ApiTester $I): void
    {
        $I->wantTo('Try to register without email');

        $I->sendPostAsJson('/auth/register', [
            'password' => $this->password,
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'field' => 'email',
                'message' => 'Email cannot be blank.',
            ]
        ]);
    }

    public function registrationWithIncorrectEmail(ApiTester $I): void
    {
        $I->wantTo('Try to register with incorrect email');

        $I->sendPostAsJson('/auth/register', [
            'email' => 'email',
            'password' => $this->password,
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'field' => 'email',
                'message' => 'Email is not a valid email address.',
            ]
        ]);
    }

    public function registrationWithSmallPassword(ApiTester $I): void
    {
        $I->wantTo('Try to register with small password');

        $I->sendPostAsJson('/auth/register', [
            'email' => $this->email,
            'password' => '123',
        ]);

        $I->seeResponseCodeIs(422);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            [
                'field' => 'password',
                'message' => 'Password should contain at least 8 characters.',
            ]
        ]);
    }

    public function registrationSuccessful(ApiTester $I): void
    {
        $I->wantTo('Register successful');

        $I->sendPostAsJson('/auth/register', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'id' => 'integer',
            'email' => 'string',
            'createdAt' => 'string',
        ]);
        $I->seeResponseContains("\"email\":\"$this->email\"");
        $I->dontSeeResponseContains('"password":');
    }

    public function loginSuccessfully(ApiTester $I): void
    {
        $I->wantTo('Login successfully');

        $user = new User();
        $user->email = $this->email;
        $user->password = $this->password;
        $user->save();

        $I->sendPostAsJson('/auth/login', [
            'email' => $this->email,
            'password' => $this->password,
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'message' => 'string',
            'token' => 'string',
        ]);
    }

    public function loginError(ApiTester $I): void
    {
        $I->wantTo('Try to login with wrong password');

        $I->sendPostAsJson('/auth/login', [
            'email' => $this->email,
            'password' => 'wrong-password',
        ]);

        $I->seeResponseCodeIs(401);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'message' => 'Invalid email or password.',
        ]);
    }
}
