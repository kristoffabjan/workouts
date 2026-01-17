<?php

namespace Database\Seeders;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Database\Seeder;

class GlobalExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $systemAdmin = User::where('is_admin', true)->first();
        $createdBy = $systemAdmin?->id;

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
        ];
    }
}
