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

- [ ] Create Laravel 12 project
  - `composer create-project laravel/laravel .`
- [ ] Configure `.env` for Docker services
  - DB_HOST=mysql, DB_DATABASE=workouts
  - CACHE_DRIVER=redis, REDIS_HOST=redis
  - MAIL_HOST=mailpit, MAIL_PORT=1025
- [ ] Install Filament 4
  - `composer require filament/filament`
  - `php artisan filament:install --panels`
- [ ] Run initial migrations
  - `php artisan migrate`
- [ ] Create admin user
  - `php artisan make:filament-user`
- [ ] Test: Access Filament panel at http://localhost/admin

**Deliverable**: Laravel + Filament admin panel accessible and functional

---

## Milestone 2: Database Schema & Models

**Goal**: Create complete database structure with Eloquent models and relationships.

### 2.1 Core Tables Setup

#### Migration: create_teams_table
```php
- id
- name (string, unique)
- slug (string, unique)
- settings (json, nullable)
- timestamps
```

#### Migration: add_team_fields_to_users_table
```php
- Modify existing users table (no changes needed initially)
```

#### Migration: create_team_user_table
```php
- id
- team_id (foreign -> teams)
- user_id (foreign -> users)
- role (enum: admin, coach, client)
- timestamps
- unique(team_id, user_id)
- indexes on team_id, user_id
```

#### Migration: create_exercises_table
```php
- id
- team_id (foreign -> teams)
- name (string)
- description (text, nullable)
- video_urls (json, nullable)
- tags (json, nullable)
- created_by (foreign -> users)
- timestamps, softDeletes
- index on team_id, created_by
```

#### Migration: create_trainings_table
```php
- id
- team_id (foreign -> teams)
- title (string)
- content (longText, nullable)
- status (enum: draft, scheduled, completed, skipped) default draft
- scheduled_date (date, nullable)
- created_by (foreign -> users)
- timestamps, softDeletes
- indexes on team_id, created_by, scheduled_date, status
```

#### Migration: create_training_exercise_table
```php
- id
- training_id (foreign -> trainings)
- exercise_id (foreign -> exercises)
- notes (text, nullable)
- sort_order (integer, default 0)
- timestamps
- indexes on training_id, exercise_id
```

#### Migration: create_training_user_table
```php
- id
- training_id (foreign -> trainings)
- user_id (foreign -> users)
- completed_at (timestamp, nullable)
- feedback (text, nullable)
- timestamps
- unique(training_id, user_id)
- indexes on training_id, user_id
```

### 2.2 Eloquent Models

#### Models to Create
- [ ] `Team` model
  - Relationships: users (belongsToMany), exercises (hasMany), trainings (hasMany)
  - Casts: settings as array
- [ ] `Exercise` model
  - Relationships: team (belongsTo), creator (belongsTo User), trainings (belongsToMany)
  - Casts: video_urls as array, tags as array
  - Scopes: forTeam($teamId)
  - Soft deletes
- [ ] `Training` model
  - Relationships: team (belongsTo), creator (belongsTo User), exercises (belongsToMany), assignedUsers (belongsToMany through training_user)
  - Casts: status as enum
  - Scopes: forTeam($teamId), scheduled(), draft(), completed()
  - Soft deletes
- [ ] Update `User` model
  - Relationships: teams (belongsToMany), createdExercises (hasMany), createdTrainings (hasMany), assignedTrainings (belongsToMany through training_user)
  - Methods: hasRole($teamId, $role), isAdmin($teamId), isCoach($teamId), isClient($teamId)

### 2.3 Enums

- [ ] Create `app/Enums/TeamRole.php`
  - Admin, Coach, Client
- [ ] Create `app/Enums/TrainingStatus.php`
  - Draft, Scheduled, Completed, Skipped

### 2.4 Database Seeder

- [ ] Create `TeamSeeder`
  - Create 2 demo teams
- [ ] Create `UserSeeder`
  - Create admin user for each team
  - Create 2 coaches and 4 clients for Team 1
- [ ] Create `ExerciseSeeder`
  - Create 10-15 sample exercises per team
  - Include video URLs and tags
- [ ] Create `TrainingSeeder`
  - Create 5 draft trainings
  - Create 10 scheduled trainings (various dates)
  - Assign exercises to trainings
  - Assign trainings to clients
