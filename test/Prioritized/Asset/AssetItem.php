<?php
declare(strict_types=1);
namespace Cl\Container\Test\Prioritized\Asset;

class AsssetItem 
{
    
    public $priority;
    public $message;
    public $accumulator = [];
    public function construct(int $priority=0, string $message = '')
    {
        $this->priority = $priority;
        $this->message = $message;
    }

    public function add($something)
    {
        $this->accumulator[] = $something;
    }
}