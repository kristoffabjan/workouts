# Workouts App - Project Specification

## 1. Project Overview

The Workouts App is a comprehensive training and workout management system designed for coaches, trainers, and athletes. The application enables coaches to create, schedule, and manage training programs for their clients, while clients can view their scheduled workouts, complete them, and provide feedback on their training sessions.

Built as a team-based platform, the application supports multi-tenancy where users can participate in multiple teams with different roles, creating a flexible environment for various training scenarios—from personal workout tracking to professional coaching relationships.

### 1.1 Project Vision

Create a solid, modular foundation for a workout management system that:
- Simplifies the creation and scheduling of training programs
- Enables efficient coach-client communication through structured training plans
- Provides clear visibility of training schedules through calendar views
- Scales from individual use to team-based coaching scenarios
- Allows future enhancement with AI-powered features and advanced planning tools

### 1.2 Primary Use Case (Phase 1)

The initial implementation focuses on enabling a coach or individual athlete to:
1. Create training sessions with rich text content
2. Build an exercise library with reference materials (videos, notes)
3. Schedule trainings on specific dates
4. Duplicate trainings to multiple future dates (e.g., "Monday training for the next 6 months")
5. View all scheduled trainings in a calendar interface
6. Track training completion and optional client feedback

---

## 2. User Roles & Access Control

### 2.1 Role Definitions

#### Admin
- Full system access within a team
- User management (add/remove users, assign roles)
- Team configuration and settings
- Access to all training data within the team

#### Coach
- Create and manage training programs
- Create and manage exercises in the library
- Assign trainings to clients
- View all client trainings and feedback
- Duplicate and schedule trainings
- Cannot manage team settings or other users

#### Client/Athlete
- View assigned trainings
- Mark trainings as completed
- Provide post-training feedback/feelings
- View personal training calendar
- Cannot create or modify training programs
- Cannot access other clients' data
- Is able to interact in app as individual, so outside of team.


### 2.2 Multi-Team Support

- Users can belong to multiple teams with different roles in each
- Users switch between teams via Filament panel selector
- Email addresses are unique per user (not per team)
- Role-based permissions are enforced at the team level using Laravel policies
- All data (trainings, exercises, schedules) are team-scoped

### 2.3 Onboarding new Users
-  Admin or team admin can invitine user in app into team, or to certain team as team admin, which will create user that belong to team, or individual user. New user would receive email with change password invitation.

---

## 3. Functional Requirements

### 3.1 User Management

#### FR-1.1: User Authentication
- Users authenticate via Filament's built-in authentication system
- Email and password-based login
- Session management with remember me functionality

#### FR-1.2: Team Management
- Coaches and admins can add users to teams directly by email
- If email exists in system, user is added to the team with specified role
- If email is new, create new user account and add to team
- Users can be assigned roles: Admin, Coach, or Client
- Admins can modify user roles within their team

#### FR-1.3: Team Switching
- Users with multiple team memberships can switch between teams
- Team switch uses Filament's panel selection mechanism
- Current team context persists throughout session

### 3.2 Exercise Library

#### FR-2.1: Exercise Management
- Coaches and admins can create exercises
- Exercise attributes:
  - Name (required)
  - Description (rich text)
  - Video URL(s) (optional, multiple)
  - Tags/categories (optional)
  - Team-scoped (exercises belong to a team)

#### FR-2.2: Exercise Reusability
- Exercises can be attached to multiple trainings
- Exercise modifications in library don't affect past trainings
- Coaches can add training-specific notes when attaching exercises

### 3.3 Training Management

#### FR-3.1: Training Creation
- Coaches create trainings with:
  - Title (required)
  - Description/content (rich text)
  - Status: Draft, Scheduled, Completed, Skipped
  - Created date and modified date
  - Optional exercises from library with custom notes

#### FR-3.2: Training Assignment
- Coaches assign trainings to specific clients
- One training can be assigned to multiple clients
- Clients only see trainings assigned to them

#### FR-3.3: Training Scheduling
- Single date scheduling: Assign training to specific date
- Bulk scheduling: Duplicate training to multiple dates
  - Select target dates manually or by pattern (e.g., "every Monday")
  - Each scheduled instance is independent (can be modified separately)
  - Scheduled trainings default to "Scheduled" status

