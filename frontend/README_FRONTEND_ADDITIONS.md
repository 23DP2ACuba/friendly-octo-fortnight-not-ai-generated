# BudgetNest Frontend — New Additions

This document describes the frontend features and components added to the BudgetNest React SPA.

---

## Overview

BudgetNest is a personal finance management application with a React + Vite frontend consuming a Laravel Sanctum-secured API. This README covers the new frontend additions:

- React Query & Recharts dependencies
- New & rebuilt UI components (no more stubs)
- New pages & dashboard enhancements
- New API modules & methods
- Budget-scoped dashboard statistics & charts

---

## 1. Dependencies Installed

`frontend/package.json`

| Package | Version | Purpose |
|---------|---------|---------|
| `@tanstack/react-query` | `^5.69.0` | Server-state management (caching, background refetching) — available for migration from raw axios + `useEffect` |
| `recharts` | `^2.15.0` | Charting library for dashboard analytics (MonthlyTrend, ExpenseByCategory) |

Install:

```bash
cd frontend
npm install
```

---

## 2. New UI Components

All previously empty "stub" components (`<div> {/* stub */}</div>`) have been replaced with functional implementations.

### `components/layout/Sidebar.jsx`
Collapsible navigation drawer with role-aware links:
- Dashboard, Transactions, Budgets, Categories, Profile
- **Admin** link (visible only when `user.role === 'admin'`)
- Logout button
- Collapse toggle for mobile

### `components/layout/Footer.jsx`
Simple footer with copyright and "Back to Home" link.

### `components/transactions/TransactionCard.jsx`
Displays a single transaction with description, category, date, formatted amount (green for income, red for expense), and user name.

### `components/transactions/TransactionList.jsx`
Renders a list of `TransactionCard` components, or an empty-state message when no transactions exist.

### `components/transactions/TransactionForm.jsx`
Create/edit transaction form with fields:
- Budget (select)
- Category (select)
- Type (income/expense)
- Amount (number, min 0.01)
- Description (text)
- Transaction date (date picker)

Accepts `initialData` for edit mode and `categories` / `budgets` prop arrays for select options.

### `components/transactions/TransactionFilters.jsx`
Filter bar with:
- Type (All / Income / Expense)
- Category (populated from `categories` prop)
- From date
- To date
- Reset button

Emits filter changes via `onFilterChange`.

### `components/budgets/BudgetCard.jsx`
Budget summary card showing name, description, total amount, date range, and member count. Buttons for Edit / Delete / View Details.

### `components/budgets/BudgetList.jsx`
Grid of `BudgetCard` components. Empty state with "Create your first budget" link when no budgets exist.

### `components/budgets/BudgetForm.jsx`
Create/edit budget form with:
- Name, Description
- Total Amount
- Start Date / End Date

### `components/budgets/InviteMembers.jsx`
Modal form to invite a user to a budget:
- Email (required, must exist)
- Role (viewer / editor / owner)
- Sends `POST /api/budgets/{id}/invite`

### `components/budgets/BudgetMembers.jsx`
Lists budget members with name, email, role badge (owner/editor/viewer), and Remove button (hidden for the budget creator).

### `components/budgets/PendingInvitations.jsx` **(NEW)**
Displays pending invitations for the current user with Accept / Decline buttons. Fetches via `GET /api/invitations`. Shown on the Dashboard.

### `components/charts/MonthlyTrend.jsx` **(NEW)**
Line chart (via Recharts) showing Income vs Expense over the last 6 months.

### `components/charts/ExpenseByCategory.jsx` **(NEW)**
Donut chart (via Recharts) showing expense breakdown by category, with legend and percentage tooltips.

---

## 3. Updated UI Components

| Component | Change |
|-----------|--------|
| `components/layout/Navbar.jsx` | Navigation links, user greeting, conditional Admin link, logout |
| `components/layout/Modal.jsx` | Fixed backdrop opacity (Tailwind v4 `bg-gray-500/75`), added `relative` positioning to panel |
| `components/ui/Alert.jsx` | Success/error/warning/info variants |
| `components/ui/LoadingSpinner.jsx` | Animated spinner |
| `components/common/EmptyState.jsx` | Generic empty-state message |

---

## 4. Pages

### `pages/Dashboard.jsx` **(ENHANCED)**
- Stats cards: Total Budgets, Total Income, Total Expenses
- **Budget Selector dropdown** — switch between "All Budgets" or a specific budget; charts and stats update accordingly
- `MonthlyTrend` and `ExpenseByCategory` charts
- Recent transactions list (latest 5)
- `PendingInvitations` banner

### `pages/Budgets.jsx` **(ENHANCED)**
- Budget grid with `BudgetCard` components
- Create/Edit/Delete budget modal using `BudgetForm`

### `pages/BudgetDetail.jsx` **(ENHANCED)**
- Budget stats, transactions list, members list
- Invite member modal (uses `InviteMembers`)
- **Leave Budget** button (current user detaches themselves; creator cannot leave)

