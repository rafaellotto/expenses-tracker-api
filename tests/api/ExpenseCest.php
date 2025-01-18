<?php

namespace tests\api;

use ApiTester;
use app\enums\CategoryEnum;

class ExpenseCest
{
    public function _makeAuthenticatedRequest(ApiTester $I): array
    {
        $email = microtime(true).'@example.com';
        $password = 'password';

        $loggedUser = $I->sendPostAsJson('/auth/register', [
            'email' => $email,
            'password' => $password,
        ]);

        $loginResponse = $I->sendPostAsJson('/auth/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $I->amBearerAuthenticated($loginResponse['token']);

        return $loggedUser;
    }

    private function _makeExpense(ApiTester $I): array
    {
        return $I->sendPostAsJson('/expenses', [
            'description' => 'New description',
            'date' => '2025-01-15',
            'amount' => 20.50,
            'category' => CategoryEnum::TRANSPORTATION->value,
        ]);
    }

    public function unauthorizedErrors(ApiTester $I): void
    {
        $I->wantTo('Test unauthorized access');

        $I->sendGet('/expenses');
        $I->seeResponseCodeIs(401);

        $I->sendGet('/expenses/1');
        $I->seeResponseCodeIs(401);

        $I->sendPost('/expenses', []);
        $I->seeResponseCodeIs(401);

        $I->sendPut('/expenses/1', []);
        $I->seeResponseCodeIs(401);

        $I->sendDelete('/expenses/1');
        $I->seeResponseCodeIs(401);
    }

    public function createExpense(ApiTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $I->sendPost('/expenses', [
            'description' => 'Taxi',
            'date' => '2025-01-15',
            'amount' => 20.50,
            'category' => CategoryEnum::TRANSPORTATION->value,
        ]);

        $I->seeResponseCodeIs(201);
    }

    public function listExpenses(ApiTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $this->_makeExpense($I);

        $I->sendGetAsJson('/expenses');

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        /**
         * TODO: check how to test response array
        $I->seeResponseMatchesJsonType([
            [
                'id' => 'integer',
                'description' => 'string',
                'category' => 'string',
                'amount' => 'string',
                'date' => 'string',
                'createdAt' => 'string',
                'updatedAt' => 'string',
            ]
        ]);
         */
    }

    public function viewExpense(ApiTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $expense = $this->_makeExpense($I);

        $I->sendGetAsJson('/expenses/'.$expense['id']);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        /**
         * TODO: check how to test response array
        $I->seeResponseMatchesJsonType([
            [
                'id' => 'integer',
                'description' => 'string',
                'category' => 'string',
                'amount' => 'string',
                'date' => 'string',
                'createdAt' => 'string',
                'updatedAt' => 'string',
            ]
        ]);
         */
    }

    public function updateExpense(ApiTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $expense = $this->_makeExpense($I);

        $newDescription = 'New description';

        $I->sendPutAsJson('/expenses/'.$expense['id'], [
            'description' => $newDescription,
            'date' => $expense['date'],
            'amount' => $expense['amount'],
            'category' => $expense['category'],
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
        $I->seeResponseContains("\"description\":\"$newDescription\"");
    }

    public function deleteExpense(ApiTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $expense = $this->_makeExpense($I);

        $I->sendDelete('/expenses/'.$expense['id']);

        $I->seeResponseCodeIs(204);
    }

    public function cantSeeOthersExpenses(ApiTester $I): void
    {
        // First user
        $this->_makeAuthenticatedRequest($I);
        $this->_makeExpense($I);

        // Second user
        $this->_makeAuthenticatedRequest($I);
        $I->sendGet('/expenses');

        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('[]');
    }

    public function cantDeleteOthersExpenses(ApiTester $I): void
    {
        // First user
        $this->_makeAuthenticatedRequest($I);

        $expense = $this->_makeExpense($I);

        // Second user
        $this->_makeAuthenticatedRequest($I);

        $I->sendDelete('/expenses/'.$expense['id']);

        $I->seeResponseCodeIs(403);
    }
}
