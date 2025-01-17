<?php


namespace Functional;

use app\models\User;
use \FunctionalTester;

class AuthCest
{
    public string $email = 'email@example.com';
    public string $password = 'password';

    public function registrationWithoutPassword(FunctionalTester $I): void
    {
        $I->wantTo('Try to register without password');

        $I->sendPost('/auth/register', [
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

    public function registrationWithoutEmail(FunctionalTester $I): void
    {
        $I->wantTo('Try to register without email');

        $I->sendPost('/auth/register', [
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

    public function registrationWithIncorrectEmail(FunctionalTester $I): void
    {
        $I->wantTo('Try to register with incorrect email');

        $I->sendPost('/auth/register', [
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

    public function registrationWithSmallPassword(FunctionalTester $I): void
    {
        $I->wantTo('Try to register with small password');

        $I->sendPost('/auth/register', [
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

    public function registrationSuccessful(FunctionalTester $I): void
    {
        $I->wantTo('Register successful');

        $I->sendPost('/auth/register', [
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

    public function loginSuccessfully(FunctionalTester $I): void
    {
        $I->wantTo('Login successfully');

        $user = new User();
        $user->email = $this->email;
        $user->password = $this->password;
        $user->save();

        $I->sendPost('/auth/login', [
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

    public function loginError(FunctionalTester $I): void
    {
        $I->wantTo('Try to login with wrong password');

        $I->sendPost('/auth/login', [
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
