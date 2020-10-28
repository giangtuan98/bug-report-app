<?php

namespace Tests\Units\Repositories;

use App\Helpers\DBQueryBuilderFactory;
use App\Models\BugReport;
use App\Repositories\BugReportRepository;
use phpDocumentor\Reflection\Types\Collection;
use PHPUnit\Framework\TestCase;

class RepositoryTest extends TestCase
{
    /**
     * @var QueryBuilder $queryBuilder
     * @var BugReportRepository $bugReportRepository
     */
    private $queryBuilder;

    public function setUp(): void
    {
        $this->queryBuilder = DBQueryBuilderFactory::make('database', 'mysql', ['db_name' => 'bug']);
        $this->queryBuilder->beginTransaction();

        $this->bugReportRepository = new BugReportRepository($this->queryBuilder);

        parent::setUp();
    }

    public function testItCanCreateRecordWithEntity()
    {
        $newBugReport = $this->createBugReport();

        self::assertInstanceOf(BugReport::class, $newBugReport);
        self::assertSame('Type 2', $newBugReport->getReportType());
        self::assertSame('https://testing.link.com', $newBugReport->getLink());
        self::assertSame('This is a dummy message', $newBugReport->getMessage());
        self::assertSame('email@test.com', $newBugReport->getEmail());
    }

    public function testItCanUpdateAGivenEntity()
    {
        $newBugReport = $this->createBugReport();
        $bugReport = $this->bugReportRepository->find($newBugReport->getId());
        $bugReport->setMessage('this is from update method')
            ->setLink('http://newlink.com/image.png');

        $updatedReport = $this->bugReportRepository->update($bugReport);
        self::assertInstanceOf(BugReport::class, $updatedReport);
        self::assertSame('http://newlink.com/image.png', $updatedReport->getLink());
        self::assertSame('this is from update method', $updatedReport->getMessage());
    }

    public function createBugReport(): BugReport
    {
        $bugReport = new BugReport();
        $bugReport->setReportType('Type 2')
            ->setLink('https://testing.link.com')
            ->setMessage('This is a dummy message')
            ->setEmail('email@test.com');

        return $this->bugReportRepository->create($bugReport);
    }

    public function testItCanDeleteAGivenEntity()
    {
        $newBugReport = $this->createBugReport();
        $this->bugReportRepository->delete($newBugReport);

        $bugReport = $this->bugReportRepository->find($newBugReport->getId());
        self::assertNull($bugReport);
    }

    public function testItCanFindByCriteria()
    {
        $newBugReport = $this->createBugReport();
        $report = $this->bugReportRepository->findBy([
            ['report_type', 'Type 2'],
            ['link', 'https://testing.link.com'],
            ['message', 'This is a dummy message'],
            ['email', 'email@test.com'],
        ]);

        self::assertIsArray($report);
        self::assertNotNull($report);

        $bugReport = $report[0];
        self::assertInstanceOf(BugReport::class, $bugReport);
        self::assertSame('Type 2', $bugReport->getReportType());
        self::assertSame('https://testing.link.com', $bugReport->getLink());
        self::assertSame('This is a dummy message', $bugReport->getMessage());
        self::assertSame('email@test.com', $bugReport->getEmail());
    }

    public function tearDown()
    {
        $this->queryBuilder->getConnection()->rollback();
        parent::tearDown();
    }
}
