<?php
namespace GDO\DogRSS;

use GDO\Core\GDO_Module;
use GDO\Dog\Dog;
use GDO\Dog\GDT_Timer;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;

final class Module_DogRSS extends GDO_Module
{

    public int $priority = 40;

    public function onInstall(): void { Install::it($this); }

    public function onLoadLanguage(): void
    {
        $this->loadLanguage('lang/dog_rss');
    }

    public function getDependencies(): array
    {
        return [
            'Dog',
            'Net',
            'News',
        ];
    }

    public function getFriendencies(): array
    {
        return [
            'DogIRC',
            'DogTelegram',
            'Mail',
            'Subscription',
        ];
    }

    public function getClasses(): array
    {
        return [
            GDO_RSSFeed::class,
            GDO_RSSItem::class,
            GDO_RSSSubscription::class,
        ];
    }

    public function onInitSidebar(): void
    {
        $bar = GDT_Page::instance()->leftBar();
        $bar->addField(GDT_Link::make('mt_dogfeed_feedadd')->href(href('DogRSS', 'FeedAdd')));
    }

    public function onModuleInit(): void
    {
        GDT_Timer::make()->command('feeds.check')->repeat()->every(600)->now();
    }

    public function clihookFeedAdded(string $id): void
    {
    }


}
