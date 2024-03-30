<?php
namespace GDO\DogRSS;

use GDO\Core\GDO;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_Object;
use GDO\Core\GDT_Text;
use GDO\UI\GDT_Title;

final class GDO_RSSItem extends GDO
{

    public function gdoColumns(): array
    {
        return [
            GDT_AutoInc::make('item_id'),
            GDT_Object::make('item_feed')->table(GDO_RSSFeed::table())->notNull(),
            GDT_Title::make('item_title')->unique()->notNull(),
            GDT_Text::make('item_text')->notNull(),
            GDT_CreatedAt::make('item_created'),
        ];
    }

    public function getFeed(): GDO_RSSFeed
    {
        return $this->gdoValue('item_feed');
    }

    public function getTitle(): string
    {
        return $this->gdoVar('item_title');
    }

    public function getText(): string
    {
        return $this->gdoVar('item_text');
    }

    public function renderCLI(): string
    {
        return t('dog_feed_item', [
            $this->getID(),
            $this->getFeed()->getName(),
            $this->getTitle(),
            $this->getText(),
        ]);
    }

}
