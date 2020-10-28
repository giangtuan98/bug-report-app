<?php

namespace App\Repositories;

use App\Models\BugReport;

class BugReportRepository extends BaseRepository {
    protected static $table = 'reports';
    protected static $className = BugReport::class;
}