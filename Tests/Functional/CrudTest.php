<?php

declare(strict_types=1);

namespace Tests\Functional;

use App\Helpers\DBQueryBuilderFactory;
use App\Helpers\HttpClient;
use App\Models\BugReport;
use App\Repositories\BugReportRepository;
use PHPUnit\Framework\TestCase;

class CrudTest extends TestCase
{

    /**
     * @var BugReportRepository $repository
     * @var QueryBuilder $queryBuilder
     */
    private $repository;
    private $queryBuilder;
    private $client;

    public function setUp()
    {
        $this->queryBuilder = DBQueryBuilderFactory::make('database', 'pdo', ['db_name' => 'bug']);
        // $this->queryBuilder->beginTransaction();
        $this->repository = new BugReportRepository($this->queryBuilder);
        $this->client = new HttpClient();
        parent::setUp();
    }

    public function testItCanCreateRecordUsingPostRequest()
    {
        $postData = $this->getPostData(['add' => true]);
        $response = $this->client->post("http://192.168.64.2/bug-report-app/Src/Pages/add.php", $postData);
        $response = json_decode($response, true);
        self::assertEquals(200, $response['statusCode']);

        $result = $this->repository->findBy([
            ['report_type', 'Audio'],
            ['email', 'test@example.com'],
            ['link', 'https://example.com'],
        ]);

        $bugReport = $result[0] ?? [];
        self::assertInstanceOf(BugReport::class, $bugReport);
        self::assertSame('Audio', $bugReport->getReportType());
        self::assertSame('test@example.com', $bugReport->getEmail());
        self::assertSame('https://example.com', $bugReport->getLink());

        return $bugReport;
    }

    public function getPostData(array $option = []): array
    {
        return array_merge([
            'reportType' => 'Audio',
            'message' => 'The video on xxx has audio issues, please check and fix it',
            'email' => 'test@example.com',
            'link' => 'https://example.com',
        ], $option);
    }

    /**
     * @depends testItCanCreateRecordUsingPostRequest
     */
    public function testItCanUpdateRecordUsingPostRequest(BugReport $bugReport)
    {
        $postData = $this->getPostData([
            'update' => true,
            'message' => 'The video on PHP OOP has audio issues, please check and fix it',
            'link' => 'https://updated.com',
            'reportId' => $bugReport->getId(),
        ]);
        $response =  $this->client->post("http://192.168.64.2/bug-report-app/Src/Pages/update.php", $postData);
        $response = json_decode($response, true);
        self::assertEquals(200, $response['statusCode']);
        /**@var BugReport $result */
        $result = $this->repository->find($bugReport->getId());

        self::assertInstanceOf(BugReport::class, $result);
        self::assertSame('The video on PHP OOP has audio issues, please check and fix it', $result->getMessage());
        self::assertSame('https://updated.com', $result->getLink());

        return $bugReport;
    }

    /**
     * @depends testItCanUpdateRecordUsingPostRequest
     */
    public function testItCanDeleteRecordUsingPostRequest(BugReport $bugReport)
    {
        $postData = $this->getPostData([
            'delete' => true,
            'reportId' => $bugReport->getId(),
        ]);
        $this->client->post("http://192.168.64.2/bug-report-app/Src/Pages/delete.php", $postData);

        $result = $this->repository->find($bugReport->getId());

        self::assertNull($result);
    }

    // public function tearDown()
    // {
    //     $this->queryBuilder->rollback();

    //     parent::tearDown();
    // }
}
