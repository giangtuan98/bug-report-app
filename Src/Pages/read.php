<?php declare(strict_types=1);

use App\Repositories\BugReportRepository;
use App\Helpers\DBQueryBuilderFactory;

$queryBuilder = DBQueryBuilderFactory::make('database', 'mysql', ['db_name' => 'bug']);
