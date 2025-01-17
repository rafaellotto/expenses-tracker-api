# Personal Expenses Tracking API

## Installation

1. Copy `.env.example` to `.env`
2. Run `./docker/scripts/setup`

## Documentation
[API documentation](API.md)

## Technical Decisions

- Use of MVC pattern following Yii structure to enable fast integration for new developers
- Use of scripts to simplify installation and testing 
- No use of refresh tokens to increase security
- The category field was designed as enum to improve performance compared to varchar, but ideally this would be a foreign key
- All business logic is on models to keep controllers small, but with more features will have to change to another pattern (e.g. service pattern)


## Testing

- Run `./docker/scripts/codecept run` to execute all tests