#### FR-3.4: Training Status Workflow
- **Draft**: Training created but not yet scheduled/assigned
- **Scheduled**: Training assigned to client(s) and scheduled for future date
- **Completed**: Client marked training as done
- **Skipped**: Training was scheduled but not completed

#### FR-3.5: Training Completion
- Clients can mark trainings as completed
- Optional feedback field for clients to describe how training felt
- Completion timestamp recorded
- Coaches can view completion status and feedback

### 3.4 Calendar & Planning

#### FR-4.1: Calendar View
- Display scheduled trainings in calendar interface
- Filter views:
  - Coaches: See all team trainings or filter by specific client
  - Clients: See only their assigned trainings
  - Admins: See all team trainings

#### FR-4.2: Calendar Features
- Month, week, and day views
- Color coding by status
- Click training to view details
- Navigate between past and future dates

#### FR-4.3: Training Duplication
- Select existing training to duplicate
- Choose target dates (single or multiple)
- Choose target clients (single or multiple)
- Bulk operations to optimize schedule creation
- Example: "Create this Monday workout for every Monday in next 6 months"

### 3.5 Team Collaboration

#### FR-5.1: Coach-Client Workflow
- Coaches create and schedule trainings for clients
- Clients receive trainings in their calendar
- Clients complete trainings and optionally provide feedback
- Coaches monitor client progress and feedback

#### FR-5.2: Data Visibility
- Clients only see their own trainings
- Coaches see all trainings for their clients
- Admins see all team data
- Enforced via Laravel policies and Filament authorization

---

## 4. Non-Functional Requirements

### 4.1 Performance

- **NFR-1.1**: Page load times under 2 seconds for typical operations
- **NFR-1.2**: Calendar view renders 3 months of data in under 1 second
- **NFR-1.3**: Bulk training duplication (up to 50 instances) completes in under 5 seconds

### 4.2 Scalability

- **NFR-2.1**: Support up to 100 teams initially
- **NFR-2.2**: Each team supports up to 50 active users
- **NFR-2.3**: System handles 10,000+ scheduled trainings without performance degradation
- **NFR-2.4**: Database design supports horizontal scaling for future growth

### 4.3 Usability

- **NFR-3.1**: Interface optimized for desktop and tablet (mobile optimization in future phase)
- **NFR-3.2**: Filament-based UI ensures consistent, professional design
- **NFR-3.3**: Rich text editor provides intuitive formatting options
- **NFR-3.4**: Calendar interface provides clear visual distinction between training statuses

### 4.4 Reliability

- **NFR-4.1**: 99% uptime during business hours
- **NFR-4.2**: Data consistency ensured through database transactions
- **NFR-4.3**: Graceful error handling with user-friendly messages
- **NFR-4.4**: Database backups configured (responsibility of deployment environment)

### 4.5 Security

- **NFR-5.1**: All routes protected by authentication middleware
- **NFR-5.2**: Authorization enforced via Laravel policies
- **NFR-5.3**: Team data isolation—users cannot access data from teams they don't belong to
- **NFR-5.4**: Password hashing using Laravel's default (bcrypt)
- **NFR-5.5**: CSRF protection enabled on all forms
- **NFR-5.6**: SQL injection prevention via Eloquent ORM

### 4.6 Maintainability

- **NFR-6.1**: Modular code structure to support future feature additions
- **NFR-6.2**: Clear separation of concerns following Laravel best practices
- **NFR-6.3**: Database migrations for version-controlled schema changes
- **NFR-6.4**: Code follows PSR-12 coding standards
- **NFR-6.5**: Comprehensive comments for complex business logic

### 4.7 Data Management

- **NFR-7.1**: All timestamps stored in UTC (server timezone)
- **NFR-7.2**: No timezone conversion (single timezone for all users)
- **NFR-7.3**: Soft deletes for trainings and users to preserve historical data
- **NFR-7.4**: Audit trail for critical actions (future enhancement)

---

## 5. Technology Stack

### 5.1 Backend

- **Framework**: Laravel 12.x
- **PHP Version**: 8.5
- **Admin Panel**: Filament 4.x
- **Reactive Components**: Livewire 3.x
- **ORM**: Eloquent
- **Database**: MySQL 8.4
- **Cache**: Redis
- **Package Manager**: Composer

### 5.2 Frontend

