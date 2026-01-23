# Workouts App - Implementation Plan

## Overview

This document outlines the implementation roadmap for the Workouts App. Each milestone builds upon the previous one, ensuring a stable foundation before adding complexity.

**Implementation Approach**: Bottom-up, starting with infrastructure and data layer, then building up through business logic to the user interface.

---

## Milestone 0: Environment Setup

**Goal**: Get local development environment running with all required services.

### Tasks

- [ x] Create `.docker/` directory structure
  - `nginx/default.conf` - Laravel-optimized configuration
  - `php/Dockerfile` - PHP 8.4-FPM with extensions (pdo_mysql, redis, gd, zip, opcache)
- [ x] Create `docker-compose.yml` with services:
  - nginx (port 80)
  - php (8.4-fpm with Composer)
  - mysql (8.4)
  - redis
  - phpmyadmin (port 8080)
  - mailpit (ports 1025, 8025)
  - node(port 3000) - for frontend asset building
- [ x] Keep in mind larvel app will reside on root level (not in subfolder), so laravels app/ will be on same level as docker-compose.yml and .docker/ - use this info according when defining volumes and working directories
- [ x] Configure shared network (workouts_network)
- [ x] Configure volumes for persistence (mysql_data, redis_data)
- [ x] Test: `docker-compose up -d` brings up all services

**Deliverable**: Working Docker environment accessible at http://localhost

---

## Milestone 1: Laravel Foundation

**Goal**: Install and configure Laravel with Filament.

### Tasks

- [x] Create Laravel 12 project
  - `composer create-project laravel/laravel .`
- [x] Configure `.env` for Docker services
  - DB_HOST=mysql, DB_DATABASE=workouts
  - CACHE_DRIVER=redis, REDIS_HOST=redis
  - MAIL_HOST=mailpit, MAIL_PORT=1025
- [x] Install Filament 4
  - `composer require filament/filament`
  - `php artisan filament:install --panels`
- [x] Run initial migrations
  - `php artisan migrate`
- [x] Create admin user
  - `php artisan make:filament-user`
- [x] Test: Access Filament panel at http://localhost/admin

**Deliverable**: Laravel + Filament admin panel accessible and functional

---

### System Purpose & Architecture

The app is a **multi-tenant workout training management system** designed for:
- **Gyms/Organizations (Teams)** to manage their coaches and clients
- **Individual Users** to manage their own workouts independently
- **Coaches** to create exercise libraries and assign training programs
- **Clients** to view assigned workouts and track completion

#### User Types

| Type | Description |
|------|-------------|
| **Team User** | Belongs to one or more teams (gym/organization). Has a role within each team. |
| **Individual User** | Works independently with a personal team auto-created for them. Full control over their own data. |

#### Roles & Workflow
| Role | Capabilities |
|------|-------------|
| **Admin** | Full team management, all coach abilities |
| **Coach** | Create exercises, create/schedule trainings, assign to clients, view feedback |
| **Client** | View assigned trainings, mark complete, provide feedback |

#### Individual User Architecture

Individual users are implemented using **personal teams**:
- When an individual user is invited/registered, a personal team is auto-created
- The `teams.is_personal` flag distinguishes personal teams from organization teams
- The user becomes the Admin of their personal team
- All existing team-scoped logic works unchanged
- Individual users can later be invited to organization teams while keeping their personal space

#### User Invitation Flow

1. **Inviting to a Team**: Admin/Coach invites user by email
   - If user exists: attach to team with specified role
   - If new user: create account, attach to team, send password setup email

2. **Inviting as Individual**: Invite user for independent use
   - Create user account
   - Create personal team for them (`is_personal = true`)
   - Attach user as Admin of their personal team
   - Send password setup email

#### Core Tables Overview

| Table | Purpose |
|-------|---------|
| `teams` | Represents a gym/organization OR a personal space. `is_personal` flag distinguishes. |
| `team_user` (pivot) | Associates users with teams and defines their role. A user can belong to multiple teams with different roles. |
| `user_invitations` | Tracks pending invitations with tokens for password setup. |
| `exercises` | Catalog of exercises per team with videos and tags for filtering. |
| `trainings` | Individual workout sessions that can be scheduled and assigned to clients. |
| `exercise_training` (pivot) | Links exercises to trainings with ordering and notes. |
| `training_user` (pivot) | Assigns trainings to clients and tracks completion status and feedback. |

---

## Milestone 2: Database Schema & Models

**Goal**: Create complete database structure with Eloquent models and relationships.

### 2.1 Core Tables Setup

#### Migration: create_teams_table ✅
```php
- id
- name (string, unique)
- slug (string, unique)
- is_personal (boolean, default false)
- owner_id (foreign -> users, nullable, nullOnDelete)
- settings (json, nullable)
- timestamps
```

#### Migration: create_team_user_table ✅
```php
- id
- team_id (foreign -> teams)
- user_id (foreign -> users)
- role (enum: admin, coach, client)
- timestamps
- unique(team_id, user_id)
```

#### Migration: create_exercises_table ✅
```php
- id
- team_id (foreign -> teams, NULLABLE, nullOnDelete)
- name (string)
- description (text, nullable)
- video_urls (json, nullable)
- tags (json, nullable)
- created_by (foreign -> users)
- timestamps, softDeletes
```

#### Migration: create_trainings_table ✅
```php
- id
- team_id (foreign -> teams, NULLABLE, nullOnDelete)
- title (string)
- content (longText, nullable)
- status (enum: draft, scheduled, completed, skipped) default draft
- scheduled_date (date, nullable)
- created_by (foreign -> users)
- timestamps, softDeletes
```

#### Migration: create_exercise_training_table ✅
```php
- id
- training_id (foreign -> trainings)
- exercise_id (foreign -> exercises)
- notes (text, nullable)
- sort_order (integer, default 0)
- timestamps
```

#### Migration: create_training_user_table ✅
```php
- id
- training_id (foreign -> trainings)
- user_id (foreign -> users)
- completed_at (timestamp, nullable)
- feedback (text, nullable)
- timestamps
- unique(training_id, user_id)
```

#### Migration: create_user_invitations_table ✅
```php
- id
- email (string)
- team_id (foreign -> teams, nullable, cascadeOnDelete)
- role (TeamRole enum, nullable)
- token (string, unique)
- invited_by (foreign -> users, cascadeOnDelete)
- accepted_at (timestamp, nullable)
- expires_at (timestamp)
- timestamps
```

### 2.2 Eloquent Models ✅

#### Models Created
- [x] `Team` model
  - Relationships: users (belongsToMany), exercises (hasMany), trainings (hasMany), owner (belongsTo User)
  - Casts: is_personal as boolean, settings as array
  - Scopes: scopeOrganizations(), scopePersonal()
  - Methods: isPersonal()
  - Helper methods: admins(), coaches(), clients()
- [x] `Exercise` model
  - Relationships: team (belongsTo, nullable), creator (belongsTo User), trainings (belongsToMany)
  - Casts: video_urls as array, tags as array
  - Scopes: forTeam($teamId), personal(), forUser($userId)
  - Methods: isPersonal()
  - Soft deletes
