<?php

namespace App\Constants;

class Visit {
    // Column constants
    public const string ID = 'id';
    public const string USER_ID = 'user_id';
    public const string PATIENT_ID = 'patient_id';
    public const string ADDRESS_ID = 'address_id';
    public const string START_TIME = 'start_time';
    public const string END_TIME = 'end_time';
    public const string NOTE = 'note';
    public const string PROGRESS = 'progress';
    public const string SCHEDULED_BY = 'scheduled_by';
    public const string CHECKIN_BY = 'checkin_by';
    public const string CHECKOUT_BY = 'checkout_by';
    public const string CANCELED_BY = 'canceled_by';
    public const string APPROVED_BY = 'approved_by';
    public const string STATUS = 'status';
    public const string CREATED_AT = 'created_at';
    public const string UPDATED_AT = 'updated_at';
}