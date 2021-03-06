<?php declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\BugReport;
use App\Helpers\DBQueryBuilderFactory;
use App\Repositories\BugReportRepository;
use App\Loggers\Logger;
use App\Exception\BadRequestException;

if (isset($_POST, $_POST['delete'])) {
    $bugReportId = $_POST['reportId'];

    $logger = new Logger();
    try {
        $queryBuilder = DBQueryBuilderFactory::make('database', 'mysql', ['db_name' => 'bug']);
        $repository = new BugReportRepository($queryBuilder);
        $bugReport = $repository->find((int) $bugReportId);

        $repository->delete($bugReport);

    } catch (Exception $e) {
        $logger->critical($e->getMessage(), [$e]);
        
        throw new BadRequestException([$e], $e->getMessage(), 404); 
    }

}