- **UI Framework**: Filament (includes Tailwind CSS)
- **JavaScript**: Alpine.js (included with Livewire)
- **Calendar Component**: To be determined (Filament plugin or third-party package)
- **Rich Text Editor**: Filament's default or TipTap integration

### 5.3 Development Environment (Docker)

- **Web Server**: Nginx (latest stable)
- **PHP**: PHP 8.5-FPM with Composer
- **Database**: MySQL 8.4
- **Cache**: Redis (latest)
- **Database Admin**: PHPMyAdmin (latest)
- **Mail Testing**: Mailpit (latest)
- **Orchestration**: Docker Compose

### 5.4 Additional Packages

- **filament/filament**: Admin panel framework
- **filament/forms**: Form builder
- **filament/tables**: Table builder
- **filament/notifications**: Notification system
- Calendar package (TBD based on Filament 4 compatibility)

---

## 6. System Architecture

### 6.1 Application Architecture

```
┌─────────────────────────────────────────────────────┐
│                   Browser (Client)                   │
└────────────────────┬────────────────────────────────┘
                     │ HTTPS
┌────────────────────▼────────────────────────────────┐
│                  Nginx (Reverse Proxy)               │
└────────────────────┬────────────────────────────────┘
                     │
┌────────────────────▼────────────────────────────────┐
│           Laravel Application (PHP-FPM)              │
│  ┌──────────────────────────────────────────────┐   │
│  │         Filament Admin Panel                 │   │
│  │  ┌────────────────────────────────────────┐  │   │
│  │  │  Resources: Users, Trainings, Exercises│  │   │
│  │  └────────────────────────────────────────┘  │   │
│  │  ┌────────────────────────────────────────┐  │   │
│  │  │  Livewire Components: Calendar, Forms  │  │   │
│  │  └────────────────────────────────────────┘  │   │
│  └──────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────┐   │
│  │          Laravel Core Services               │   │
│  │  - Authentication (Filament Auth)            │   │
│  │  - Authorization (Policies)                  │   │
│  │  - Eloquent Models & Relations               │   │
│  │  - Service Layer (Business Logic)            │   │
│  └──────────────────────────────────────────────┘   │
└────────────┬────────────────────┬────────────────────┘
             │                    │
┌────────────▼────────────┐  ┌───▼──────────────┐
│   MySQL 8.4             │  │   Redis          │
│   - Application Data    │  │   - Cache        │
│   - Transactional Store │  │   - Sessions     │
└─────────────────────────┘  └──────────────────┘
```

### 6.2 Deployment Structure (Docker)

```
workouts/
├── .docker/
│   ├── nginx/
│   │   └── default.conf
│   ├── php/
│   │   └── Dockerfile
│   └── mysql/
│       └── init.sql (optional)
├── docker-compose.yml
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
├── resources/
├── routes/
├── storage/
└── vendor/
```

### 6.3 Data Flow

#### Training Creation Flow
1. Coach accesses Filament Training resource
2. Fills form (title, description, exercises)
3. Form submitted via Livewire
4. Laravel validates and stores via Eloquent
5. Training saved to MySQL
6. Success notification displayed

#### Training Scheduling Flow
1. Coach selects training and target dates
2. Custom Livewire component handles bulk duplication
3. Service layer creates training instances
4. Database transaction ensures atomicity
5. Calendar cache invalidated (Redis)
6. Success notification with count of created trainings

#### Client Training View Flow
1. Client accesses calendar view
2. Livewire component queries trainings via Eloquent
3. Policy filters trainings (only assigned to client)
4. Training data formatted for calendar display
5. Calendar rendered with Livewire reactivity
6. Client clicks training to view details

---

## 7. Database Schema (High-Level)

### 7.1 Core Tables

#### teams
- id
- name
- slug
- settings (JSON)
- created_at, updated_at

#### users
- id
- name
- email (unique)
- password
- email_verified_at
- created_at, updated_at

#### team_user (pivot)
- id
- team_id (FK: teams)
- user_id (FK: users)
- role (enum: admin, coach, client)
- created_at, updated_at

#### exercises
- id
- team_id (FK: teams)
- name
- description (text)
- video_urls (JSON)
- tags (JSON)
- created_by (FK: users)
- created_at, updated_at, deleted_at