- [ ] Update `DatabaseSeeder` to call all seeders

**Deliverable**: Complete database schema with seedable test data

---

## Milestone 3: Team Management & Multi-Tenancy

**Goal**: Implement Filament multi-tenancy with team switching and scoped data.

### Tasks

- [ ] Configure Filament panel for tenancy
  - Register `Team` as tenant model
  - Configure tenant relationship on User model
- [ ] Create middleware for team context
  - Ensure all queries are scoped to current team
- [ ] Create `TeamResource` in Filament
  - Fields: name, slug
  - Only accessible to system admins (future feature)
- [ ] Create team switcher component
  - Use Filament's tenant menu
- [ ] Test: Switch between teams, verify data isolation

**Deliverable**: Working multi-tenancy with team switching

---

## Milestone 4: User Management

**Goal**: Manage users within teams with role assignment.

### Tasks

- [ ] Create `UserResource` in Filament
  - Table columns: name, email, role (in current team), created_at
  - Form fields: name, email, password, role (for current team)
  - Filters: by role
  - Actions: edit, delete (remove from team)
  - Bulk actions: change role
  - Policies: Only admins and coaches can access
- [ ] Create custom "Add User to Team" action
  - Check if email exists in system
  - If yes: attach existing user to current team with role
  - If no: create new user and attach to team
  - Validate unique email per team
