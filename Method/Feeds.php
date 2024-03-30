<?php
namespace GDO\DogRSS\Method;

use GDO\Core\GDT;
use GDO\Core\Method;

final class Feeds extends Method
{

    public function getCLITrigger(): string
    {
        return 'feeds';
    }

    public function execute(): GDT
    {
        // TODO: Implement execute() method.
    }
}