- [x] `Training` model
  - Relationships: team (belongsTo, nullable), creator (belongsTo User), exercises (belongsToMany), assignedUsers (belongsToMany)
  - Casts: status as TrainingStatus enum, scheduled_date as date
  - Scopes: forTeam($teamId), scheduled(), draft(), completed(), personal(), forUser($userId)
  - Methods: isPersonal()
  - Soft deletes
- [x] `User` model
  - Relationships: teams (belongsToMany), createdExercises (hasMany), createdTrainings (hasMany), assignedTrainings (belongsToMany)
  - Methods: getRoleInTeam(), hasRole(), isAdmin(), isCoach(), isClient()
- [x] `UserInvitation` model
  - Relationships: team (belongsTo, nullable), inviter (belongsTo User)
  - Casts: role as TeamRole, accepted_at as datetime, expires_at as datetime
  - Methods: isExpired(), isAccepted(), isPending(), isForTeam(), isForIndividual(), generateToken()

### 2.3 Enums ✅

- [x] `app/Enums/TeamRole.php` - Admin, Coach, Client (with HasLabel, HasColor, HasIcon)
- [x] `app/Enums/TrainingStatus.php` - Draft, Scheduled, Completed, Skipped (with HasLabel, HasColor, HasIcon)

### 2.4 Database Seeders ✅

- [x] `TeamSeeder` - Creates 4 named teams + 3 factory teams
- [x] `UserSeeder` - Global admin + admin/coaches/clients per team
- [x] `ExerciseSeeder` - 10 exercises per team
- [x] `TrainingSeeder` - Draft, scheduled, and completed trainings with exercises and assigned users
- [x] `DatabaseSeeder` - Calls all seeders in order

**Deliverable**: ✅ Complete database schema with seedable test data



## Milestone 3: Team Management & Multi-Tenancy ✅

**Goal**: Implement Filament multi-tenancy with team switching and scoped data.

### 3.1 Main Tasks

- [x] Configure Filament panel for tenancy
  - Registered `Team` as tenant model with slug attribute
  - Implemented `HasTenants` interface on User model with `getTenants()` and `canAccessTenant()`
- [x] Create BelongsToTenant trait for automatic scoping
  - Global scope filters queries by current tenant
  - Auto-assigns team_id on model creation
  - Applied to Exercise and Training models
- [x] Update seeders for multi-team testing
  - Super Admin attached to all teams
  - Created multi-team user with different roles in each team
- [x] Test: Switch between teams, verify data isolation
  - 11 passing tests for data isolation, tenant switching, and access control

### 3.2 Team resource in Filament ✅

- [x] Create `TeamResource` in Filament
  - Table columns: name, slug, is_personal, owner (name), members count, created_at
  - Form fields: name (auto-generates slug), slug, is_personal (toggle), owner (searchable select)
  - Actions: edit, delete
  - Bulk actions: delete
  - Filter: by team type (personal/organization)
- [x] Create `TeamPolicy` for system admin access only
  - All abilities check `$user->is_admin`
- [x] Disabled tenancy scoping (`$isScopedToTenant = false`)
  - Resource shows all teams regardless of current tenant
- [x] Wrote 11 smoke tests for TeamResource

### 3.3 Filament panels ✅
- [x] Adapt existing Admin filament panel, to be accesible via admin/login route only for system admins. On route / there should be simple button to navigate to "Admin login"
- [x] Create separate Filament panel called App(route /app) for personal users and teams(coaches and athletes). Accessible via /login route. On route / there should be simple button to navigate to "User login". Panel should be accessible for all roles except system admins.

#### 3.3.1 Admin panel base setup ✅
- [x] Create or just check main resources that are shown to system admins:
  - TeamResource (moved to `App\Filament\Admin\Resources\Teams`)
  - UserResource (created in `App\Filament\Admin\Resources\Users`)

#### 3.3.2 App panel base setup ✅
- [x] Create new filament panel called App (AppPanelProvider).
- [x] Divide it as multi-tenant aware panel as Team entity acts as tenant.
- [x] User in this panel can be coach or client/athlete
  - Panel access controlled via `canAccessPanel()` in User model
- [x] Create basic resources that are shown to team users(coach and clients):
  - ExerciseResource (team scoped, in `App\Filament\App\Resources\Exercises`)
  - TrainingResource (team scoped, in `App\Filament\App\Resources\Trainings`)
  - Current team is switched via filaments native top-level team switcher

### 3.4 Filament homepage ✅
- [x] Replace current Laravel default / route with landing page showing:
  - Admin panel (/admin) - "Admin Login" button
  - User panel (/app) - "User Login" button

**Deliverable**:  Working multi-tenancy with team switching, data isolation, and TeamResource for system admins. Additional distinction of app into two panels: Base, original Admin panel for system admins to manage teams and users; App panel for team users to manage workouts within their teams.
Basic filament homepage with links to both panels.

---

## Milestone 4: User Management & Invitations ✅

**Goal**: Manage users within teams with role assignment and invitation system.

### 4.1 User Resource

### 4.1 Refactor team roles ✅
- [x] Include only roles coach and client/athlete, remove admin role from team_user pivot table
  - Coach
  - Client/Athlete
  - Remove team admin role from team_user pivot table and related code
  - Migrate fresh on any step if needed, edit existing tables

### 4.2 User resource in App panel ✅
- [x] In App panel, make user resource. Scoped by team and visible to coaches only. List users in current team with their role badges.
- [x] Coaches can manage users within their team (view, remove)
- [x] Clients/athletes cannot access User resource.
- [x] Certain coach can be coach in multiple teams, but also he can be client/athlete in other teams. Make sure role is isolated per team(i think it already is, but double check).

### 4.3 Personal team for users(coaches and clients/athletes) ✅
- [x] If user is created/invited as individual user (not to a team), create personal team for them with is_personal = true flag.
- [x] Make sure, each user belong to personal team. In that way, user can plan his own trainings and exercises even if not invited to any organization team. Provide this option for both coaches(they can plan their training for their spare time etc.) and clients/athletes.

### 4.4 User Resource ✅

- [x] Create `UserResource` in Filament (tenant-scoped)
  - Table columns: name, email, role badge (in current team), created_at
  - Form fields: name, email (readonly), role (for current team)
  - Filters: by role
  - Actions: edit, delete (remove from team)
  - Bulk actions: change role
  - Policies: Admins see all users, Coaches see clients only (via query scope)
- [x] Verify user resource in both admin and app panels
  - Admin panel: full user management across all teams
  - App panel: team-scoped user management for coaches only

### 4.5 Team Membership Management ✅
- [x] Simplified approach: UserResource handles team membership directly
  - No separate relation manager needed - UserResource is tenant-scoped
- [x] Ensure role isolation per team
  - User can have different roles in different teams
  - Role changes affect only current team
  - Tests verify role isolation across teams
- [x] In admin panel, team resource, add team members as relationship manager, so system admin can see all users per team and their roles.

### 4.6 User Invitation System 

- [x] Create `InviteUserToTeam` Filament Action (on ListUsers page)
  - Form: email, role (select)
  - Uses `UserInvitationService::inviteToTeam()`
  - If user exists: attach to team immediately, send notification email
  - If new user: create invitation, send email with password setup link, attach to personal team as well

- [x] Create `UserInvitationService` with `inviteAsIndividual()` method
  - Creates invitation with password setup link
  - Auto-creates personal team upon acceptance

