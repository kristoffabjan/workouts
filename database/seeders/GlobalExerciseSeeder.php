<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Database\Seeder;

class GlobalExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $systemAdmin = User::where('is_admin', true)->first()
            ?? User::first();

        if (! $systemAdmin) {
            $this->command?->warn('No users found. Skipping GlobalExerciseSeeder.');

            return;
        }

        $createdBy = $systemAdmin->id;

        $exercises = $this->getExercises();

        foreach ($exercises as $exercise) {
            Exercise::withoutGlobalScopes()->firstOrCreate(
                [
                    'team_id' => null,
                    'name' => $exercise['name'],
                ],
                [
                    'description' => $exercise['description'] ?? null,
                    'tags' => $exercise['tags'],
                    'video_urls' => [],
                    'created_by' => $createdBy,
                ]
            );
        }
    }

    private function getExercises(): array
    {
        return [
            // Olympic Weightlifting (8)
            [
                'name' => 'Snatch',
                'description' => 'Full Olympic snatch from floor to overhead in one motion.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Clean & Jerk',
                'description' => 'Two-part Olympic lift: clean to shoulders, then jerk overhead.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Power Clean',
                'description' => 'Clean caught in a partial squat position.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Hang Clean',
                'description' => 'Clean starting from hang position above the knees.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Power Snatch',
                'description' => 'Snatch caught in a partial squat position.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Hang Snatch',
                'description' => 'Snatch starting from hang position above the knees.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Clean Pull',
                'description' => 'First and second pull of the clean without catching the bar.',
                'tags' => ['olympic', 'power', 'pull', 'barbell', 'compound'],
            ],
            [
                'name' => 'Push Press',
                'description' => 'Overhead press with leg drive assistance.',
                'tags' => ['olympic', 'power', 'upper-body', 'push', 'barbell', 'compound'],
            ],

            // Strength - Lower Body (10)
            [
                'name' => 'Back Squat',
                'description' => 'Barbell squat with bar positioned on upper back.',
                'tags' => ['strength', 'lower-body', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Front Squat',
                'description' => 'Barbell squat with bar in front rack position.',
                'tags' => ['strength', 'lower-body', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Conventional Deadlift',
                'description' => 'Barbell deadlift with narrow stance and hands outside legs.',
                'tags' => ['strength', 'lower-body', 'pull', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Sumo Deadlift',
                'description' => 'Barbell deadlift with wide stance and hands inside legs.',
                'tags' => ['strength', 'lower-body', 'pull', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Romanian Deadlift',
                'description' => 'Hip hinge movement emphasizing hamstrings.',
                'tags' => ['strength', 'lower-body', 'pull', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Hip Thrust',
                'description' => 'Glute-focused hip extension with back on bench.',
                'tags' => ['strength', 'lower-body', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Walking Lunge',
                'description' => 'Forward stepping lunges in a walking pattern.',
                'tags' => ['strength', 'lower-body', 'dumbbell', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Bulgarian Split Squat',
                'description' => 'Single-leg squat with rear foot elevated.',
                'tags' => ['strength', 'lower-body', 'dumbbell', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Leg Press',
                'description' => 'Machine-based leg pressing movement.',
                'tags' => ['strength', 'lower-body', 'machine', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Goblet Squat',
                'description' => 'Squat holding weight at chest level.',
                'tags' => ['strength', 'lower-body', 'dumbbell', 'kettlebell', 'compound', 'bilateral'],
            ],

            // Strength - Upper Body Push (8)
            [
                'name' => 'Barbell Bench Press',
                'description' => 'Horizontal pressing movement on flat bench.',
                'tags' => ['strength', 'upper-body', 'push', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Incline Bench Press',
                'description' => 'Bench press on inclined bench targeting upper chest.',
                'tags' => ['strength', 'upper-body', 'push', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Overhead Press',
                'description' => 'Standing barbell press overhead.',
                'tags' => ['strength', 'upper-body', 'push', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Dumbbell Shoulder Press',
                'description' => 'Seated or standing overhead press with dumbbells.',
                'tags' => ['strength', 'upper-body', 'push', 'dumbbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Dips',
                'description' => 'Bodyweight pressing exercise on parallel bars.',
                'tags' => ['strength', 'upper-body', 'push', 'bodyweight', 'compound'],
            ],
            [
                'name' => 'Dumbbell Bench Press',
                'description' => 'Horizontal pressing with dumbbells.',
                'tags' => ['strength', 'upper-body', 'push', 'dumbbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Close Grip Bench Press',
                'description' => 'Bench press with narrow grip emphasizing triceps.',
                'tags' => ['strength', 'upper-body', 'push', 'barbell', 'compound'],
            ],
            [
                'name' => 'Tricep Pushdown',
                'description' => 'Cable exercise for triceps isolation.',
                'tags' => ['strength', 'upper-body', 'push', 'machine', 'isolation'],
            ],

            // Strength - Upper Body Pull (8)
            [
                'name' => 'Pull-ups',
                'description' => 'Bodyweight pulling exercise with overhand grip.',
                'tags' => ['strength', 'upper-body', 'pull', 'bodyweight', 'compound'],
            ],
            [
                'name' => 'Chin-ups',
                'description' => 'Bodyweight pulling exercise with underhand grip.',
                'tags' => ['strength', 'upper-body', 'pull', 'bodyweight', 'compound'],
            ],
            [
                'name' => 'Barbell Row',
                'description' => 'Bent-over rowing movement with barbell.',
                'tags' => ['strength', 'upper-body', 'pull', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Dumbbell Row',
                'description' => 'Single-arm rowing movement with dumbbell.',
                'tags' => ['strength', 'upper-body', 'pull', 'dumbbell', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Lat Pulldown',
                'description' => 'Cable pulldown exercise for lats.',
                'tags' => ['strength', 'upper-body', 'pull', 'machine', 'compound'],
            ],
            [
                'name' => 'Seated Cable Row',
                'description' => 'Horizontal cable rowing movement.',
                'tags' => ['strength', 'upper-body', 'pull', 'machine', 'compound'],
            ],
            [
                'name' => 'Face Pull',
                'description' => 'Cable exercise for rear delts and upper back.',
                'tags' => ['strength', 'upper-body', 'pull', 'machine', 'isolation'],
            ],
            [
                'name' => 'Bicep Curl',
                'description' => 'Isolation exercise for biceps.',
                'tags' => ['strength', 'upper-body', 'pull', 'dumbbell', 'barbell', 'isolation'],
            ],

            // Cardio (8)
            [
                'name' => 'Running',
                'description' => 'Outdoor or treadmill running.',
                'tags' => ['cardio', 'endurance', 'lower-body'],
            ],
            [
                'name' => 'Cycling',
                'description' => 'Outdoor or stationary bike riding.',
                'tags' => ['cardio', 'endurance', 'lower-body'],
            ],
            [
                'name' => 'Rowing Machine',
                'description' => 'Indoor rowing for full-body cardio.',
                'tags' => ['cardio', 'endurance', 'full-body', 'machine'],
            ],
            [
                'name' => 'Jump Rope',
                'description' => 'Skipping rope for cardio conditioning.',
                'tags' => ['cardio', 'endurance', 'full-body', 'plyometric'],
            ],
            [
                'name' => 'Swimming',
                'description' => 'Any swimming stroke for cardio.',
                'tags' => ['cardio', 'endurance', 'full-body'],
            ],
            [
                'name' => 'Stair Climber',
                'description' => 'Machine-based stair climbing.',
                'tags' => ['cardio', 'endurance', 'lower-body', 'machine'],
            ],
            [
                'name' => 'Assault Bike',
                'description' => 'Air resistance bike with arm movement.',
                'tags' => ['cardio', 'endurance', 'full-body', 'machine'],
            ],
            [
                'name' => 'Elliptical',
                'description' => 'Low-impact elliptical machine cardio.',
                'tags' => ['cardio', 'endurance', 'full-body', 'machine'],
            ],

            // Core & Abs (8)
            [
                'name' => 'Plank',
                'description' => 'Isometric core hold in push-up position.',
                'tags' => ['core', 'bodyweight', 'isometric'],
            ],
            [
                'name' => 'Dead Bug',
                'description' => 'Core stability exercise lying on back.',
                'tags' => ['core', 'bodyweight', 'stability'],
            ],
            [
                'name' => 'Hanging Leg Raise',
                'description' => 'Leg raises while hanging from bar.',
                'tags' => ['core', 'bodyweight', 'compound'],
            ],
            [
                'name' => 'Russian Twist',
                'description' => 'Rotational core exercise.',
                'tags' => ['core', 'bodyweight', 'dumbbell'],
            ],
            [
                'name' => 'Ab Wheel Rollout',
                'description' => 'Core exercise using ab wheel.',
                'tags' => ['core', 'compound'],
            ],
            [
                'name' => 'Cable Woodchop',
                'description' => 'Rotational cable exercise for core.',
                'tags' => ['core', 'machine', 'compound'],
            ],
            [
                'name' => 'Pallof Press',
                'description' => 'Anti-rotation core exercise with cable.',
                'tags' => ['core', 'machine', 'stability'],
            ],
            [
                'name' => 'Bird Dog',
                'description' => 'Contralateral limb raise for core stability.',
                'tags' => ['core', 'bodyweight', 'stability'],
            ],

            // Flexibility & Mobility (6)
            [
                'name' => 'Hip Flexor Stretch',
                'description' => 'Kneeling or standing hip flexor stretch.',
                'tags' => ['flexibility', 'mobility', 'lower-body'],
            ],
            [
                'name' => 'Shoulder Dislocates',
                'description' => 'Shoulder mobility exercise with band or stick.',
                'tags' => ['flexibility', 'mobility', 'upper-body'],
            ],
            [
                'name' => 'Foam Rolling - Quads',
                'description' => 'Self-myofascial release for quadriceps.',
                'tags' => ['flexibility', 'mobility', 'lower-body'],
            ],
            [
                'name' => 'Foam Rolling - Back',
                'description' => 'Self-myofascial release for upper back.',
                'tags' => ['flexibility', 'mobility', 'upper-body'],
            ],
            [
                'name' => '90/90 Hip Stretch',
                'description' => 'Seated hip mobility exercise.',
                'tags' => ['flexibility', 'mobility', 'lower-body'],
            ],
            [
                'name' => 'Cat-Cow Stretch',
                'description' => 'Spinal mobility exercise on hands and knees.',
                'tags' => ['flexibility', 'mobility', 'core'],
            ],

            // Plyometrics (6)
            [
                'name' => 'Box Jump',
                'description' => 'Explosive jump onto elevated platform.',
                'tags' => ['plyometric', 'power', 'lower-body', 'bodyweight'],
            ],
            [
                'name' => 'Burpees',
                'description' => 'Full-body explosive exercise.',
                'tags' => ['plyometric', 'cardio', 'full-body', 'bodyweight'],
            ],
            [
                'name' => 'Jump Squat',
                'description' => 'Explosive squat with jump.',
                'tags' => ['plyometric', 'power', 'lower-body', 'bodyweight'],
            ],
            [
                'name' => 'Broad Jump',
                'description' => 'Horizontal explosive jump.',
                'tags' => ['plyometric', 'power', 'lower-body', 'bodyweight'],
            ],
            [
                'name' => 'Medicine Ball Slam',
                'description' => 'Explosive overhead slam with medicine ball.',
                'tags' => ['plyometric', 'power', 'full-body'],
            ],
            [
                'name' => 'Tuck Jump',
                'description' => 'Vertical jump bringing knees to chest.',
                'tags' => ['plyometric', 'power', 'lower-body', 'bodyweight'],
            ],

            // Functional & Bodyweight (6)
            [
                'name' => 'Push-ups',
                'description' => 'Classic bodyweight pressing exercise.',
                'tags' => ['bodyweight', 'upper-body', 'push', 'compound'],
            ],
            [
                'name' => 'Bodyweight Lunges',
                'description' => 'Unloaded forward or reverse lunges.',
                'tags' => ['bodyweight', 'lower-body', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Air Squat',
                'description' => 'Unloaded squat movement.',
                'tags' => ['bodyweight', 'lower-body', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Mountain Climbers',
                'description' => 'Dynamic plank with alternating knee drives.',
                'tags' => ['bodyweight', 'cardio', 'core', 'full-body'],
            ],
            [
                'name' => 'Kettlebell Swing',
                'description' => 'Hip hinge movement with kettlebell.',
                'tags' => ['kettlebell', 'power', 'full-body', 'compound'],
            ],
            [
                'name' => 'Turkish Get-Up',
                'description' => 'Complex movement from lying to standing with weight overhead.',
                'tags' => ['kettlebell', 'full-body', 'compound', 'stability'],
            ],

            // Additional useful exercises (4)
            [
                'name' => 'Farmers Walk',
                'description' => 'Loaded carry with weights at sides.',
                'tags' => ['strength', 'full-body', 'dumbbell', 'kettlebell', 'compound'],
            ],
            [
                'name' => 'Wall Sit',
                'description' => 'Isometric squat hold against wall.',
                'tags' => ['bodyweight', 'lower-body', 'isometric'],
            ],
            [
                'name' => 'Glute Bridge',
                'description' => 'Hip extension exercise lying on back.',
                'tags' => ['bodyweight', 'lower-body', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Superman Hold',
                'description' => 'Prone back extension hold.',
                'tags' => ['bodyweight', 'core', 'isometric'],
            ],

            // Strength - Lower Body (additional) (12)
            [
                'name' => 'Hack Squat',
                'description' => 'Machine squat with back supported on angled sled.',
                'tags' => ['strength', 'lower-body', 'machine', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Zercher Squat',
                'description' => 'Squat with barbell held in the crook of the elbows.',
                'tags' => ['strength', 'lower-body', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Overhead Squat',
                'description' => 'Squat with barbell locked out overhead.',
                'tags' => ['strength', 'lower-body', 'barbell', 'compound', 'bilateral', 'mobility'],
            ],
            [
                'name' => 'Pistol Squat',
                'description' => 'Single-leg bodyweight squat with non-working leg extended.',
                'tags' => ['strength', 'lower-body', 'bodyweight', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Cossack Squat',
                'description' => 'Wide-stance lateral squat shifting weight to one leg.',
                'tags' => ['strength', 'lower-body', 'bodyweight', 'compound', 'unilateral', 'mobility'],
            ],
            [
                'name' => 'Step-Up',
                'description' => 'Stepping onto elevated surface with weight.',
                'tags' => ['strength', 'lower-body', 'dumbbell', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Reverse Lunge',
                'description' => 'Stepping backward into lunge position.',
                'tags' => ['strength', 'lower-body', 'dumbbell', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Leg Extension',
                'description' => 'Machine isolation exercise for quadriceps.',
                'tags' => ['strength', 'lower-body', 'machine', 'isolation', 'bilateral'],
            ],
            [
                'name' => 'Leg Curl',
                'description' => 'Machine isolation exercise for hamstrings.',
                'tags' => ['strength', 'lower-body', 'machine', 'isolation', 'bilateral'],
            ],
            [
                'name' => 'Standing Calf Raise',
                'description' => 'Calf raise performed standing with weight on shoulders.',
                'tags' => ['strength', 'lower-body', 'machine', 'isolation', 'bilateral'],
            ],
            [
                'name' => 'Seated Calf Raise',
                'description' => 'Calf raise performed seated targeting the soleus.',
                'tags' => ['strength', 'lower-body', 'machine', 'isolation', 'bilateral'],
            ],
            [
                'name' => 'Trap Bar Deadlift',
                'description' => 'Deadlift using a hexagonal trap bar for neutral grip.',
                'tags' => ['strength', 'lower-body', 'pull', 'barbell', 'compound', 'bilateral'],
            ],

            // Strength - Upper Body Push (additional) (10)
            [
                'name' => 'Decline Bench Press',
                'description' => 'Bench press on a declined bench targeting lower chest.',
                'tags' => ['strength', 'upper-body', 'push', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Arnold Press',
                'description' => 'Rotational dumbbell shoulder press hitting all three delt heads.',
                'tags' => ['strength', 'upper-body', 'push', 'dumbbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Landmine Press',
                'description' => 'Single or double arm press using a barbell anchored at one end.',
                'tags' => ['strength', 'upper-body', 'push', 'barbell', 'compound'],
            ],
            [
                'name' => 'Lateral Raise',
                'description' => 'Dumbbell raise to the side for medial deltoids.',
                'tags' => ['strength', 'upper-body', 'push', 'dumbbell', 'isolation'],
            ],
            [
                'name' => 'Front Raise',
                'description' => 'Dumbbell raise to the front for anterior deltoids.',
                'tags' => ['strength', 'upper-body', 'push', 'dumbbell', 'isolation'],
            ],
            [
                'name' => 'Overhead Tricep Extension',
                'description' => 'Tricep extension with dumbbell or cable overhead.',
                'tags' => ['strength', 'upper-body', 'push', 'dumbbell', 'isolation'],
            ],
            [
                'name' => 'Skull Crusher',
                'description' => 'Lying tricep extension with barbell or EZ bar.',
                'tags' => ['strength', 'upper-body', 'push', 'barbell', 'isolation'],
            ],
            [
                'name' => 'Cable Fly',
                'description' => 'Chest fly movement using cable machine.',
                'tags' => ['strength', 'upper-body', 'push', 'machine', 'isolation'],
            ],
            [
                'name' => 'Dumbbell Fly',
                'description' => 'Chest fly movement lying on flat bench with dumbbells.',
                'tags' => ['strength', 'upper-body', 'push', 'dumbbell', 'isolation'],
            ],
            [
                'name' => 'Machine Chest Press',
                'description' => 'Horizontal pressing on a chest press machine.',
                'tags' => ['strength', 'upper-body', 'push', 'machine', 'compound', 'bilateral'],
            ],

            // Strength - Upper Body Pull (additional) (10)
            [
                'name' => 'T-Bar Row',
                'description' => 'Rowing exercise using a T-bar or landmine setup.',
                'tags' => ['strength', 'upper-body', 'pull', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Pendlay Row',
                'description' => 'Strict bent-over row with bar returning to floor each rep.',
                'tags' => ['strength', 'upper-body', 'pull', 'barbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Chest Supported Row',
                'description' => 'Rowing movement with chest supported on incline bench.',
                'tags' => ['strength', 'upper-body', 'pull', 'dumbbell', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Meadows Row',
                'description' => 'Single-arm landmine row with staggered stance.',
                'tags' => ['strength', 'upper-body', 'pull', 'barbell', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Straight Arm Pulldown',
                'description' => 'Cable pulldown with straight arms targeting lats.',
                'tags' => ['strength', 'upper-body', 'pull', 'machine', 'isolation'],
            ],
            [
                'name' => 'Hammer Curl',
                'description' => 'Dumbbell curl with neutral grip targeting brachialis.',
                'tags' => ['strength', 'upper-body', 'pull', 'dumbbell', 'isolation'],
            ],
            [
                'name' => 'Preacher Curl',
                'description' => 'Bicep curl with upper arms braced on preacher bench.',
                'tags' => ['strength', 'upper-body', 'pull', 'barbell', 'isolation'],
            ],
            [
                'name' => 'Reverse Fly',
                'description' => 'Rear deltoid fly with dumbbells or cable.',
                'tags' => ['strength', 'upper-body', 'pull', 'dumbbell', 'isolation'],
            ],
            [
                'name' => 'Shrug',
                'description' => 'Trap-focused exercise lifting shoulders with barbell or dumbbells.',
                'tags' => ['strength', 'upper-body', 'pull', 'barbell', 'dumbbell', 'isolation'],
            ],
            [
                'name' => 'Inverted Row',
                'description' => 'Bodyweight horizontal row using bar or rings at waist height.',
                'tags' => ['strength', 'upper-body', 'pull', 'bodyweight', 'compound'],
            ],

            // Olympic Weightlifting (additional) (6)
            [
                'name' => 'Muscle Clean',
                'description' => 'Clean variation pulled directly to front rack without squatting under.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Muscle Snatch',
                'description' => 'Snatch variation pulled directly overhead without squatting under.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Snatch Grip Deadlift',
                'description' => 'Deadlift with wide snatch grip for upper back development.',
                'tags' => ['olympic', 'strength', 'pull', 'barbell', 'compound'],
            ],
            [
                'name' => 'Split Jerk',
                'description' => 'Jerk variation catching the bar in a split stance.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Push Jerk',
                'description' => 'Jerk variation catching the bar in a partial squat.',
                'tags' => ['olympic', 'power', 'full-body', 'barbell', 'compound'],
            ],
            [
                'name' => 'Snatch Pull',
                'description' => 'First and second pull of the snatch without catching the bar.',
                'tags' => ['olympic', 'power', 'pull', 'barbell', 'compound'],
            ],

            // Kettlebell (8)
            [
                'name' => 'Kettlebell Clean',
                'description' => 'Single-arm clean bringing kettlebell to rack position.',
                'tags' => ['kettlebell', 'power', 'full-body', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Kettlebell Snatch',
                'description' => 'Single-arm snatch bringing kettlebell from floor to overhead.',
                'tags' => ['kettlebell', 'power', 'full-body', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Kettlebell Goblet Squat',
                'description' => 'Squat holding kettlebell at chest in goblet position.',
                'tags' => ['kettlebell', 'strength', 'lower-body', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Kettlebell Windmill',
                'description' => 'Hip hinge with kettlebell overhead, reaching opposite hand to floor.',
                'tags' => ['kettlebell', 'stability', 'core', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Kettlebell Row',
                'description' => 'Single-arm row in bent-over position with kettlebell.',
                'tags' => ['kettlebell', 'strength', 'upper-body', 'pull', 'compound', 'unilateral'],
            ],
            [
                'name' => 'Kettlebell Thruster',
                'description' => 'Front squat to overhead press with kettlebells.',
                'tags' => ['kettlebell', 'power', 'full-body', 'compound'],
            ],
            [
                'name' => 'Kettlebell Halo',
                'description' => 'Circular movement of kettlebell around the head for shoulder mobility.',
                'tags' => ['kettlebell', 'mobility', 'upper-body'],
            ],
            [
                'name' => 'Kettlebell Dead Clean',
                'description' => 'Clean starting from a dead stop on the floor each rep.',
                'tags' => ['kettlebell', 'power', 'full-body', 'compound', 'unilateral'],
            ],

            // Core & Abs (additional) (8)
            [
                'name' => 'V-Up',
                'description' => 'Simultaneous lift of torso and legs forming a V shape.',
                'tags' => ['core', 'bodyweight', 'compound'],
            ],
            [
                'name' => 'Bicycle Crunch',
                'description' => 'Alternating elbow-to-knee crunch with pedaling motion.',
                'tags' => ['core', 'bodyweight', 'compound'],
            ],
            [
                'name' => 'Dragon Flag',
                'description' => 'Advanced core exercise lowering body from bench with only shoulders as contact.',
                'tags' => ['core', 'bodyweight', 'compound'],
            ],
            [
                'name' => 'Hollow Body Hold',
                'description' => 'Supine isometric hold with arms and legs extended off floor.',
                'tags' => ['core', 'bodyweight', 'isometric'],
            ],
            [
                'name' => 'Side Plank',
                'description' => 'Lateral isometric hold on one forearm.',
                'tags' => ['core', 'bodyweight', 'isometric', 'unilateral'],
            ],
            [
                'name' => 'Toe Touch',
                'description' => 'Lying on back, reaching hands to toes with legs vertical.',
                'tags' => ['core', 'bodyweight', 'isolation'],
            ],
            [
                'name' => 'Cable Crunch',
                'description' => 'Kneeling crunch using cable resistance.',
                'tags' => ['core', 'machine', 'isolation'],
            ],
            [
                'name' => 'L-Sit',
                'description' => 'Isometric hold on parallel bars or floor with legs extended horizontally.',
                'tags' => ['core', 'bodyweight', 'isometric'],
            ],

            // Cardio (additional) (8)
            [
                'name' => 'Sled Push',
                'description' => 'Pushing a weighted sled across the floor.',
                'tags' => ['cardio', 'strength', 'lower-body', 'compound'],
            ],
            [
                'name' => 'Sled Pull',
                'description' => 'Pulling a weighted sled using a rope or harness.',
                'tags' => ['cardio', 'strength', 'full-body', 'compound'],
            ],
            [
                'name' => 'Battle Ropes',
                'description' => 'Alternating or double arm waves with heavy ropes.',
                'tags' => ['cardio', 'endurance', 'full-body'],
            ],
            [
                'name' => 'Ski Erg',
                'description' => 'Vertical pulling machine simulating cross-country skiing.',
                'tags' => ['cardio', 'endurance', 'full-body', 'machine'],
            ],
            [
                'name' => 'Hiking',
                'description' => 'Walking on trails or inclines outdoors.',
                'tags' => ['cardio', 'endurance', 'lower-body'],
            ],
            [
                'name' => 'Sprints',
                'description' => 'Short-distance maximal effort running.',
                'tags' => ['cardio', 'power', 'lower-body'],
            ],
            [
                'name' => 'Hill Sprints',
                'description' => 'Maximal effort sprinting uphill.',
                'tags' => ['cardio', 'power', 'lower-body'],
            ],
            [
                'name' => 'Tire Flip',
                'description' => 'Flipping a heavy tire for full-body conditioning.',
                'tags' => ['cardio', 'strength', 'full-body', 'compound'],
            ],

            // Plyometrics (additional) (6)
            [
                'name' => 'Depth Jump',
                'description' => 'Stepping off a box and immediately jumping upon landing.',
                'tags' => ['plyometric', 'power', 'lower-body', 'bodyweight'],
            ],
            [
                'name' => 'Lateral Box Jump',
                'description' => 'Explosive sideways jump onto an elevated platform.',
                'tags' => ['plyometric', 'power', 'lower-body', 'bodyweight'],
            ],
            [
                'name' => 'Plyo Push-Up',
                'description' => 'Explosive push-up where hands leave the ground.',
                'tags' => ['plyometric', 'power', 'upper-body', 'bodyweight'],
            ],
            [
                'name' => 'Skater Jump',
                'description' => 'Lateral bounding jump from one leg to the other.',
                'tags' => ['plyometric', 'power', 'lower-body', 'bodyweight', 'unilateral'],
            ],
            [
                'name' => 'Medicine Ball Wall Throw',
                'description' => 'Explosive chest pass against a wall with medicine ball.',
                'tags' => ['plyometric', 'power', 'upper-body'],
            ],
            [
                'name' => 'Banded Broad Jump',
                'description' => 'Broad jump with resistance band for added power demand.',
                'tags' => ['plyometric', 'power', 'lower-body', 'band'],
            ],

            // Functional & Bodyweight (additional) (8)
            [
                'name' => 'Bear Crawl',
                'description' => 'Crawling on hands and feet with knees hovering above ground.',
                'tags' => ['bodyweight', 'full-body', 'compound', 'stability'],
            ],
            [
                'name' => 'Handstand Push-Up',
                'description' => 'Inverted push-up in a handstand position against wall.',
                'tags' => ['bodyweight', 'upper-body', 'push', 'compound'],
            ],
            [
                'name' => 'Muscle-Up',
                'description' => 'Pull-up transitioning into a dip on top of the bar.',
                'tags' => ['bodyweight', 'upper-body', 'compound'],
            ],
            [
                'name' => 'Wall Walk',
                'description' => 'Walking feet up a wall to handstand position and back down.',
                'tags' => ['bodyweight', 'upper-body', 'compound', 'stability'],
            ],
            [
                'name' => 'Crab Walk',
                'description' => 'Moving on hands and feet in a supine tabletop position.',
                'tags' => ['bodyweight', 'full-body', 'compound'],
            ],
            [
                'name' => 'Inchworm',
                'description' => 'Walking hands out to plank and walking feet to hands.',
                'tags' => ['bodyweight', 'full-body', 'compound', 'mobility'],
            ],
            [
                'name' => 'Diamond Push-Up',
                'description' => 'Push-up with hands close together forming a diamond shape.',
                'tags' => ['bodyweight', 'upper-body', 'push', 'compound'],
            ],
            [
                'name' => 'Pike Push-Up',
                'description' => 'Push-up in a pike position targeting shoulders.',
                'tags' => ['bodyweight', 'upper-body', 'push', 'compound'],
            ],

            // Flexibility & Mobility (additional) (8)
            [
                'name' => 'Pigeon Stretch',
                'description' => 'Deep hip opener stretching the external rotators.',
                'tags' => ['flexibility', 'mobility', 'lower-body'],
            ],
            [
                'name' => 'World\'s Greatest Stretch',
                'description' => 'Multi-position dynamic stretch for hips, thoracic spine, and hamstrings.',
                'tags' => ['flexibility', 'mobility', 'full-body'],
            ],
            [
                'name' => 'Couch Stretch',
                'description' => 'Deep hip flexor and quad stretch using a wall or bench.',
                'tags' => ['flexibility', 'mobility', 'lower-body'],
            ],
            [
                'name' => 'Thoracic Spine Rotation',
                'description' => 'Rotational mobility drill for the mid-back.',
                'tags' => ['flexibility', 'mobility', 'core'],
            ],
            [
                'name' => 'Banded Shoulder Stretch',
                'description' => 'Shoulder mobility stretch using resistance band.',
                'tags' => ['flexibility', 'mobility', 'upper-body', 'band'],
            ],
            [
                'name' => 'Hamstring Stretch',
                'description' => 'Standing or seated stretch for hamstring flexibility.',
                'tags' => ['flexibility', 'mobility', 'lower-body'],
            ],
            [
                'name' => 'Ankle Mobility Drill',
                'description' => 'Wall-facing or banded ankle dorsiflexion stretch.',
                'tags' => ['flexibility', 'mobility', 'lower-body'],
            ],
            [
                'name' => 'Foam Rolling - IT Band',
                'description' => 'Self-myofascial release for the iliotibial band.',
                'tags' => ['flexibility', 'mobility', 'lower-body'],
            ],

            // Machine & Cable (additional) (10)
            [
                'name' => 'Cable Lateral Raise',
                'description' => 'Lateral raise using cable for constant tension.',
                'tags' => ['strength', 'upper-body', 'push', 'machine', 'isolation'],
            ],
            [
                'name' => 'Cable Bicep Curl',
                'description' => 'Bicep curl using cable for constant tension.',
                'tags' => ['strength', 'upper-body', 'pull', 'machine', 'isolation'],
            ],
            [
                'name' => 'Machine Shoulder Press',
                'description' => 'Overhead pressing on a shoulder press machine.',
                'tags' => ['strength', 'upper-body', 'push', 'machine', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Pec Deck Fly',
                'description' => 'Chest fly on a pec deck machine.',
                'tags' => ['strength', 'upper-body', 'push', 'machine', 'isolation'],
            ],
            [
                'name' => 'Machine Row',
                'description' => 'Horizontal rowing on a plate-loaded or cable machine.',
                'tags' => ['strength', 'upper-body', 'pull', 'machine', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Smith Machine Squat',
                'description' => 'Squat performed on a Smith machine with guided bar path.',
                'tags' => ['strength', 'lower-body', 'machine', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Cable Kickback',
                'description' => 'Glute kickback using low cable attachment.',
                'tags' => ['strength', 'lower-body', 'machine', 'isolation', 'unilateral'],
            ],
            [
                'name' => 'Reverse Pec Deck',
                'description' => 'Rear delt fly on a reverse pec deck machine.',
                'tags' => ['strength', 'upper-body', 'pull', 'machine', 'isolation'],
            ],
            [
                'name' => 'Hip Adduction Machine',
                'description' => 'Machine exercise targeting inner thigh adductors.',
                'tags' => ['strength', 'lower-body', 'machine', 'isolation', 'bilateral'],
            ],
            [
                'name' => 'Hip Abduction Machine',
                'description' => 'Machine exercise targeting outer hip abductors.',
                'tags' => ['strength', 'lower-body', 'machine', 'isolation', 'bilateral'],
            ],

            // Band & Resistance (6)
            [
                'name' => 'Banded Pull-Apart',
                'description' => 'Pulling a resistance band apart horizontally for rear delts.',
                'tags' => ['strength', 'upper-body', 'pull', 'band', 'isolation'],
            ],
            [
                'name' => 'Banded Face Pull',
                'description' => 'Face pull using resistance band anchored at head height.',
                'tags' => ['strength', 'upper-body', 'pull', 'band', 'isolation'],
            ],
            [
                'name' => 'Banded Good Morning',
                'description' => 'Hip hinge with resistance band around neck and under feet.',
                'tags' => ['strength', 'lower-body', 'band', 'compound'],
            ],
            [
                'name' => 'Banded Squat',
                'description' => 'Squat with resistance band around knees or looped under feet.',
                'tags' => ['strength', 'lower-body', 'band', 'compound', 'bilateral'],
            ],
            [
                'name' => 'Banded Lateral Walk',
                'description' => 'Side stepping with resistance band around ankles or knees.',
                'tags' => ['strength', 'lower-body', 'band', 'isolation'],
            ],
            [
                'name' => 'Banded Tricep Pushdown',
                'description' => 'Tricep extension using resistance band anchored overhead.',
                'tags' => ['strength', 'upper-body', 'push', 'band', 'isolation'],
            ],

        ];
    }
}