- [ ] Create policies for UserResource
  - `viewAny`: admin, coach
  - `view`, `create`, `update`: admin only
  - `delete`: admin only (removes from team, doesn't delete user)
- [ ] Test: Add existing user to multiple teams, verify role isolation

**Deliverable**: Full user management within team context

---

## Milestone 5: Exercise Library

**Goal**: Create and manage exercise library with videos and tags.

### Tasks

- [ ] Create `ExerciseResource` in Filament
  - Table columns: name, tags, created_by, created_at
  - Table filters: by tags, by creator
  - Form fields:
    - name (required, text input)
    - description (rich text editor)
    - video_urls (repeater with URL inputs)
    - tags (tag input or select)
  - Actions: edit, delete, duplicate
  - Bulk actions: add tag, delete
- [ ] Implement global search for exercises
  - Search by name and tags
- [ ] Create policies for ExerciseResource
  - `viewAny`, `view`: all roles
  - `create`, `update`: coach, admin
  - `delete`: creator or admin
- [ ] Ensure exercises are team-scoped
  - Add global scope to Exercise model
  - Set team_id automatically on creation
- [ ] Test: Create exercise with multiple videos and tags

**Deliverable**: Functional exercise library with full CRUD

---

## Milestone 6: Training Management

**Goal**: Create and manage trainings with exercise attachments.

### Tasks

- [ ] Create `TrainingResource` in Filament
  - Table columns: title, status, scheduled_date, assigned_to (count), created_at
  - Table filters: by status, by date range, by assigned user
  - Form fields:
    - title (required, text input)
    - content (rich text editor)
    - status (select)
    - scheduled_date (date picker, nullable)
  - Relation manager: exercises (attach/detach with pivot notes and sort_order)
  - Relation manager: assignedUsers (clients only)
  - Actions: edit, delete, duplicate, schedule (custom)
  - Bulk actions: change status, delete
- [ ] Create `ExerciseRelationManager` for trainings
  - Attach form: select exercise, add notes, set sort_order
  - Table: exercise name, notes, sort_order
  - Actions: edit notes, detach, reorder
- [ ] Create `AssignedUsersRelationManager` for trainings
  - Attach form: select client(s) from current team
  - Table: name, assigned_at, completed_at, feedback
  - Filters: completed/pending
  - View client feedback
- [ ] Create policies for TrainingResource
  - `viewAny`: all roles (filtered by role)
  - `view`: creator, assigned clients, admins
  - `create`, `update`: coach, admin
  - `delete`: creator or admin
- [ ] Implement view filters based on role
  - Clients: only see assigned trainings
  - Coaches: see all trainings or filter by client
  - Admins: see all trainings
- [ ] Test: Create training with exercises, assign to clients

**Deliverable**: Full training CRUD with exercise and client assignment

---

## Milestone 7: Training Scheduling & Duplication

**Goal**: Implement single and bulk training scheduling.

### Tasks

- [ ] Create `ScheduleTrainingAction` (Filament action)
  - Form: scheduled_date, assign_to (select clients)
  - Logic: update training status to 'scheduled', set date, assign to clients
- [ ] Create `DuplicateTrainingAction` (Filament action)
  - Form:
    - Target dates (date picker with multiple selection OR pattern selector)
    - Assign to (select clients, default to original clients)
    - Copy exercises (checkbox, default true)
  - Logic:
    - Create training copies for each selected date
    - Copy exercises with notes if enabled
    - Assign to selected clients
    - Set status to 'scheduled'
    - Use database transaction
  - Notification: Show count of created trainings
- [ ] Create `BulkScheduleService` (app/Services/)
  - Method: `duplicateToMultipleDates(Training $training, array $dates, array $userIds)`
  - Validate dates and users
  - Create trainings in chunks for performance
  - Return count of created trainings
- [ ] Add "Quick Schedule" button to TrainingResource table
  - Opens modal with date picker
  - Quick schedule to single date
- [ ] Test: Duplicate training to 20 dates, verify all created correctly

**Deliverable**: Single and bulk training scheduling functionality

---

## Milestone 8: Calendar View

**Goal**: Display trainings in a calendar interface with filtering.

### Tasks

- [ ] Choose calendar package
  - Option 1: FullCalendar.js integration with Livewire
  - Option 2: Filament calendar plugin (if available)
  - Option 3: Custom Livewire component with simple month view
- [ ] Create `CalendarPage` (Filament custom page)
  - Register in panel navigation
  - Access: all roles
- [ ] Create `TrainingCalendar` Livewire component
  - Display trainings on calendar by scheduled_date
  - Color code by status (draft=gray, scheduled=blue, completed=green, skipped=orange)
  - Click training to open slide-over with details
  - Filter by:
    - Client (for coaches/admins)
    - Status
    - Date range
- [ ] Implement data loading
  - Load trainings for visible date range only
  - Apply role-based filtering (clients see only assigned)
  - Eager load relationships to reduce queries
- [ ] Add actions on calendar events
  - Quick complete (for clients)
  - Quick view details
  - Edit (navigate to TrainingResource)
- [ ] Test: Load calendar with 100+ trainings, verify performance

**Deliverable**: Functional calendar view with role-based filtering

---

## Milestone 9: Client Training Workflow

**Goal**: Enable clients to view and complete trainings with feedback.

### Tasks

- [ ] Create `MyTrainingsPage` (Filament custom page)
  - List view of assigned trainings
  - Filters: status, date range
  - Sort by scheduled_date
  - Actions: view details, mark complete
- [ ] Create `CompleteTrainingAction`
  - Form: feedback (textarea, optional), completed_at (auto-set to now)
  - Update training status to 'completed'
  - Update pivot record (training_user) with completion data
  - Notification: "Training marked as complete"
- [ ] Create `TrainingDetailPage` or modal
  - Display: title, content, exercises with notes and videos
  - Show completion status and feedback if completed
  - Action button: Mark as Complete (if not completed)
- [ ] Modify TrainingResource view for clients
  - Read-only access
  - Show assigned trainings only
  - Prominent "Complete Training" button
- [ ] Create notification for coaches when client completes training
  - Optional: can be added later
- [ ] Test: Client completes training, coach sees feedback

**Deliverable**: Complete client workflow for viewing and completing trainings

---

## Milestone 10: Dashboard & Overview

**Goal**: Create dashboard with relevant information for each role.

### Tasks

- [ ] Create dashboard widgets for Clients
  - Upcoming trainings (next 7 days)
  - Training completion stats (this week/month)
  - Recent feedback submitted
- [ ] Create dashboard widgets for Coaches
  - Assigned trainings overview (pending/completed)
  - Recent client completions with feedback
  - Clients with overdue trainings
- [ ] Create dashboard widgets for Admins
  - Team statistics (users, trainings, exercises)
  - Activity feed (recent trainings created/completed)
- [ ] Configure Filament dashboard
  - Role-based widget display
  - Refresh data with Livewire polling (optional)
- [ ] Test: View dashboard as each role, verify relevant data

**Deliverable**: Role-specific dashboards with useful widgets

---

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

**Next Steps**: Begin with Milestone 0 (Environment Setup) and proceed sequentially through each milestone.
