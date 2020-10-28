<?php declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\BugReport;
use App\Helpers\DBQueryBuilderFactory;
use App\Repositories\BugReportRepository;
use App\Loggers\Logger;
use App\Exception\BadRequestException;

if (isset($_POST, $_POST['update'])) {
    $bugReportId = $_POST['reportId'];
    $reportType = $_POST['reportType'];
    $email = $_POST['email'];
    $link = $_POST['link'];
    $message = $_POST['message'];

    $logger = new Logger();
    try {
        $queryBuilder = DBQueryBuilderFactory::make('database', 'mysql', ['db_name' => 'bug']);
        $repository = new BugReportRepository($queryBuilder);
        $bugReport = $repository->find((int) $bugReportId);
        $bugReport->setReportType($reportType);
        $bugReport->setEmail($email);
        $bugReport->setLink($link);
        $bugReport->setMessage($message);

        $repository->update($bugReport);

    } catch (Exception $e) {
        $logger->critical($e->getMessage(), [$e]);
        
        throw new BadRequestException([$e], $e->getMessage(), 404); 
    }

    // $logger->info('new bug report created', 
    // [
    //     'id' => $newReport->getId(),
    //     'type' => $newReport->getReportType(),
    // ]);
    // $bugReport = $repository->findAll();
}