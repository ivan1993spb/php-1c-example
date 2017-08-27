<?php

namespace Example\Client1C;

/**
 * Class Response Базовый класс для всех ответов от 1С
 *
 * @package Example\Client1C
 */
abstract class Response
{
    /**
     * Time when response was received
     *
     * @var int
     */
    public $time = 0;

    /**
     * @var null|\Example\Client1C\Request
     */
    public $request = null;

    /**
     * @param int $time
     * @return $this
     */
    public function attachTime($time)
    {
        $this->time = $time;
        return $this;
    }

    /**
     * now creates current time as time of request receiving
     */
    public function attachTimeNow()
    {
        $this->time = time();
        return $this;
    }

    /**
     * @param \Example\Client1C\Request $request
     * @return $this
     */
    public function attachRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    public function __toString()
    {
        return print_r($this, true);
    }
}
