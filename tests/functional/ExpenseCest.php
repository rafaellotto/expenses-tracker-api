<?php


namespace Functional;

use app\enums\CategoryEnum;
use app\models\Expense;
use app\models\User;
use \FunctionalTester;

class ExpenseCest
{
    public string $email = 'email@example.com';
    public string $password = 'password';
    public User $user;
    public User $anotherUser;
    public Expense $expense;

    public function _before(FunctionalTester $I): void
    {
        $this->user = new User;
        $this->user->email = $this->email;
        $this->user->password = $this->password;
        $this->user->save();

        $this->anotherUser = new User;
        $this->anotherUser->email = 'another@example.com';
        $this->anotherUser->password = $this->password;
        $this->anotherUser->save();

        $this->expense = new Expense([
            'user_id' => $this->user->id,
            'description' => 'Description',
            'amount' => 99.99,
            'category' => CategoryEnum::FOOD->value,
            'date' => '2025-01-15',
        ]);
        $this->expense->save();
    }

    public function _makeAuthenticatedRequest(FunctionalTester $I): void
    {
        $token = $this->user->generateToken();
        $I->haveHttpHeader('Authentication', 'Bearer '.$token);
    }

    public function unauthorizedErrors(FunctionalTester $I): void
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

    public function createExpense(FunctionalTester $I): void
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

    public function listExpenses(FunctionalTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $I->sendGet('/expenses?'.http_build_query([
            'category' => CategoryEnum::FOOD->value,
            'month' => '2025-01',
            'sort' => '-date',
        ]));

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
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
    }

    public function viewExpense(FunctionalTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $I->sendGet('/expenses/'.$this->expense->id);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
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
    }

    public function updateExpense(FunctionalTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $I->sendPut('/expenses/'.$this->expense->id, [
            'description' => 'New description',
            'date' => $this->expense->date,
            'amount' => $this->expense->amount,
            'category' => $this->expense->category,
        ]);

        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
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
        $I->seeResponseContains('"description":"New description"');
    }

    public function deleteExpense(FunctionalTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $I->sendDelete('/expenses/'.$this->expense->id);

        $I->seeResponseCodeIs(204);
    }

    public function cantSeeOthersExpenses(FunctionalTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $expense = new Expense;
        $expense->user_id = $this->anotherUser->id;
        $expense->description = 'Description';
        $expense->amount = 10.00;
        $expense->category = CategoryEnum::LEISURE->value;
        $expense->date = '2025-01-15';
        $expense->save();

        $I->sendGet('/expenses');

        $I->seeResponseCodeIs(200);
        $I->seeResponseContains('[]');
    }

    public function cantDeleteOthersExpenses(FunctionalTester $I): void
    {
        $this->_makeAuthenticatedRequest($I);

        $expense = new Expense;
        $expense->user_id = $this->anotherUser->id;
        $expense->description = 'Description';
        $expense->amount = 10.00;
        $expense->category = CategoryEnum::LEISURE->value;
        $expense->date = '2025-01-15';
        $expense->save();

        $I->sendDelete('/expenses/'.$expense->id);

        $I->seeResponseCodeIs(401);
    }
}
