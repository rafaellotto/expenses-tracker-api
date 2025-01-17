# Personal Expenses Tracking API

## Installation

1. Copy `.env.example` to `.env` and update the database values
2. Generate a key for JWT using `php yii key/generate`
2. Run `docker compose up -d`

## Documentation
[API documentation](API.md)

## Technical Decisions

-

## Testing

- For the first time, run `./tests/bin/yii migrate`
- Run `./vendor/bin/codecept run` to execute all tests
