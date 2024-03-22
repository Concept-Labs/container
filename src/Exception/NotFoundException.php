<?php
namespace Ctl\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;
use Ctl\Container\Exception\ContainerException;

class NotFoundException extends ContainerException 
    implements NotFoundExceptionInterface
{}