### `pages/Transactions.jsx` **(ENHANCED)**
- Transaction table with inline Edit / Delete
- Create/Edit transaction modal (uses `TransactionForm`)
- **TransactionFilters** bar for type / category / date range

### `pages/Profile.jsx` **(CONNECTED)**
- Profile information form (name, email) — calls `PUT /api/auth/profile`
- Change password form — calls `PUT /api/auth/password`
- Real API calls replace previous TODO placeholders

### `pages/Login.jsx`, `pages/Register.jsx`, `pages/Landing.jsx`
Authentication and landing pages using `api/auth.js` methods.

---

## 5. API Layer (`frontend/src/api/`)

### `api/auth.js`
- `login(credentials)` — `POST /api/auth/login`
- `register(userData)` — `POST /api/auth/register`
- `logout()` — `POST /api/auth/logout`
- `getCurrentUser()` — `GET /api/auth/user`
- `updateProfile(profileData)` — `PUT /api/auth/profile`
- `updatePassword(passwordData)` — `PUT /api/auth/password`
- `deleteAccount(password)` — `DELETE /api/auth/profile`

### `api/budgets.js`
- `getBudgets()` — `GET /api/budgets`
- `getBudget(id)` — `GET /api/budgets/{id}`
- `createBudget(data)` — `POST /api/budgets`
- `updateBudget(id, data)` — `PUT /api/budgets/{id}`
- `deleteBudget(id)` — `DELETE /api/budgets/{id}`
- `inviteMember(budgetId, data)` — `POST /api/budgets/{id}/invite`
- `removeMember(budgetId, userId)` — `DELETE /api/budgets/{id}/members/{userId}`
- `leaveBudget(budgetId)` — `DELETE /api/budgets/{id}/leave`
- `getMyInvitations()` — `GET /api/invitations`
- `acceptInvitation(budgetId)` — `POST /api/invitations/{id}/accept`
- `declineInvitation(budgetId)` — `POST /api/invitations/{id}/decline`

### `api/transactions.js`
- `getAllTransactions(params)` — `GET /api/transactions` (with optional filters)
- `getTransactions(budgetId, params)` — `GET /api/budgets/{id}/transactions` (throws if no budgetId)
- `getTransaction(id)` — `GET /api/transactions/{id}`
- `createTransaction(data)` — `POST /api/transactions`
- `updateTransaction(id, data)` — `PUT /api/transactions/{id}`
- `deleteTransaction(id)` — `DELETE /api/transactions/{id}`

### `api/categories.js`
- `getCategories()`, `getCategory(id)`, `createCategory(data)`, `updateCategory(id, data)`, `deleteCategory(id)`

### `api/axios.js`
Axios instance with:
- `baseURL` from `VITE_API_URL` (default `http://localhost:8000/api`)
- Bearer token injection from `localStorage.getItem('auth_token')`
- Automatic redirect to `/login` on 401

---

## 6. Context & Hooks

### `context/AuthContext.jsx`
- `user`, `loading`, `login(token, userData)`, `logout()`
- Auto-loads user from `localStorage` token on mount

### `hooks/useAuth.js`
Wrapper around `AuthContext`.

### `hooks/useBudgets.js`
Fetches all budgets via `getBudgets()`.

### `hooks/useTransactions.js`
Fetches transactions for a given `budgetId` via `getTransactions(budgetId)` (skips fetch if no `budgetId`).

---

## 7. How to Run

```bash
# From repo root
docker-compose up --build -d

# Frontend: http://localhost:5173
# Backend API: http://localhost:8000/api
# Database: localhost:3306
```

Default admin credentials (seeded):
- Email: `admin@budgetnest.com`
- Password: `admin123`

---

## 8. Testing

Backend tests (run in Docker):

```bash
docker-compose exec backend php vendor/bin/phpunit
```

Note: Legacy Inertia-based skeleton tests (`AuthenticationTest`, `RegistrationTest`, `ProfileTest`, etc.) are not applicable to the React SPA and will fail. New tests (`AuthTest`, `BudgetTest`, `TransactionTest`, `CategoryTest`, `InvitationTest`, `AdminTest`, model `Unit` tests) pass against the API.

---

## 9. File Summary

**New files:**
- `frontend/src/components/budgets/PendingInvitations.jsx`
- `frontend/src/components/charts/MonthlyTrend.jsx`
- `frontend/src/components/charts/ExpenseByCategory.jsx`

**Modified files:**
- `frontend/package.json` (+ `@tanstack/react-query`, `recharts`)
- `frontend/postcss.config.js` (Tailwind v4 plugin)
- `frontend/src/index.css` (Tailwind v4 `@import`)
- `frontend/src/api/auth.js`, `api/budgets.js`, `api/transactions.js`
- All `components/layout/*`, `components/ui/*`, `components/transactions/*`, `components/budgets/*`
- All `src/pages/*`
