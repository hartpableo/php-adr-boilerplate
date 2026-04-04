# JUNIE.md — Project Architecture Context

This file provides Junie AI with the architectural context needed to contribute correctly to this codebase. Read it before writing or suggesting any code.

---

## Architecture: Action–Domain–Responder (ADR)

This project uses **ADR**, a PHP 8.1+ architectural pattern proposed by [Paul M. Jones](https://pmjones.io/adr/). ADR is a stricter, HTTP-focused alternative to MVC. The core rule: **every concern has exactly one class, and every class has exactly one job.**

If you come from an MVC background, the biggest mindset shift is this: there are no controller classes with multiple methods. Each HTTP operation (list, create, delete, etc.) is its own standalone class.

---

## The Four Layers

### 1. Action (`src/Action/`)

- One class per HTTP operation, never per resource.
- Its only job: collect input from the request, call the Domain, pass the result to the Responder.
- **Must not** contain `echo`, `header()`, SQL queries, or business logic.
- Actions are invokable (`__invoke`) and receive `$_SERVER`, `$_GET`, and `$_POST` as plain arrays.

```
src/Action/User/ListUsersAction.php   ← GET /users
src/Action/User/CreateUserAction.php  ← POST /users
src/Action/User/DeleteUserAction.php  ← DELETE /users/{id}
```

**Rule of thumb:** If an Action is doing anything beyond calling the Domain and then calling the Responder, it belongs in a different layer.

---

### 2. Domain (`src/Domain/`)

- Pure business logic. Zero awareness of HTTP.
- Never imports `$_GET`, `$_POST`, `header()`, PSR-7 objects, or anything HTTP-related.
- Composed of three types of files:
  - **Entity** — a readonly value object representing a domain concept (e.g. `UserEntity`). All properties are `readonly`. A new instance is created instead of mutating.
  - **Repository Interface** — a contract describing how domain objects are stored. No SQL here.
  - **Service** — the business logic. Calls the repository interface. Validates data. Throws domain exceptions.

```
src/Domain/User/UserEntity.php        ← data shape (value object)
src/Domain/User/UserRepository.php    ← interface (no SQL)
src/Domain/User/UserService.php       ← business logic
```

**The Domain is the shared language of the entire application.** The Entity travels through every layer — Infrastructure creates it, the Service validates and returns it, the Responder serialises it.

---

### 3. Responder (`src/Responder/`)

- The **only** layer allowed to call `echo`, `header()`, and `http_response_code()`.
- Receives domain objects and turns them into an HTTP response (JSON, HTML, redirect, etc.).
- Content negotiation (Accept header: JSON vs HTML) lives here, not in the Action.
- Responders are invokable.

```
src/Responder/UserResponder.php
```

**If you see `echo` or `header()` anywhere outside a Responder class, something has leaked into the wrong layer.**

---

### 4. Infrastructure (`src/Infrastructure/`)

- Implements the repository interfaces defined in the Domain.
- This is where actual SQL queries live.
- Swappable: replace `PdoUserRepository` with Eloquent or Doctrine without touching Domain or Action code.
- Maps raw database rows into Domain Entities.

```
src/Infrastructure/Persistence/PdoUserRepository.php
```

---

## File Structure

```
src/
├── Action/
│   └── User/
│       ├── ListUsersAction.php
│       ├── CreateUserAction.php
│       └── DeleteUserAction.php
├── Domain/
│   └── User/
│       ├── UserService.php
│       ├── UserEntity.php
│       └── UserRepository.php        ← interface, not implementation
├── Responder/
│   └── UserResponder.php
└── Infrastructure/
    └── Persistence/
        └── PdoUserRepository.php     ← implements UserRepository

public/
└── index.php                         ← front controller (single entry point)
```

---

## Request Lifecycle (GET /users)

```
HTTP Request
    ↓
public/index.php       — boots Composer, wires dependencies, routes to the correct Action
    ↓
ListUsersAction        — extracts input, calls UserService::fetchAll()
    ↓
UserService            — applies business rules, calls UserRepository::findAll()
    ↓
PdoUserRepository      — runs SQL, maps rows → UserEntity[]
    ↑
UserEntity[]           — travels back up through Service → Action → Responder
    ↓
UserResponder          — sets headers, status code, echoes JSON
    ↓
HTTP Response
```

---

## Front Controller (`public/index.php`)

Every request enters through a single file. Dependencies are wired here manually (or via a DI container like PHP-DI in production). A simple router maps path + method to the correct Action.

```php
$pdo       = new \PDO('sqlite::memory:');
$repo      = new PdoUserRepository($pdo);
$service   = new UserService($repo);
$responder = new UserResponder();
$action    = new ListUsersAction($service, $responder);

if ($path === '/users' && $method === 'GET') {
    $action($_SERVER, $_GET, $_POST);
}
```

---

## The Four Rules — Never Break These

1. **One Action per HTTP operation, not per resource.** `CreateUserAction` and `ListUsersAction` are separate classes, not methods on a `UserController`.

2. **Domain never touches HTTP.** No `$_GET`, no `header()`, no PSR-7 request objects. It only operates on domain objects.

3. **Responder handles all output.** Headers, status codes, templates, JSON serialisation. If `echo` or `header()` appears outside a Responder, something is in the wrong place.

4. **Infrastructure is a swappable detail.** Replace `PdoUserRepository` with Eloquent or Doctrine without touching Domain or Action code.

---

## Smell Test — Quick Sanity Check

| Symptom | Fix |
|---|---|
| `echo` inside an Action | Move output to the Responder |
| Domain service reads `$_GET` or HTTP headers | Pass the data as plain arguments from the Action |
| Action contains SQL queries or direct DB calls | SQL belongs in Infrastructure, accessed via the Domain |
| One Action class has methods for list, create, and delete | Split into `ListUsersAction`, `CreateUserAction`, etc. |

---

## Key PHP Conventions in This Codebase

- Indentations: Use **2 spaces** for all files (PHP, Twig, JS, etc.).
- Front-end design system uses Bootstrap 5.
- PHP 8.1+ — use `readonly` properties, constructor property promotion, enums, and named arguments where appropriate.
- Classes are `final` by default unless there is a clear reason for inheritance.
- Repository interfaces live in the Domain, not Infrastructure.
- Entities are immutable value objects — no setters, no mutation. Create a new instance instead.
- Type declarations are required on all method signatures (parameters and return types).
- Dependency injection via constructor only — no service locators, no static calls.

---

## What Junie Should Not Do

- Do not add `echo` or `header()` calls outside of a Responder class.
- Do not add HTTP-related imports (`$_GET`, `$_POST`, PSR-7, etc.) inside Domain classes.
- Do not add methods to an existing Action class for a new operation — create a new Action class instead.
- Do not put SQL or database calls in an Action or Domain Service.
- Do not bypass the repository interface by calling Infrastructure classes directly from outside Infrastructure.

---

## Further Reading

- Original ADR specification: [pmjones.io/adr](https://pmjones.io/adr/)
