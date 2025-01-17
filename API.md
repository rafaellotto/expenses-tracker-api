# API Endpoints

- This is a REST API and all request and responses are in JSON format
- All endpoints use the base address: `http://localhost:8000`

## Authentication and login

<details>
    <summary>
        <code>POST</code>
        <code><b>/auth/register</b></code>
        <code>Register a new user</code>
    </summary>

### Body Params

> | Name     | Required | Type     | Description                                 |
> |----------|----------|----------|---------------------------------------------|
> | `email`    | Yes      | string   | A valid and unique email                    |
> | `password` | Yes      | string   | A password containing at least 8 characters |                       

### Responses

> | Code  | Response                                                                 |
> |-------|--------------------------------------------------------------------------|
> | `201` | `{"id":1,"email":"email@example.com","createdAt":"2025-01-15 01:00:00"}` |
> | `422` | `[{"field":"email","message":"Email has already been taken"}]`           |

</details>

<details>
    <summary>
        <code>POST</code>
        <code><b>/auth/login</b></code>
        <code>Login a user and receive token</code>
    </summary>

### Body Params

> | Name       | Required | Type   | Description                  |
> |------------|----------|--------|------------------------------|
> | `email`    | Yes      | string | The registered user email    |
> | `password` | Yes      | string | The registered user password |

### Responses

> | Code  | Response                                                               |
> |-------|------------------------------------------------------------------------|
> | `200` | `{"message": "Logged in successfully", "token": "eyJ0eXAiOiJKV1Q..."}` |
> | `422` | `[{"field": "email", "message": "Email has already been taken."}]`     |

</details>

## Expenses

- After the token has been received using the login endpoint, you can make authenticated requests sending the requests with header `Authorization: Bearer {token}`

<details>
    <summary>
        <code>POST</code>
        <code><b>/expenses</b></code>
        <code>Create a new expense</code>
    </summary>

### Body Params

> | Name          | Required | Type           | Description                                                             |
> |---------------|----------|----------------|-------------------------------------------------------------------------|
> | `description` | Yes      | string         | Text describing the expense                                             |
> | `date`        | Yes      | string (Y-m-d) | Date of the expense                                              |                       
> | `amount`      | Yes      | float          | Amount paid (e.g. `15.99`)                                                |
> | `category`    | Yes      | string         | One of the allowed categories<br/>`Alimentação`, `Transporte` or `Lazer` |

### Responses

> | Code  | Response                                                                                                                                                   |
> |-------|------------------------------------------------------------------------------------------------------------------------------------------------------------|
> | `201` | `{"id": 1, "description": "...", "date": "2025-01-15", "category": "Alimentação", "createdAt": "2025-01-15 15:00:00", "updatedAt": "2025-01-15 15:00:00"}` |
> | `401` | `{"message": "Your request was made with invalid or expired JSON Web Token"}`                                                                              |
> | `422` | `[{"field": "description","message": "Description can not be empty."}]`                                                                                    |

</details>

<details>
    <summary>
        <code>GET</code>
        <code><b>/expenses</b></code>
        <code>List all expenses</code>
    </summary>

### Pagination

By default, this endpoint is paginated. The response headers have more information for the pagination.

> | Header                    | Value | Description                                     |
> |---------------------------|-------|-------------------------------------------------|
> | `X-Pagination-Total-Count`  | 14    | Represents the number of registers              |
> | `X-Pagination-Page-Count`   | 2     | Represents the number of pages                  |                       
> | `X-Pagination-Current-Page` | 1     | Represents the current page                     |
> | `X-Pagination-Per-Page`     | 10    | Represents how many registers are shown by page |

### Query Params

> | name       | description                                                                                                |
> |------------|------------------------------------------------------------------------------------------------------------|
> | `sort`     | `-date`: date DESC<br />`+date`: date ASC                                                                  |
> | `category` | String to search for category name                                                                         |                       
> | `month`    | String in format Y-m to filter expenses by date<br/>e.g. `2025-01` will return only expenses of this month |
> | `page`     | Number of the page for pagination                                                                          |

### Responses

> | Code  | Response                                                                                                                                                                        |
> |-------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
> | `200` | `[{"id": 1, "description": "...", "category": "Alimentação", "amount": "19.99", "date": "2025-01-15", "createdAt": "2025-01-15 15:00:00", "updatedAt": "2025-01-15 15:00:00"}]` |
> | `401` | `{"message": "Your request was made with invalid or expired JSON Web Token"}`                                                                                                   |

</details>

<details>
    <summary>
        <code>GET</code>
        <code><b>/expenses/[ID]</b></code>
        <code>Show an expense</code>
    </summary>

### Responses

> | Code  | Response                                                                                                                                                                        |
> |-------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
> | `200` | `[{"id": 1, "description": "...", "category": "Alimentação", "amount": "19.99", "date": "2025-01-15", "createdAt": "2025-01-15 15:00:00", "updatedAt": "2025-01-15 15:00:00"}]` |
> | `401` | `{"message": "Your request was made with invalid or expired JSON Web Token"}`                                                                                                   |
> | `404` | `{"message": "Object not found: 1"}`                                                                                                                                            |

</details>

<details>
    <summary>
        <code>PUT</code>
        <code><b>/expenses/[ID]</b></code>
        <code>Update an expense</code>
    </summary>

### Body Params

> | Name          | Required | Type           | Description                                                             |
> |---------------|----------|----------------|-------------------------------------------------------------------------|
> | `description` | Yes      | string         | Text describing the expense                                             |
> | `date`        | Yes      | string (Y-m-d) | Date of the expense                                              |                       
> | `amount`      | Yes      | float          | Amount paid (e.g. `15.99`)                                                |
> | `category`    | Yes      | string         | One of the allowed categories<br/>`Alimentação`, `Transporte` or `Lazer` |


### Responses

> | Code  | Response                                                                                                                                                                        |
> |-------|---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
> | `200` | `[{"id": 1, "description": "...", "category": "Alimentação", "amount": "19.99", "date": "2025-01-15", "createdAt": "2025-01-15 15:00:00", "updatedAt": "2025-01-15 15:00:00"}]` |
> | `401` | `{"message": "Your request was made with invalid or expired JSON Web Token"}`                                                                                                   |
> | `404` | `{"message": "Object not found: 1"}`                                                                                                                                            |
> | `422` | `[{"field": "description","message": "Description can not be empty."}]`                                                                                                         |

</details>

<details>
    <summary>
        <code>DELETE</code>
        <code><b>/expenses/[ID]</b></code>
        <code>Delete an expense</code>
    </summary>

### Responses

> | Code  | Response                                                                      |
> |-------|-------------------------------------------------------------------------------|
> | `204` | No body. Expense deleted successfully                                         |
> | `401` | `{"message": "Your request was made with invalid or expired JSON Web Token"}` | 
> | `404` | `{"message": "Object not found: 1"}`                                          |

</details>

