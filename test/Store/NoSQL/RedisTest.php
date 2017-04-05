<?php
namespace Zan\Framework\Test\Store\NoSQL;

use Zan\Framework\Network\Connection\ConnectionInitiator;
use Zan\Framework\Store\Facade\Cache;
use Zan\Framework\Testing\TaskTest;

/**
 * Created by PhpStorm.
 * User: marsnowxiao
 * Date: 2017/3/29
 * Time: 上午11:47
 */
class RedisTest extends TaskTest {
    public function initTask()
    {
        //connection pool init
        ConnectionInitiator::getInstance()->init('connection', null);
        parent::initTask();
    }

    public function taskSetGet()
    {
        yield Cache::set("pf.test.test", ["zan", "test"], "redisTest");
        $result = (yield Cache::get("pf.test.test", ["zan", "test"]));
        $this->assertEquals($result, "redisTest");
    }

    public function taskDel()
    {
        yield Cache::set("pf.test.test", ["zan", "test"], "redisTest");
        yield Cache::del("pf.test.test", ["zan", "test"]);
        $result = (yield Cache::get("pf.test.test", ["zan", "test"]));
        $this->assertEquals($result, null);
    }
}