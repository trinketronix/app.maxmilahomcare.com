<?php

namespace App\Constants;
class Status {
    // -1: Not Verified (the user is pending of verification. Just for user authentication)
    public const int NOT_VERIFIED = -1;
    // 0: Inactive (the user is in temporary suspension, like vacations, or personal leave)
    public const int INACTIVE = 0;
    // 1: Active/Normal/Visible (record is active and normally visible)
    public const int ACTIVE = 1;
    // 2: Archived (record archived)
    public const int ARCHIVED = 2;
    // 3: Soft-deleted (record marked for deletion)
    public const int SOFT_DELETED = 3;
}