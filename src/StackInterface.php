<?php
namespace Ctl\Container;

interface StackInterface
{
    public function push(...$values);
    public function pop();
    public function peek();
    public function isEmpty();
    public function count();
    public function contains($value);
    public function clear();
    public function toArray();
}