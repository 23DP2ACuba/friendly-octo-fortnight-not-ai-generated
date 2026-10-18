# Backend: 11-commit plan (skeleton → BudgetNest API → dockerized app)

> PLAN ONLY — no commits were made. Run the commands below in order (from the
> repo root) to replay the backend evolution in 11 commits. Each step stages
> only the files listed, so after step 11 `git status --short backend` is empty
> and the tree matches the current working copy.
>
> Baseline: the `first commit` skeleton already contains stub controllers
> (empty `//` bodies), stub models, stub seeders, the default `DatabaseSeeder`,
> the `2024_01_01_*` domain migrations, and the fully wired
> `backend/routes/api.php` (unchanged throughout — verify with
> `git diff HEAD -- backend/routes`).

## 1. Bump Sanctum dependency

```bash
git add backend/composer.json backend/composer.lock
git commit -m "backend: bump laravel/sanctum to ^4.3"
```

## 2. Flesh out domain models

Fillable fields, casts and Eloquent relations for budgets, memberships,
categories and transactions.

```bash
git add backend/app/Models/Budget.php backend/app/Models/BudgetUser.php backend/app/Models/Category.php backend/app/Models/Transaction.php
git commit -m "backend: flesh out Budget, BudgetUser, Category and Transaction models"
```

## 3. User roles, API tokens and user relations

`role` fillable, `HasApiTokens` trait, `ownedBudgets` / `budgets` / `transactions`
relations, plus the published Sanctum config and `personal_access_tokens` table.

```bash
git add backend/app/Models/User.php backend/config/sanctum.php backend/database/migrations/2026_10_01_153317_create_personal_access_tokens_table.php
git commit -m "backend: add user roles, Sanctum tokens and user relations"
```

## 4. Token auth endpoints

Register, login, logout and current-user actions.

```bash
git add backend/app/Http/Controllers/AuthController.php
git commit -m "backend: implement token auth API (register, login, logout, me)"
```

## 5. Category and transaction endpoints

Category CRUD plus transaction CRUD, per-budget listing (`byBudget`, membership
checked) and a transaction index scoped to budgets the user created or belongs to.

```bash
git add backend/app/Http/Controllers/CategoryController.php backend/app/Http/Controllers/TransactionController.php
git commit -m "backend: implement category and transaction endpoints"
```

## 6. Budget, member and invitation endpoints

Budget CRUD (creator attached as `owner`), member removal and email invitations.

```bash
git add backend/app/Http/Controllers/BudgetController.php backend/app/Http/Controllers/InvitationController.php
git commit -m "backend: implement budget CRUD, member removal and invitations"
```

## 7. Admin endpoints

Dashboard stats and searchable user listing.

```bash
git add backend/app/Http/Controllers/AdminController.php
git commit -m "backend: add admin dashboard and user listing endpoints"
```

## 8. Idempotent `role` migration for the users table

The `2024_01_01` migration no longer recreates the tables owned by the default
`0001_01_01` migration — it only adds `role` when missing.

```bash
git add backend/database/migrations/2024_01_01_000001_create_users_table.php
git commit -m "backend: make users role migration idempotent"
```

## 9. Seeders

Default admin / demo users and the default income/expense category catalog
(`firstOrCreate`, safe to re-run), wired through `DatabaseSeeder`.

```bash
git add backend/database/seeders/AdminUserSeeder.php backend/database/seeders/CategorySeeder.php backend/database/seeders/DatabaseSeeder.php
git commit -m "backend: seed default admin users and category catalog"
```

## 10. Containerize the API

Production Dockerfile (PHP 8.4, required by Laravel 13 / Symfony 8.1) and
`.dockerignore`.

```bash
git add backend/Dockerfile backend/.dockerignore
git commit -m "backend: containerize API with PHP 8.4 image"
```

## 11. Dockerize the full app (images and containers)

Brings up the whole stack with `docker-compose up --build`: MySQL 8.0 service
with healthcheck, persistent volumes and isolated network; backend service with
production env, storage volumes and a boot command that generates the app key,
caches config, migrates, seeds and serves on :8000; React frontend image
(`node:20-alpine` build → `serve` on :5173).

```bash
git add docker-compose.yml frontend/Dockerfile frontend/.dockerignore
git commit -m "chore: dockerize full app with compose (db, api, frontend)"
```

## Verify

```bash
git status --short backend   # expect no output: all backend changes committed
git log --oneline -11        # expect the 11 commits above, in order
```
