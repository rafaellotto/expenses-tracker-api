<?php

namespace Api;

use \ApiTester;

class AuthCest
{
    public function _before(ApiTester $I): void
    {
        //
    }

    public function testUsersCanRegister(ApiTester $I)
    {
        $email = 'email@example.com';
        $password = 'password';

        $I->sendPostAsJson('/auth/register', []);
        $I->seeResponseCodeIs(422);

        $I->sendPostAsJson('/auth/register', [
            'email' => $email,
            'password' => $password,
        ]);

        $I->seeResponseCodeIs(201);
        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'email' => $email,
        ]);
    }
}
