<?php

return [
    'employee_lifecycle' => [
        'invalid_status_transition' => 'The requested user status transition is not allowed.',
        'effective_date_before_assignment_start' => 'The effective date cannot be earlier than a current assignment start date.',
        'actor_must_be_persisted' => 'The lifecycle actor must be a persisted user.',
        'effective_date_in_future' => 'The effective date cannot be in the future.',
    ],
];
