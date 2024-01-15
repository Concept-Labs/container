<?php
namespace Cl\Container\ArrayPathIterator;

use ArrayAccess;
use Countable;
use Iterator;
use SeekableIterator;
use Serializable;
use Traversable;

interface ArrayPathIteratorInterface extends SeekableIterator, Traversable, Iterator, ArrayAccess, Serializable, Countable
{
    
}