<?php

namespace Tests\Units;

use App\Database\MySQLiConnection;
use App\Database\MySQLiQueryBuilder;
use App\Database\PDOConnection;
use App\Database\PDOQueryBuilder;
use App\Database\QueryBuilder;
use App\Helpers\Config;
use App\Helpers\DBQueryBuilderFactory;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    /**
     * @var QueryBuilder $queryBuilder
     */
    private $queryBuilder;

    public function setUp(): void
    {
        $this->queryBuilder = DBQueryBuilderFactory::make('database', 'pdo', ['db_name' => 'bug']);
        $this->queryBuilder->beginTransaction();
        parent::setUp();
    }

    // public function testBindings()
    // {
    //     $query = $this->queryBuilder->table('reports')->where('id', 7)->where('report_type', '>=', '100');
    //     self::assertIsArray($query->getPlaceHolders());
    //     self::assertIsArray($query->getBindings());
    // }

    public function testItCanCreateRecords()
    {


        $id = $this->insertIntoReports();

        self::assertNotNull($id);
    }

    public function testItCanPerformRawQuery()
    {
        $result = $this->queryBuilder->raw('SELECT * FROM reports')->get();
        self::assertNotNull($result);
    }

    public function testItCanPerformSelectQuery()
    {
        $id = $this->insertIntoReports();
        $result = $this->queryBuilder->table('reports')->select('*')->where('id', $id)->runQuery()->first();

        self::assertNotNull($result);
        self::assertSame($id, $result->id);
    }

    public function testItCanPerformSelectQueryMultipleWhereClause()
    {
        $id = $this->insertIntoReports();
        $result = $this->queryBuilder
            ->table('reports')
            ->select('*')
            ->where('id', $id)
            ->where('report_type', '=', 'Report Type 1')
            ->runQuery()
            ->first();

        self::assertNotNull($result);
        self::assertSame($id, $result->id);
        self::assertSame('Report Type 1', $result->report_type);
    }

    public function insertIntoReports()
    {
        $data = [
            'report_type' => 'Report Type 1',
            'message' => 'Some error happen',
            'email' => 'giang.vu@impl.vn',
            'link' => 'https://www.udemy.com/course/object-oriented-php-tdd-with-phpunit-from-scratch/learn/lecture/14559976#notes'
        ];

        return $this->queryBuilder->table('reports')->create($data);
    }

    public function testItCanFindById()
    {
        $id = $this->insertIntoReports();
        $result = $this->queryBuilder->table('reports')->select('*')->find($id);

        self::assertNotNull($result);
        self::assertSame($id, $result->id);
        self::assertSame('Report Type 1', $result->report_type);
    }

    public function testItCanFindOneByGivenValue()
    {
        $id = $this->insertIntoReports();
        $result = $this->queryBuilder->table('reports')->select('*')->findOneBy('report_type', 'Report Type 1');

        self::assertNotNull($result);
        self::assertSame($id, $result->id);
        self::assertSame('Report Type 1', $result->report_type);
    }

    public function testItCanUpdateGivenRecord()
    {
        $id = $this->insertIntoReports();
        $count = $this->queryBuilder->table('reports')->update([
            'report_type' => 'Report Type 2 update',
        ])->where('id', $id)
            ->runQuery()
            ->affected();
        self::assertEquals(1, $count);

        $result = $this->queryBuilder->select('*')->find($id);
        // $result = $this->queryBuilder->select('*')->findOneBy('report_type', 'Report Type 2');

        self::assertSame($id, $result->id);
        self::assertSame('Report Type 2 update', $result->report_type);
    }

    public function testItCanDeleteGivenId()
    {
        $id = $this->insertIntoReports();
        $count = $this->queryBuilder->table('reports')->delete()->where('id', $id)->runQuery()->affected();
        self::assertEquals(1, $count);

        $result = $this->queryBuilder->select('*')->find($id);
        // $result = $this->queryBuilder->select('*')->findOneBy('report_type', 'Report Type 2');

        self::assertNull($result);
    }

    public function tearDown()
    {
        $this->queryBuilder->getConnection()->rollback();
        parent::tearDown();
    }
}
