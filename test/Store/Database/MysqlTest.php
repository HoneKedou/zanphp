<?php
/**
 * Created by IntelliJ IDEA.
 * User: winglechen
 * Date: 15/12/18
 * Time: 17:20
 */
namespace Zan\Framework\Test\Store\Database;

use Zan\Framework\Network\Connection\ConnectionInitiator;
use Zan\Framework\Store\Database\Flow;
use Zan\Framework\Store\Database\Sql\SqlMapInitiator;
use Zan\Framework\Store\Database\Sql\Table;
use Zan\Framework\Testing\TaskTest;
use Zan\Framework\Store\Facade\Db;

class MysqlTest extends TaskTest
{
    public function initTask()
    {
        //sql map
        SqlMapInitiator::getInstance()->init();
        //table
        Table::getInstance()->init();
        //connection pool init
        ConnectionInitiator::getInstance()->init('connection', null);
        parent::initTask();
    }

    public function taskCRUD()
    {
        yield $this->create();
        yield $this->insert();
        yield $this->select();
        yield $this->update();
        yield $this->delete();
    }

    private function create()
    {
        $table = "market_category";
        $flow = new Flow();

        $sql = "DROP TABLE IF EXISTS $table";
        yield $flow->queryRaw($table, $sql);
        $flow = new Flow();
        $sql = "CREATE TABLE $table (
          relation_id int(10) unsigned NOT NULL AUTO_INCREMENT,
          market_id int(10) NOT NULL,
          goods_id int(10) NOT NULL,
          PRIMARY KEY (relation_id)
        ) ENGINE=InnoDB";
        yield $flow->queryRaw($table, $sql);
    }

    private function insert()
    {
        for ($i = 0; $i < 1000; $i++) {
            $sid = 'market.category.insert';
            $data = [
                'insert'=> [
                    'market_id' => 1111,
                    'goods_id' => 2222,
                ],
            ];
            $result = (yield Db::execute($sid, $data));
            $sid = 'market.category.batch_insert';
            $data = [
                'inserts' => [
                    [
                        'market_id' => 1111,
                        'goods_id' => 2222,
                    ],
                    [
                        'market_id' => 222,
                        'goods_id' => 333,
                    ],
                ],
            ];
            $result = (yield Db::execute($sid, $data));
            $this->assertTrue($result, "Db insert failed");
        }

    }

    public function select()
    {
        $sid = 'market.category.all_rows';
        $data = [
            'var'=> [
                'relation_id' => 5
            ],
            'limit' => 20,
        ];
        $result = (yield Db::execute($sid, $data));
        $this->assertEquals(count($result), 3000, "3000 records excepted");
    }

    public function update()
    {
        $sid = 'market.category.update_by_id';
        $data = [
            'data'=> [
                'market_id' => 1111,
                'goods_id' => 2222,
            ],
            'var' => [
                'relation_id' => 2
            ]
        ];
        $result = (yield Db::execute($sid, $data));
        $this->assertTrue($result, "Db update failed");
    }

    public function delete()
    {
        $sid = 'market.category.delete_all_rows';
        $result = (yield Db::execute($sid, []));
        $this->assertTrue($result, "Db delete failed");
        $sid = 'market.category.all_rows';
        $result = (yield Db::execute($sid, []));
        $this->assertEmpty($result, "Expected database cleared empty");
    }
}