#### trainings
- id
- team_id (FK: teams)
- title
- content (text)
- status (enum: draft, scheduled, completed, skipped)
- scheduled_date (date, nullable)
- created_by (FK: users)
- created_at, updated_at, deleted_at

#### training_exercise (pivot)
- id
- training_id (FK: trainings)
- exercise_id (FK: exercises)
- notes (text, nullable)
- sort_order (int)

#### training_user (assignments, pivot)
- id
- training_id (FK: trainings)
- user_id (FK: users)
- completed_at (timestamp, nullable)
- feedback (text, nullable)
- created_at, updated_at

### 7.2 Indexes

- team_id on all team-scoped tables
- user_id on team_user and training_user
- training_id on training_exercise and training_user
- scheduled_date on trainings for calendar queries
- status on trainings for filtering
- email on users (unique)

### 7.3 Relationships

- Team hasMany Users (through team_user)
- User belongsToMany Teams (through team_user)
- Team hasMany Exercises
- Team hasMany Trainings
- Training belongsToMany Exercises (through training_exercise)
- Training belongsToMany Users (through training_user for assignments)
- User hasMany created Trainings
- User hasMany created Exercises

---

## 8. Docker Infrastructure

### 8.1 Services Configuration

#### Service: nginx
- **Image**: nginx:alpine
- **Port**: 80:80
- **Volume**: Application code mounted to /var/www/html
- **Config**: Custom nginx.conf for Laravel
- **Depends on**: php

#### Service: php
- **Image**: Custom Dockerfile (PHP 8.5-FPM)
- **Includes**: Composer, required PHP extensions (pdo_mysql, redis, gd, zip, etc.)
- **Volume**: Application code mounted to /var/www/html
- **Working Directory**: /var/www/html
- **Depends on**: mysql, redis

#### Service: mysql
- **Image**: mysql:8.4
- **Port**: 3306:3306
- **Environment**:
  - MYSQL_ROOT_PASSWORD
  - MYSQL_DATABASE=workouts
  - MYSQL_USER=workouts
  - MYSQL_PASSWORD
- **Volume**: Persistent volume for /var/lib/mysql
- **Health check**: mysqladmin ping

#### Service: redis
- **Image**: redis:alpine
- **Port**: 6379:6379
- **Volume**: Persistent volume for /data
- **Command**: redis-server --appendonly yes

#### Service: phpmyadmin
- **Image**: phpmyadmin:latest
- **Port**: 8080:80
- **Environment**:
  - PMA_HOST=mysql
  - PMA_USER=workouts
  - PMA_PASSWORD
- **Depends on**: mysql

#### Service: mailpit
- **Image**: axllent/mailpit:latest
- **Port**:
  - 1025:1025 (SMTP)
  - 8025:8025 (Web UI)

### 8.2 Volume Management

- **mysql_data**: Persistent MySQL database
- **redis_data**: Persistent Redis data
- **Application code**: Mounted from host for development

### 8.3 Network

- All services on shared Docker network: workouts_network
- Internal DNS resolution for service communication

---

## 9. Development Workflow

### 9.1 Initial Setup

1. Clone repository
2. Run `docker-compose up -d` to start services
3. Run `docker-compose exec php composer install`
4. Copy `.env.example` to `.env` and configure
5. Run `docker-compose exec php php artisan key:generate`
6. Run `docker-compose exec php php artisan migrate`
7. Run `docker-compose exec php php artisan db:seed` (optional)
8. Access application at http://localhost

### 9.2 Development Process

- Use Claude Code (VS Code) as primary IDE
- Code changes reflected immediately (volume mount)
- Database migrations for schema changes
- Filament resources for CRUD operations
- Livewire components for interactive features
- Laravel policies for authorization
- Redis cache for performance optimization

### 9.3 Testing Strategy

- **Unit Tests**: Model logic, service layer
- **Feature Tests**: API endpoints, business logic
- **Browser Tests**: Critical user flows (optional in Phase 1)
- Run via: `docker-compose exec php php artisan test`

---

## 10. Filament Implementation Details

### 10.1 Filament Resources

#### UserResource
- Manage users within current team
- Assign roles
- View user training history
- Admin and Coach access only

#### ExerciseResource
- CRUD operations for exercises
- Rich text description editor
- Multiple video URL inputs
- Tag management
- Coach and Admin access

