<?php
namespace GDO\DogRSS;

use GDO\DogRSS\Method\FeedAdd;

final class Install
{

    public static function it(Module_DogRSS $module): void
    {
        $input = [
            'name' => 'WeChall',
            'url' => 'https://www.wechall.net/news/feed',
        ];
        FeedAdd::make()->executeWithInputs($input, false);
    }

}