- [x] Create invitation acceptance flow
  - Route: `/invitation/accept/{token}` (Livewire component)
  - Form: set password (if new user)
  - Validates token, creates user if needed, accepts invitation
  - Redirects to Filament panel

- [x] Create `UserInvitedNotification` (handles both existing and new users)
  - Contains invitation link with token for new users
  - Contains team access notification for existing users
  - Link expires after 7 days

- [x] Review above tasks and ensure:
  - Admin can create teams and invite coaches
  - Admin can invite users as individual users
  - Coach can invite clients to their team

### 4.7 Policies ✅

- [x] Create `UserPolicy` for UserResource
  - `viewAny`: admin, coach (coach sees clients only via query scope)
  - `view`: admin, coach (for clients)
  - `create`, `update`: admin only
  - `delete`: admin only (removes from team, doesn't delete user)
  - `inviteToTeam`: admin only

### 4.8 User invitation improvements and completions ✅
- [x] In admin panel, replace "New user" button with "Invite user to team" action.
  - [x] User is invited in app as general (inviteAsIndividual)
  - [x] On user creation, personal team is created for them (via UserObserver)
- [x] In app panel, users can belong to multiple teams. Make user able to create new teams from app panel.
  - [x] Create "Register new team" page in app panel (RegisterTeam in App\Filament\App\Pages\Tenancy)
  - [x] On team creation, user becomes coach/owner of that team
  - [x] User can switch between teams via top-level team switcher
  - [x] User can create multiple teams
  - [x] Invited users must accept invitation to team, sent via email with token link
  - [x] User can leave team from Team Settings page in app panel
  - [x] Team owner cannot leave team, must transfer ownership first (Transfer Ownership action)
  - [x] Team owner can remove other users from team (Remove action in UserResource)  

### 4.9 Testing ✅

- [x] Test: Invite existing user to team (UserInvitationTest)
- [x] Test: Invite new user to team, complete password setup (UserInvitationTest)
- [x] Test: Invite individual user, verify personal team created (UserInvitationTest)
- [x] Test: Add existing user to multiple teams, verify role isolation (UserInvitationTest)
- [x] Test: Expired invitation cannot be accepted (UserInvitationTest)
- [x] Test: UserResource access control for admin, coach, client (UserResourceTest)
- [x] Test: UserResource shows correct users per role (UserResourceTest)
- [x] Test: UserResource role filtering (UserResourceTest)


**Deliverable**: Full user management with invitation system and password setup flow (27 passing tests)

---

## Milestone 5: Exercise Library

**Goal**: Create global exercise library and enable teams to copy exercises to their collection.

### Design Decision
When teams "attach" global exercises, **create a copy** with `team_id` set to the team (full independence, teams can customize).

### Tasks

#### 5.1 GlobalExerciseSeeder ✅
- [x] Create `database/seeders/GlobalExerciseSeeder.php`
  - Idempotent using `firstOrCreate()` by name + team_id=null
  - ~60-70 exercises across broad categories:
    - Olympic Weightlifting (8): Snatch, Clean & Jerk, Power Clean, etc.
    - Strength - Lower Body (10): Squat, Deadlift, Lunge, Hip Thrust, etc.
    - Strength - Upper Body - Push (8): Bench Press, Overhead Press, Dips, etc.
    - Strength - Upper Body - Pull (8): Pull-ups, Rows, Lat Pulldown, etc.
    - Cardio (8): Running, Cycling, Rowing, Jump Rope, etc.
    - Core & Abs (8): Plank, Dead Bug, Hanging Leg Raise, etc.
    - Flexibility & Mobility (6): Hip stretches, Shoulder mobility, etc.
    - Plyometrics (6): Box Jump, Burpees, Jump Squat, etc.
    - Functional & Bodyweight (6): Push-ups, Lunges, Air Squat, etc.
  - Tags: strength, cardio, flexibility, core, upper-body, lower-body, full-body, compound, isolation, olympic, plyometric, bodyweight, machine, dumbbell, barbell, kettlebell, unilateral, bilateral, push, pull, endurance, power, mobility
  - Global exercises have `team_id = null`, `created_by = system admin`
- [x] Update `DatabaseSeeder` to call `GlobalExerciseSeeder` before `ExerciseSeeder`

#### 5.2 Exercise Model Updates ✅
- [x] Add `scopeGlobal(Builder $query)` to Exercise model - filters `whereNull('team_id')`

#### 5.3 Admin Panel ExerciseResource (Global Exercises) ✅
- [x] Create `app/Filament/Admin/Resources/Exercises/ExerciseResource.php`
  - Override `getEloquentQuery()` to show only global exercises (`team_id = null`)
  - Table columns: name, tags (badge), created_at
  - Form: name, description (rich text), video_urls (repeater), tags (TagsInput)
  - Actions: edit, delete
- [x] Create pages: ListExercises, CreateExercise, EditExercise
- [x] Create Schemas/ExerciseForm.php and Tables/ExercisesTable.php

#### 5.4 ExerciseLibraryService ✅
- [x] Create `app/Services/ExerciseLibraryService.php`
  - Method: `copyToTeam(array $exerciseIds, Team $team, User $copiedBy): int`
  - Fetches global exercises, skips if name already exists in team
  - Creates copies with team_id set, returns count

#### 5.5 App Panel - Add from Library Action ✅
- [x] Update `app/Filament/App/Resources/Exercises/Pages/ListExercises.php`
  - Add header action "Add from Library"
  - Simple searchable multiselect (search by name and tags)
  - Calls ExerciseLibraryService to copy selected exercises
  - Success notification with count

#### 5.6 Enhance ExercisesTable (App Panel) ✅
- [x] Add filters to `Tables/ExercisesTable.php`:
  - Filter by tag (from JSON field)
  - Filter by creator

#### 5.7 Global Search ✅
- [x] Configure global search in App panel ExerciseResource
  - Search attributes: name, tags
  - Result details: show tags

#### 5.8 ExercisePolicy ✅
- [x] Create `app/Policies/ExercisePolicy.php`
  - viewAny: system admin (global) OR team member (team-scoped)
  - view: system admin OR team member
  - create: system admin (global) OR coach (team-scoped)
  - update/delete: system admin (global) OR coach (team-scoped)
- [x] Policy auto-discovered by Laravel (no manual registration needed)

#### 5.9 Tests ✅
- [x] Create `tests/Feature/ExerciseResourceTest.php` (App Panel)
  - Coach can view, create, edit, delete exercises
  - Client can only view exercises
  - Exercises are team-scoped
- [x] Create `tests/Feature/ExerciseLibraryTest.php`
  - GlobalExerciseSeeder is idempotent
  - Coach can add exercises from library
  - Duplicate names are skipped
  - Copied exercises have correct team_id and created_by
  - Search by name and tag works
- [x] Create `tests/Feature/AdminExerciseResourceTest.php`
  - System admin can CRUD global exercises
  - Only global exercises shown
  - Non-admin cannot access

**Deliverable**: ✅ Global exercise library with copy-to-team functionality (41 passing tests)

---
## Milestone 6: Training Management ✅

**Goal**: Create and manage trainings with exercise attachments.

### Tasks

- [x] Create `TrainingResource` in Filament
  - Table columns: title, status, scheduled_at, assigned_to (count), exercises (count), created_at
  - [x] Port scheduled_date to scheduled_at (datetime, nullable) in DB migration and model
  - Table filters: by status, by date range, by assigned user
  - Form fields:
    - title (required, text input)
    - content (rich text editor)
    - status (select)
    - scheduled_at (datetime picker, nullable)
  - Relation manager: exercises (attach/detach with pivot notes and sort_order)
  - Relation manager: assignedUsers (clients only)
  - Actions: view, edit, delete
  - Bulk actions: delete
- [x] Check and refactor `ExercisesRelationManager` for trainings
  - Review Laravel docs: https://laravel.com/docs/12.x/eloquent-relationships#many-to-many
  - Filament docs: https://filamentphp.com/docs/4.x/resources/managing-relationships
  - You can define intermediate table model: https://filamentphp.com/docs/4.x/resources/managing-relationships
    - Fixed TrainingExercise model to extend Pivot with `$incrementing = true`
  - Training - belongsToMany exercises via pivot table with `->using(TrainingExercise::class)`
  - Exercise - belongsToMany trainings via pivot table with `->using(TrainingExercise::class)`
  - Pivot table exercise_training with id, notes, sort_order, and timestamps
  - Added to both admin and app panels with `allowDuplicates()`
  - Actions: Attach, detach
  - Attach form: select exercise(s), add notes, set sort_order
  - Filament Table fields: exercise name, notes, sort_order
  - Actions: edit pivot data, detach, reorder (drag-and-drop)
- [x] Check and refactor `AssignedUsersRelationManager` for trainings
  - Review Laravel docs: https://laravel.com/docs/12.x/eloquent-relationships#many-to-many
  - Filament docs: https://filamentphp.com/docs/4.x/resources/managing-relationships
  - You can define intermediate table model: https://filamentphp.com/docs/4.x/resources/managing-relationships
    - Fixed TrainingUser model to extend Pivot with `$incrementing = true`
  - Training - belongsToMany users (clients) via pivot table with `->using(TrainingUser::class)`
  - User - belongsToMany trainings via pivot table with `->using(TrainingUser::class)`
  - Pivot table training_user with id, completed_at, feedback, and timestamps
  - Added to both admin and app panels
  - Actions: Attach, detach
  - Attach form: select client(s) from current team
  - Filament Table fields: name, assigned_at, completed status, completed_at, feedback
  - Filters: completed/pending
  - View action to see client feedback details
- [x] Check again `TrainingPolicy` for authorization
  - `viewAny`: team member OR system admin
  - `view`: coach (any team training) OR assigned client OR system admin
  - `create`, `update`, `delete`: coach OR system admin
- [x] Implement view filters based on role
  - Clients: only see assigned trainings (via query scope in getEloquentQuery)
  - Coaches: see all team trainings with filters
  - System admins: see all trainings across all teams
- [x] Ensure trainings are team-scoped
  - BelongsToTenant trait handles automatic scoping
  - team_id set automatically on creation
  - Exercise attachment limited to team exercises only
- [x] Create Admin Panel TrainingResource
  - Shows all trainings across all teams
  - Filter by team, status, date range
  - Full CRUD with relation managers
- [x] Add ability for client/athlete to mark training as completed. This sets timestamp completed at in pivot table training_user. Also provide optional feedback textarea.
  - Created `MarkAsComplete` action on ViewTraining page (Filament action)
    - Form: feedback (textarea, optional)
    - Logic: update pivot record with completed_at = now(), save feedback
    - Show this action only when time is past scheduled_at date
    - Notification: "Training marked as complete", add notification to coach (db notification)
    - Created `TrainingCompletedNotification` for database notifications
    - Added `markComplete` policy method to TrainingPolicy
  - Action visible on TrainingResource view page (visible to clients only)
  - [x] For client/athlete, show feedback form on training view/edit page
    - Added "Your Completion" section on ViewTraining page showing completion timestamp and feedback
    - Added "Edit Feedback" action to allow client to update feedback after completion
    - On MarkAsComplete action, training status is set to Completed
- [x] Tests: 40 passing tests
  - TrainingResourceTest (29 tests): list access, view, create, edit, delete, filtering, mark as complete, edit feedback
  - AdminTrainingResourceTest (11 tests): access control, view all teams, CRUD, filtering

**Deliverable**: ✅ Full training CRUD with exercise and client assignment (33 passing tests)

---


## Milestone 6.5: Coach has clients/athletes overview ✅

**Goal**: In coach panel, Team members resource, when clicking on user(client/athlete), show overview of his assigned trainings with status, to see users history and future trainings.
### Tasks
- [x] In App panel, in Team members resource, create relation manager "Assigned Trainings" for user(client/athlete)
  - Table columns: title, status, scheduled_at, exercises (count), created_at
  - Filters: by status, by date range
  - Actions: view training (navigate to TrainingResource view page)
- [x] Ensure only coaches can see this relation manager
- [x] Test: Coach views client assigned trainings, verifies data (10 passing tests)

**Deliverable**: ✅ Coaches can view client assigned trainings history via ViewUser page with relation manager


---

## Milestone 6.6: Queue setup & Background Jobs ✅
**Goal**: Configure Laravel queue with Redis and Horizon for background job processing.
### Tasks
- [x] Configure `.env` for Redis queue (QUEUE_CONNECTION=redis)
- [x] Install Laravel Horizon (`composer require laravel/horizon`)
- [x] Run `php artisan horizon:install` to publish assets and config
- [x] Configure `config/horizon.php` for Redis queue
- [x] Configure Horizon access gate for system admins in `HorizonServiceProvider`
- [x] Set up separate Horizon Docker container (cleaner than supervisor in PHP container)
  - Added `horizon` service to docker-compose.yml
  - Uses same PHP image with `php artisan horizon` command
  - Depends on php, redis, and mysql services

## Milestone 7: Training Scheduling & Duplication ✅

**Goal**: Implement single and bulk training scheduling.

### Tasks

- [x] Create `BulkScheduleService` (app/Services/)
  - Method: `scheduleTraining()` - schedule single training with optional client assignment
  - Method: `duplicateTraining()` - duplicate training to new date with exercises
  - Method: `duplicateToMultipleDates()` - bulk duplicate with transaction support
  - Method: `generateWeeklyDates()` - generate dates for weekly patterns
- [x] Create unified `ScheduleTrainingAction` (Filament action)
  - Merged schedule and duplicate functionality into single action
  - Form with tabs: Single Date, Multiple Dates (repeater), Weekly Pattern (weeks + days)
  - Options section: copy_exercises toggle, assign_to multi-select
  - Single date mode updates existing training
  - Multiple dates mode duplicates training
  - Shows appropriate notifications
- [x] Cleaned up TrainingsTable
  - Removed columns: Created By, Assigned (count), Exercises (count)
  - Kept columns: Title, Status, Scheduled At, Created At (hidden by default)
  - Row actions: Schedule, View, Edit, Delete
- [x] Tests: 59 passing tests
  - BulkScheduleServiceTest (13 tests): scheduleTraining, duplicateTraining, duplicateToMultipleDates, generateWeeklyDates
  - TrainingResourceTest (46 tests): scheduling action with single/multiple dates, access control

**Deliverable**: ✅ Single and bulk training scheduling functionality with separate Horizon container

---

## Milestone 8: Calendar View ✅

**Goal**: Display trainings in a calendar interface with filtering.

### Tasks

- [x] Choose calendar package
  - Using `saade/filament-fullcalendar:^4.0@beta` for Filament v4 compatibility
- [x] Create custom Filament theme for App panel (required for fullcalendar CSS)
  - Created `resources/css/filament/app/theme.css`
  - Registered in `AppPanelProvider` with `viteTheme()`
  - Registered `FilamentFullCalendarPlugin` in App panel
- [x] Create `CalendarWidget` (extends FullCalendarWidget)
  - Implements `fetchEvents()` method with role-based filtering
  - Events colored by status (Draft=gray, Scheduled=amber, Completed=green, Skipped=red)
  - Loads trainings for visible date range only with eager loading
- [x] Create `CalendarPage` (Filament custom page)
  - Registered in panel navigation with calendar icon
  - Applied in App panel only at `/app/{tenant}/calendar`
  - Shows legend for training status colors
- [x] Access control
  - Coaches: view all team trainings
  - Clients: view assigned trainings only
  - Calendar trainings scoped by current team (tenant)
- [x] Event click action
  - On click, navigates to training detail page (ViewTraining)
  - URL includes tenant slug for proper routing
- [x] Performance tested with 100+ trainings (< 2 seconds)
- [x] Tests: 11 passing tests for calendar functionality

**Deliverable**: ✅ Functional calendar with training display, role-based filtering, and efficient data loading


## Milestone 9: Notifications & Reminders ✅
**Goal**: Implement notification system for training assignments and reminders.

### Tasks
- [x] Verify Laravel and Filament is setup for Filament database notifications
   - https://filamentphp.com/docs/4.x/notifications/database-notifications
   - Added `->databaseNotifications()` to AppPanelProvider
   - Added `->databaseNotifications()` to AdminPanelProvider
   - Notifications table migration exists
   - User model has Notifiable trait
- [x] Notify coaches when clients complete trainings
  - Created `TrainingCompletedNotification` (database notification)
  - Triggers on training completion action in ViewTraining page
  - Coach (training creator) receives database notification
  - Notification includes training details, client info, and feedback
  - Tests verify notification is sent correctly (46 passing tests)

**Deliverable**: ✅ Complete notification system with Filament database notifications UI (bell icon), TrainingCompletedNotification sent to coaches on training completion

---

## Milestone 10: Dashboard & Overview ✅

**Goal**: Create dashboard with relevant information for each role.

### Tasks

- [x] Remove calendar widget from default Filament dashboard, keep it only in Calendar page.
  - Calendar widget was already only on the Calendar page, not on default dashboard
- [x] Create dashboard widgets for Clients
  - `ClientStatsWidget`: Training completion stats (this week/month/year/total)
  - `UpcomingTrainingsWidget`: Upcoming trainings (configurable via `config/workouts.php`, default 3 days)
  - `PendingFeedbackWidget`: List of past trainings awaiting feedback
  - `RecentFeedbackWidget`: Recent feedback submitted by client
- [x] Create dashboard widgets for Coaches
  - `CoachStatsWidget`: Basic stats (total trainings, scheduled, completed, team members, exercises)
  - `RecentCompletionsWidget`: Recent client completions with feedback
- [x] Create dashboard widgets for Admins
  - `AdminStatsWidget`: Global statistics (teams, users, trainings, exercises)
  - `RecentActivityWidget`: Recent trainings from all teams
- [x] Configure Filament dashboard
  - Role-based widget display via `canView()` method on each widget
  - Removed `FilamentInfoWidget` from both panels
- [x] Tests: 29 passing tests for dashboard functionality

**Deliverable**: ✅ Role-specific dashboards with useful widgets

---
## Milestone 10.1: Homepage ✅
**Goal**: Create a landing page/dashboard for route / showing basic info and navigation options.

### Tasks
- [x] Create a new view `resources/views/home.blade.php` for the homepage
  - Show basic info about the application (app name, tagline, feature highlights)
  - Provide navigation links to Admin panel (/admin) and App panel (/app)
  - Show login buttons for both panels with icons and descriptions
- [x] Update web routes to use `Route::view('/', 'home')` (following guidelines - no controller needed for simple view)
- [x] Clean up old `welcome.blade.php` file

**Deliverable**: ✅ Enhanced landing page with branding, feature overview, and login navigation  

---

## Milestone 10.2: Info pages
**Goal**: Create static info pages for Terms of Service and Privacy Policy and feature pages
### Tasks
- [ ] Create new views:
  - `resources/views/terms.blade.php` for Terms of Service
  - `resources/views/privacy.blade.php` for Privacy Policy
  - `resources/views/features.blade.php` for Features overview, add detailed description of app features
- [ ] Update web routes to serve these pages:
  - `Route::view('/terms', 'terms')`
  - `Route::view('/privacy', 'privacy')`
  - `Route::view('/features', 'features')`
- [ ] Add navigation links to these pages in the homepage and footer (if applicable)
- [ ] Populate pages with placeholder text (to be replaced with actual legal content later)
**Deliverable**: ✅ Static info pages accessible from homepage and footer

## Milestone 10.2: Language Support
**Goal**: Implement multi-language support for English and Slovenian languages.
### Tasks
- [ ] Add language files for English and Slovenian in `resources/lang/en` and `resources/lang/sl` respectively.
- [ ] Translate all Filament resources, models, policies, notifications, and UI elements.
- [ ] Implement language switcher in Filament panels (both Admin and App panels)
    - https://filamentphp.com/plugins/bezhansalleh-language-switch, version 4
- [ ] Test: Switch languages in both panels, verify translations

**Deliverable**: Fully localized application with English and Slovenian support


## Milestone 11: Policies & Authorization

**Goal**: Ensure all role-based permissions are correctly enforced.

### Tasks

- [ ] Create comprehensive policies
  - `TeamPolicy`: manage team settings
  - `UserPolicy`: manage team users
  - `ExercisePolicy`: CRUD exercises
  - `TrainingPolicy`: CRUD trainings, view based on role
- [ ] Policy rules by role:
  - **Admin**: All permissions within team
  - **Coach**: Create/edit trainings and exercises, view all clients' data
  - **Client**: View assigned trainings only, complete trainings
- [ ] Add policy checks to all Filament resources
  - Use `authorizeResource()` in resources
  - Apply policies to actions and bulk actions
- [ ] Test authorization matrix
  - Create test scenarios for each role
  - Verify proper access and denial
  - Check API endpoints if any
- [ ] Implement team data isolation middleware
  - Ensure all queries are scoped to current team
  - Prevent cross-team data access

**Deliverable**: Fully enforced role-based access control

---

## Milestone 12: Data Validation & Business Rules

**Goal**: Implement validation rules and business logic constraints.

### Tasks

- [ ] Athlete can't submit feedback for training more than 3 days after scheduled date.
- [ ] Training is marked as missed if not marked as completed within 3 days after scheduled date.
- [ ] Prevent scheduling training in the past.
- [ ] Prevent assigning training to users not in the current team.
- [ ] Prevent deleting exercises that are attached to existing trainings.
- [ ] Prevent deleting users who have created trainings or exercises (require transfer of ownership or soft delete).
- [ ] Add form validation rules
  - Training: title required, scheduled_date in future for new trainings
  - Exercise: name required, video_urls must be valid URLs
  - User: email unique in system, role valid enum
- [ ] Implement business rules
  - Cannot delete exercise if attached to trainings
  - Cannot delete user if they created trainings/exercises (transfer ownership or soft delete)
  - Cannot assign training to user not in current team
  - Cannot schedule training without date
- [ ] Add database constraints
  - Foreign key constraints with CASCADE/RESTRICT as appropriate
  - Unique indexes where needed
  - NOT NULL constraints on required fields
- [ ] Create validation tests
  - Test all validation rules
  - Test business rule enforcement
- [ ] Add user-friendly error messages
  - Custom validation messages
  - Graceful error handling in Filament

**Deliverable**: Robust validation and business rule enforcement

---

## Milestone 13: UI Polish & UX Improvements

**Goal**: Improve user experience and interface polish.

### Tasks

- [ ] Replace deprecated Filament components
  - replace form() with schema()
  - mutateFormDataUsing() with mutateDataUsing()
- [ ] Customize Filament theme
  - Brand colors
  - Logo and favicon
  - Custom fonts (optional)
- [ ] Improve navigation structure
  - Group related resources
  - Add icons to navigation items
  - Badge counts (e.g., pending trainings)
- [ ] Add helpful notifications
  - Success messages for all actions
  - Info notifications for important events
  - Error messages with actionable suggestions
- [ ] Implement search functionality
  - Global search across trainings and exercises
  - Quick create shortcuts
- [ ] Add tooltips and help text
  - Explain complex fields
  - Provide examples where helpful
- [ ] Improve table layouts
  - Optimize column visibility
  - Add useful default filters
  - Improve mobile table experience (basic)
- [ ] Test: Review UX as each role, gather feedback

**Deliverable**: Polished, intuitive user interface

---

## Milestone 14: Performance Optimization

**Goal**: Ensure application performs well with realistic data volumes.

### Tasks

- [ ] Optimize database queries
  - Eager load relationships to prevent N+1 queries
  - Add database indexes for frequent queries
  - Use select() to limit loaded columns where appropriate
- [ ] Implement caching
  - Cache team settings
  - Cache exercise lists for trainings
  - Cache user roles per team
  - Set appropriate cache TTLs
- [ ] Optimize calendar queries
  - Load only trainings in visible date range
  - Paginate or limit large result sets
  - Use database aggregations for counts
- [ ] Configure Redis caching
  - Session storage in Redis
  - Cache driver set to Redis
  - Queue driver set to Redis (if using queues)
- [ ] Add query monitoring
  - Enable query logging in development
  - Identify slow queries
  - Add indexes as needed
- [ ] Test performance with realistic data
  - 1000+ trainings
  - 100+ exercises
  - 50+ users per team
  - Monitor query counts and execution time

**Deliverable**: Optimized application meeting performance requirements

---

## Milestone 15: Testing & Quality Assurance

**Goal**: Ensure application stability and correctness.

### Tasks

- [ ] Write model tests
  - Test relationships
  - Test scopes
  - Test custom methods
- [ ] Write feature tests
  - Test training creation and scheduling
  - Test user assignment and roles
  - Test policies and authorization
  - Test bulk operations (duplication)
- [ ] Write Filament resource tests
  - Test CRUD operations for each resource
  - Test filters and search
  - Test actions
- [ ] Manual testing checklist
  - Test as Admin: all functionality
  - Test as Coach: training creation, client management
  - Test as Client: view trainings, complete trainings
  - Test team switching
  - Test data isolation between teams
- [ ] Browser testing
  - Chrome, Firefox, Safari, Edge
  - Test responsive views (desktop, tablet)
- [ ] Create test data scenarios
  - Fresh install seeder
  - Realistic data seeder (100s of trainings)
- [ ] Fix identified bugs and issues

**Deliverable**: Stable application with test coverage

---

## Milestone 16: Documentation & Deployment Prep

**Goal**: Prepare for deployment and document the application.

### Tasks

- [ ] Create `.env.example` with all required variables
- [ ] Document Docker setup in `README.md`
  - Prerequisites
  - Installation steps
  - Running the application
  - Accessing services (PHPMyAdmin, Mailpit)
- [ ] Document key application features
  - User roles and permissions
  - Training workflow
  - Exercise library usage
  - Calendar usage
- [ ] Create `CLAUDE.md` with context for Claude Code
  - Project structure
  - Key conventions
  - Common tasks and commands
  - File organization
- [ ] Create deployment guide (future use)
  - Production environment requirements
  - Environment variables
  - Database migrations
  - Backup strategy
- [ ] Code cleanup
  - Remove debug code
  - Remove commented-out code
  - Ensure consistent formatting (PSR-12)
  - Add docblocks to complex methods

**Deliverable**: Documented, deployment-ready application

---

## Implementation Notes

### Development Workflow

1. **Start each milestone by:**
   - Reading relevant Laravel/Filament documentation
   - Understanding the requirements
   - Planning the approach

2. **During implementation:**
   - Write migrations first, then models
   - Create seeders for testing
   - Build Filament resources with basic fields first
   - Add relationships and complex logic incrementally
   - Test as you go

3. **After each milestone:**
   - Run migrations: `php artisan migrate:fresh --seed`
   - Test in browser as each role
   - Verify data isolation
   - Commit changes with descriptive message

### Key Technical Decisions

- **Multi-Tenancy**: Use Filament's built-in tenancy feature with Team model
- **Calendar**: Evaluate available packages before implementing (Milestone 8)
- **Rich Text Editor**: Use Filament's default (TipTap/RichEditor field)
- **Testing Strategy**: Focus on feature tests and manual testing for Phase 1
- **Caching**: Use Redis for sessions and cache, add strategic caching after basic functionality works

### Common Commands

```bash
# Run migrations with seeding
docker-compose exec php php artisan migrate:fresh --seed

# Create migration
docker-compose exec php php artisan make:migration create_trainings_table

# Create model with migration
docker-compose exec php php artisan make:model Training -m

# Create Filament resource
docker-compose exec php php artisan make:filament-resource Training --generate

# Create policy
docker-compose exec php php artisan make:policy TrainingPolicy --model=Training

# Run tests
docker-compose exec php php artisan test

# Clear cache
docker-compose exec php php artisan cache:clear
docker-compose exec php php artisan config:clear
```

---

## Success Criteria

**Phase 1 is complete when:**

- ✅ All 16 milestones delivered
- ✅ You can create a training with exercises
- ✅ You can duplicate that training to 20+ future dates in under 10 seconds
- ✅ Calendar displays all scheduled trainings correctly
- ✅ Client can view assigned trainings and mark as complete
- ✅ Coach can see client feedback
- ✅ All role-based permissions work correctly
- ✅ Application performs well with 500+ trainings
- ✅ No critical bugs
- ✅ Ready for personal use (your weightlifting training schedule)

---

## Estimated Milestone Duration

| Milestone | Complexity | Estimated Effort |
|-----------|------------|------------------|
| 0: Environment Setup | Low | 1-2 sessions |
| 1: Laravel Foundation | Low | 1 session |
| 2: Database Schema | Medium | 2-3 sessions |
| 3: Team Multi-Tenancy | Medium | 2 sessions |
| 4: User Management | Medium | 2 sessions |
| 5: Exercise Library | Medium | 2 sessions |
| 6: Training Management | High | 3-4 sessions |
| 7: Scheduling & Duplication | High | 3 sessions |
| 8: Calendar View | High | 3-4 sessions |
| 9: Client Workflow | Medium | 2 sessions |
| 10: Dashboard | Low | 1-2 sessions |
| 11: Policies | Medium | 2 sessions |
| 12: Validation | Low | 1-2 sessions |
| 13: UI Polish | Medium | 2 sessions |
| 14: Performance | Medium | 2 sessions |
| 15: Testing | Medium | 2-3 sessions |
| 16: Documentation | Low | 1-2 sessions |

**Total estimated effort**: 32-42 work sessions (assuming 2-4 hours per session)

---

# Low Priority Enhancement Milestones

These milestones are suggested improvements that can be implemented after core functionality is complete. They are not required for MVP but would enhance the user experience.

---


## Milestone LP-2: Pre-Training Reminders

**Priority**: Low

**Goal**: Send automated reminder emails before scheduled trainings.

### Tasks

- [ ] Create `TrainingReminderNotification`
  - Email template with training details and exercises
  - Sent 24 hours before scheduled_at
- [ ] Create `SendTrainingReminders` scheduled command
  - Query trainings scheduled for tomorrow
  - Send reminders to assigned clients who haven't completed
  - Run daily via Laravel scheduler
- [ ] Register command in `routes/console.php`
- [ ] Test: Schedule training for tomorrow, verify reminder sent

**Deliverable**: Automated 24-hour reminder emails using Horizon queue

---

## Milestone LP-3: Training Conflict Detection

**Priority**: Low

**Goal**: Warn coaches when scheduling overlapping trainings for the same client.

### Tasks

- [ ] Create `TrainingConflictService`
  - Method: `checkConflicts(array $userIds, DateTime $scheduledAt)`
  - Return existing trainings within ±2 hours of scheduled time
- [ ] Integrate into ScheduleTrainingAction
  - Show warning notification if conflicts detected
  - Allow coach to proceed anyway (soft warning, not blocking)
- [ ] Add conflict indicator on trainings table (optional)
- [ ] Test: Schedule overlapping trainings, verify warning shown

**Deliverable**: Conflict detection warnings when scheduling overlapping trainings

---

**Next Steps**: Begin with Milestone 0 (Environment Setup) and proceed sequentially through each milestone.

---
---

# PHASE 2: Performance Tracking & Advanced Features

Phase 2 begins after Phase 1 (Milestones 0-16) is complete. This phase adds workout logging, progress tracking, and advanced features.

---

## Phase 2 Overview

### Known Limitations After Phase 1

1. **No Exercise Parameters**: Cannot specify sets, reps, weight, duration, rest time
2. **No Progress Tracking**: No way to record actual performance vs planned
3. **No Body Metrics**: Cannot track weight, measurements, body fat
4. **No Workout Programs**: Cannot group trainings into multi-week programs
5. **No Personal Records**: No PR tracking for motivation
6. **No Exercise Grouping**: Cannot create supersets or circuits
7. **No Communication**: Limited to feedback field, no coach-client messaging
8. **No Goals**: Cannot set and track fitness goals

---

## Phase 2 Requirements

### Non-Technical Requirements

#### User Stories - Client
- As a client, I want to **see my prescribed sets/reps/weight** for each exercise
- As a client, I want to **log my actual performance** (sets completed, weight used)
- As a client, I want to **track my body weight** over time
- As a client, I want to **see my personal records** for motivation
- As a client, I want to **view my training history** and progress
- As a client, I want to **receive reminders** for upcoming trainings
- As a client, I want to **communicate with my coach** about trainings

#### User Stories - Coach
- As a coach, I want to **prescribe specific sets/reps/weight** per exercise per client
- As a coach, I want to **see client workout logs** to track adherence
- As a coach, I want to **create training templates** for reuse
- As a coach, I want to **build multi-week programs** with progressive overload
- As a coach, I want to **track client body metrics** over time
- As a coach, I want to **see which clients completed trainings** with their performance data
- As a coach, I want to **receive notifications** when clients complete workouts

#### User Stories - Admin
- As an admin, I want to **see team-wide statistics** (completion rates, active clients)
- As an admin, I want to **manage team settings and branding**

---

### Technical Requirements

#### Performance
- Calendar view loads < 2 seconds with 500+ trainings
- Workout log saves in < 1 second
- Progress charts render with 1 year of data

#### Data Integrity
- All performance data persisted (no data loss)
- Audit trail for training changes
- Proper soft deletes for exercises/trainings used in historical data

#### Scalability
- Support 100+ clients per team
- Support 50+ trainings per client per month
- Support 10+ exercises per training

---

## Phase 2 Implementation Priority

### Phase 2A - Performance Tracking (HIGH VALUE)
The biggest missing piece - users need to log their workouts.

| Table | Priority | Value |
|-------|----------|-------|
| workout_logs | HIGH | Core feature - log actual performance |
| personal_records | HIGH | Motivation, auto-tracked from logs |
| body_metrics | MEDIUM | Common feature in fitness apps |

### Phase 2B - Templates & Programs (MEDIUM VALUE)
Makes the app more efficient for coaches.

| Table | Priority | Value |
|-------|----------|-------|
| training_templates | MEDIUM | Speeds up training creation |
| programs | LOW | Multi-week programs (complex) |
| program_weeks | LOW | Part of programs |
| program_week_template | LOW | Part of programs |
| program_enrollments | LOW | Part of programs |

### Phase 2C - Engagement Features (LOWER VALUE)
Nice to have, not critical for MVP.

| Table | Priority | Value |
|-------|----------|-------|
| goals | LOW | Goal tracking |
| messages | LOW | Coach-client communication |

---

## Phase 2 New Enums

```php
// app/Enums/ExerciseCategory.php
enum ExerciseCategory: string {
    case Strength = 'strength';
    case Cardio = 'cardio';
    case Flexibility = 'flexibility';
    case Balance = 'balance';
    case Plyometric = 'plyometric';
    case Compound = 'compound';
    case Isolation = 'isolation';
}

// app/Enums/MuscleGroup.php
enum MuscleGroup: string {
    case Chest = 'chest';
    case Back = 'back';
    case Shoulders = 'shoulders';
    case Biceps = 'biceps';
    case Triceps = 'triceps';
    case Forearms = 'forearms';
    case Core = 'core';
    case Glutes = 'glutes';
    case Quadriceps = 'quadriceps';
    case Hamstrings = 'hamstrings';
    case Calves = 'calves';
    case FullBody = 'full_body';
}

// app/Enums/SetType.php
enum SetType: string {
    case Normal = 'normal';
    case Warmup = 'warmup';
    case Dropset = 'dropset';
    case Superset = 'superset';
    case Giant = 'giant';
    case AMRAP = 'amrap';
    case RIR = 'rir';
}

// app/Enums/WeightUnit.php
enum WeightUnit: string {
    case Kg = 'kg';
    case Lb = 'lb';
}

// app/Enums/GoalStatus.php
enum GoalStatus: string {
    case Active = 'active';
    case Achieved = 'achieved';
    case Abandoned = 'abandoned';
}
```

---

## Phase 2 Database Schema

### Enhanced Tables (migrations to modify existing)

#### Enhance `training_exercise` pivot
```php
// Add prescribed parameters
$table->unsignedTinyInteger('prescribed_sets')->nullable();
$table->unsignedTinyInteger('prescribed_reps')->nullable();
$table->decimal('prescribed_weight', 8, 2)->nullable();
$table->unsignedSmallInteger('prescribed_duration_seconds')->nullable();
$table->unsignedSmallInteger('prescribed_rest_seconds')->nullable();
$table->string('set_type')->default(SetType::Normal->value);
$table->unsignedTinyInteger('superset_group')->nullable();
```

#### Enhance `training_user` pivot
```php
// Add completion tracking
$table->timestamp('started_at')->nullable();
$table->unsignedSmallInteger('duration_minutes')->nullable();
$table->unsignedTinyInteger('perceived_effort')->nullable(); // RPE 1-10
$table->text('coach_notes')->nullable();
```

### New Tables

#### `workout_logs` - Performance Tracking
```php
Schema::create('workout_logs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('training_user_id')->constrained('training_user')->cascadeOnDelete();
    $table->foreignId('training_exercise_id')->constrained('training_exercise')->cascadeOnDelete();
    $table->unsignedTinyInteger('set_number');
    $table->unsignedTinyInteger('reps_completed')->nullable();
    $table->decimal('weight_used', 8, 2)->nullable();
    $table->unsignedSmallInteger('duration_seconds')->nullable();
    $table->unsignedSmallInteger('rest_taken_seconds')->nullable();
    $table->string('set_type')->default(SetType::Normal->value);
    $table->boolean('completed')->default(true);
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index(['training_user_id', 'training_exercise_id']);
});
```

#### `personal_records` - PR Tracking
```php
Schema::create('personal_records', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('exercise_id')->constrained()->cascadeOnDelete();
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->string('record_type'); // '1rm', '5rm', 'max_reps', 'max_duration'
    $table->decimal('value', 10, 2);
    $table->date('achieved_at');
    $table->foreignId('workout_log_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamps();

    $table->unique(['user_id', 'exercise_id', 'record_type']);
    $table->index(['user_id', 'team_id']);
});
```

#### `body_metrics` - Body Tracking
```php
Schema::create('body_metrics', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->date('measured_at');
    $table->decimal('weight', 5, 2)->nullable();
    $table->decimal('body_fat_percentage', 4, 1)->nullable();
    $table->decimal('chest_cm', 5, 1)->nullable();
    $table->decimal('waist_cm', 5, 1)->nullable();
    $table->decimal('hips_cm', 5, 1)->nullable();
    $table->decimal('bicep_left_cm', 5, 1)->nullable();
    $table->decimal('bicep_right_cm', 5, 1)->nullable();
    $table->decimal('thigh_left_cm', 5, 1)->nullable();
    $table->decimal('thigh_right_cm', 5, 1)->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->index(['user_id', 'team_id', 'measured_at']);
});
```

#### `training_templates` - Reusable Templates
```php
Schema::create('training_templates', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('category')->nullable();
    $table->json('exercises'); // Snapshot of exercises with parameters
    $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
    $table->timestamps();
    $table->softDeletes();

    $table->index(['team_id', 'category']);
});
```

#### `programs` - Multi-Week Programs
```php
Schema::create('programs', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->unsignedTinyInteger('duration_weeks');
    $table->unsignedTinyInteger('sessions_per_week');
    $table->string('difficulty')->nullable();
    $table->json('settings')->nullable();
    $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
    $table->boolean('is_published')->default(false);
    $table->timestamps();
    $table->softDeletes();

    $table->index(['team_id', 'is_published']);
});
```

#### `program_weeks` - Program Structure
```php
Schema::create('program_weeks', function (Blueprint $table) {
    $table->id();
    $table->foreignId('program_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('week_number');
    $table->string('name')->nullable();
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->unique(['program_id', 'week_number']);
});
```

#### `program_week_template` - Templates per Week
```php
Schema::create('program_week_template', function (Blueprint $table) {
    $table->id();
    $table->foreignId('program_week_id')->constrained()->cascadeOnDelete();
    $table->foreignId('training_template_id')->constrained()->cascadeOnDelete();
    $table->unsignedTinyInteger('day_of_week');
    $table->unsignedTinyInteger('sort_order')->default(0);
    $table->timestamps();

    $table->index(['program_week_id', 'day_of_week']);
});
```

#### `program_enrollments` - Client Enrollments
```php
Schema::create('program_enrollments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('program_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->date('start_date');
    $table->date('end_date')->nullable();
    $table->unsignedTinyInteger('current_week')->default(1);
    $table->string('status')->default('active');
    $table->timestamps();

    $table->unique(['program_id', 'user_id', 'start_date']);
    $table->index(['user_id', 'status']);
});
```

#### `goals` - Fitness Goals
```php
Schema::create('goals', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->string('title');
    $table->text('description')->nullable();
    $table->string('goal_type');
    $table->decimal('target_value', 10, 2)->nullable();
    $table->decimal('starting_value', 10, 2)->nullable();
    $table->decimal('current_value', 10, 2)->nullable();
    $table->string('unit')->nullable();
    $table->date('target_date')->nullable();
    $table->string('status')->default(GoalStatus::Active->value);
    $table->timestamp('achieved_at')->nullable();
    $table->foreignId('exercise_id')->nullable()->constrained()->nullOnDelete();
    $table->timestamps();

    $table->index(['user_id', 'team_id', 'status']);
});
```

#### `messages` - Coach-Client Communication
```php
Schema::create('messages', function (Blueprint $table) {
    $table->id();
    $table->foreignId('team_id')->constrained()->cascadeOnDelete();
    $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('training_id')->nullable()->constrained()->nullOnDelete();
    $table->text('content');
    $table->timestamp('read_at')->nullable();
    $table->timestamps();

    $table->index(['team_id', 'recipient_id', 'read_at']);
    $table->index(['training_id']);
});
```

---

## Phase 2 Key Decisions

Before starting Phase 2, decide:

1. **Weight Units**: Support both kg and lb? Per-team setting?
2. **Exercise Categories**: Use predefined enum or freeform tags?
3. **Workout Logging UI**: Log during workout or after? Mobile-first?
4. **PR Notifications**: Auto-detect and celebrate PRs?

---

## Phase 2 Sample Data Flows

### Client Completes Workout
```
1. Client opens assigned training (training_user record exists)
2. Client sees exercises with prescribed sets/reps/weight (training_exercise)
3. For each exercise, client logs actual performance (workout_logs)
   - Set 1: 10 reps @ 50kg
   - Set 2: 8 reps @ 55kg
   - Set 3: 6 reps @ 60kg (PR!)
4. System auto-checks for PR (personal_records)
5. Client marks training complete with feedback (training_user.completed_at, feedback)
6. Coach sees completion and can add coach_notes
```

### Coach Creates Training with Parameters
```
1. Coach creates new training
2. Coach adds exercises with prescribed parameters:
   - Bench Press: 3 sets x 8 reps @ 70% 1RM, 90s rest
   - Rows: 3 sets x 10 reps, 60s rest
   - Shoulder Press: 3 sets x 12 reps, superset with...
   - Lateral Raises: 3 sets x 15 reps (same superset_group)
3. Coach assigns to clients
4. System schedules for selected date
```