#### TrainingResource
- CRUD operations for trainings
- Rich text content editor
- Exercise attachment with notes
- Client assignment
- Duplication action (custom)
- Status management
- Coach and Admin access

#### TrainingCalendarPage (Custom Page)
- Custom Filament page with calendar component
- Filter by client (for coaches)
- Color-coded by status
- Click to view/edit training
- All roles can access (with appropriate filters)

### 10.2 Filament Panels

- **Admin Panel**: Primary panel for all users
- **Team Switching**: Via panel selector (Filament's tenant feature)
- **Dashboard**: Show upcoming trainings, recent activity

### 10.3 Filament Forms

- Rich text editors for training content and exercise descriptions
- Repeater for multiple video URLs in exercises
- Relation managers for exercise attachment
- Date picker for scheduling
- Custom bulk scheduling form

---

## 11. Key Features Breakdown (Phase 1)

### 11.1 Must Have (MVP)

✅ User authentication and team management
✅ Multi-role support (Admin, Coach, Client)
✅ Exercise library with video URLs
✅ Training creation with rich text
✅ Training-exercise relationships
✅ Training assignment to clients
✅ Single and bulk training scheduling
✅ Calendar view of trainings
✅ Training status tracking (Draft, Scheduled, Completed, Skipped)
✅ Client feedback on completed trainings
✅ Team-based data isolation

### 11.2 Should Have (Phase 1)

⚠️ Training duplication wizard with pattern selection (every Monday, etc.)
⚠️ Dashboard with upcoming trainings summary
⚠️ Notification system for assigned trainings
⚠️ Search and filter in exercise library
⚠️ Training templates for quick creation

### 11.3 Could Have (Phase 1+)

🔵 Export training schedules to PDF
🔵 Import exercises from CSV
🔵 Client mobile app
🔵 Advanced analytics for coaches

---

## 12. Future Enhancements (Out of Scope for Phase 1)

### 12.1 AI-Powered Features

- AI-generated training programs based on goals
- Intelligent exercise recommendations
- Automatic training optimization based on feedback

### 12.2 Advanced Planning

- Multi-week program templates
- Periodization support (macro/meso/micro cycles)
- Automatic progression calculations
- Rest day optimization

### 12.3 Mobile Applications

- Native iOS and Android apps
- Offline training access
- Push notifications
- Video recording for exercise form checking

### 12.4 Social Features

- Client community forums
- Progress sharing
- Coach marketplace
- Training program sharing/templates

### 12.5 Integrations

- Wearable device integration (Apple Watch, Fitbit, etc.)
- Video hosting integration (YouTube, Vimeo)
- Calendar sync (Google Calendar, Apple Calendar)
- Payment processing for coaching services

### 12.6 Advanced Analytics

- Progress tracking over time
- Performance visualization
- Predictive analytics for client success
- Coach effectiveness metrics

---

## 13. Constraints & Assumptions

### 13.1 Constraints

- Desktop and tablet only (no mobile optimization in Phase 1)
- Single timezone (server timezone, no user-specific timezones)
- Direct user addition only (no email invitation system)
- English language only
- Limited to 100 teams initially

### 13.2 Assumptions

- Users have basic computer literacy
- Coaches understand training programming concepts
- Video content hosted externally (YouTube, Vimeo, etc.)
- HTTPS handled by production deployment environment
- Backup strategy handled at infrastructure level
- Users access via modern browsers (Chrome, Firefox, Safari, Edge)

### 13.3 Dependencies

- Stable internet connection required
- External video hosting services availability
- Docker environment for local development
- Composer package availability

---

## 14. Success Metrics

### 14.1 Technical Metrics

- Zero critical bugs in production
- Page load times < 2 seconds
- Database query times < 500ms average
- Test coverage > 70%

### 14.2 Functional Metrics

- Users can create a training in < 2 minutes
- Bulk scheduling 20 trainings takes < 10 seconds
- Calendar loads and displays correctly
- All role-based permissions enforced correctly

### 14.3 User Satisfaction

- Positive feedback from initial users (you!)
- Intuitive UI requiring minimal documentation
- Successful personal use case completion (weightlifting schedule for weeks/months)

---

## 15. Project Phases

### Phase 1: Foundation (Current Spec)
**Goal**: Solid, modular base with core functionality

- ✅ Docker environment setup
- ✅ Laravel + Filament installation
- ✅ Database schema and migrations
- ✅ User authentication and team management
- ✅ Exercise library
- ✅ Training CRUD
- ✅ Training scheduling and duplication
- ✅ Calendar view
- ✅ Client feedback system
- ✅ Role-based access control

### Phase 2: Enhancement (Future)
**Goal**: Improve usability and add convenience features

- Training templates
- Advanced duplication patterns
- Notifications system
- Dashboard improvements
- Export functionality
- Mobile-responsive design

### Phase 3: Intelligence (Future)
**Goal**: Add AI-powered features

- AI training generation
- Intelligent recommendations
- Automatic optimization
- Advanced analytics

### Phase 4: Scale (Future)
**Goal**: Mobile apps and integrations

- Native mobile applications
- Third-party integrations
- Social features
- Marketplace

---

## 16. Risks & Mitigations

### 16.1 Technical Risks

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Laravel 12/Filament 4 compatibility issues | High | Low | Use stable release versions, monitor package compatibility |
| Performance degradation with many trainings | Medium | Medium | Implement pagination, caching, database indexing |
| Data integrity issues with bulk operations | High | Low | Use database transactions, add validation |
| Calendar UI complexity | Medium | Medium | Use established calendar package, simplify initial features |

### 16.2 Functional Risks

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Unclear user requirements | High | Medium | Iterative development, frequent review with primary user |
| Scope creep | Medium | High | Strict adherence to Phase 1 spec, document future enhancements |
| Poor UX for bulk scheduling | Medium | Medium | Prototype and test scheduling flow early |
| Role permission bugs | High | Low | Comprehensive policy tests, manual testing of each role |

### 16.3 Project Risks

| Risk | Impact | Probability | Mitigation |
|------|--------|-------------|------------|
| Development time underestimated | Low | Medium | Modular approach allows partial delivery |
| External package abandonment | Medium | Low | Choose popular, maintained packages |
| Docker environment issues | Low | Low | Use standard Docker images, document setup |

---

## 17. Glossary

- **Training**: A workout session with exercises, scheduled for a specific date
- **Exercise**: A reusable workout movement/activity stored in the library
- **Client**: An athlete or user who receives and completes training programs
- **Coach**: A user who creates and assigns training programs to clients
- **Team**: A workspace containing users, trainings, and exercises with isolated data
- **Duplication**: Creating multiple copies of a training for different dates/clients
- **Scheduled Training**: A training assigned to a client for a specific date
- **Training Status**: The lifecycle state of a training (Draft, Scheduled, Completed, Skipped)
- **Rich Text**: Formatted text content with styling (bold, lists, links, etc.)
- **Panel**: Filament terminology for a distinct admin interface area
- **Resource**: Filament's CRUD interface for a model (e.g., TrainingResource)

---

## Document Information

- **Version**: 1.0
- **Created**: 2026-01-13
- **Author**: Generated via Claude Code based on rough project specification
- **Status**: Draft - Pending Review
- **Next Steps**: Create plan.md with implementation phases and tasks

---

## Appendix A: Reference Documentation

### Laravel 12
- Official Docs: https://laravel.com/docs/12.x
- Focus areas: Eloquent, Policies, Migrations, Validation

### Filament 4
- Official Docs: https://filamentphp.com/docs/4.x
- Focus areas: Resources, Forms, Tables, Pages, Panels, Tenancy

### Livewire 3
- Official Docs: https://livewire.laravel.com/docs/3.x
- Focus areas: Components, Forms, Actions

### Docker
- Docker Compose documentation
- Official PHP images
- MySQL configuration

---

## Appendix B: Open Questions

1. **Calendar Package**: Which Filament-compatible calendar package should we use?
   - Options: Filament plugins, FullCalendar integration, custom Livewire component

2. **Video Storage**: Should we support direct video uploads or only external URLs in Phase 1?
   - Phase 1 decision: External URLs only

3. **Training Copy vs Reference**: When duplicating trainings, should they be independent copies or reference a template?
   - Phase 1 decision: Independent copies for simplicity

4. **Exercise Notes**: Should exercise notes be editable by clients or coaches only?
   - Phase 1 decision: Coaches only

---

**End of Specification Document**
