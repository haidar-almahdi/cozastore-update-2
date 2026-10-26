<?php

return [
    'black_friday' => [
        'discount_percentage' => (int) env('BLACK_FRIDAY_DISCOUNT_PERCENTAGE', 30),
        'month' => (int) env('BLACK_FRIDAY_MONTH', 11),
        'start_day' => (int) env('BLACK_FRIDAY_START_DAY', 25),
        'end_day' => (int) env('BLACK_FRIDAY_END_DAY', 30),
    ],
];
