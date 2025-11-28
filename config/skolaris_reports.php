<?php

return [
    'school_name' => env('SCHOOL_NAME', 'Skolaris High School'),
    'school_logo' => env('SCHOOL_LOGO', 'images/logo.png'), // Path relative to public/
    'grading_system' => [
        'A' => ['min' => 80, 'max' => 100, 'points' => 12, 'remark' => 'Excellent'],
        'B' => ['min' => 70, 'max' => 79, 'points' => 10, 'remark' => 'Very Good'],
        'C' => ['min' => 60, 'max' => 69, 'points' => 8, 'remark' => 'Good'],
        'D' => ['min' => 50, 'max' => 59, 'points' => 6, 'remark' => 'Average'],
        'E' => ['min' => 0, 'max' => 49, 'points' => 4, 'remark' => 'Fail'],
    ],
    'user_model' => App\Models\User::class,
    'term_model' => App\Models\Academic\Term::class, 
    'subject_model' => App\Models\Academic\Subject::class,
];