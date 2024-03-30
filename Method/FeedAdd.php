<?php
namespace GDO\DogRSS\Method;

use GDO\Core\GDO_DBException;
use GDO\Core\GDT;
use GDO\Core\GDT_Name;
use GDO\Dog\DOG_Command;
use GDO\Dog\DOG_Message;
use GDO\DogRSS\GDO_RSSFeed;
use GDO\Net\GDT_Url;
use GDO\Net\URL;

/**
 * Add an RSS feed with $feed.add WeChallNews https://www.wechall.net/news/feed
 */
final class FeedAdd extends DOG_Command
{

    public function getCLITrigger(): string
    {
        return 'feed.add';
    }

    public function gdoParameters(): array
    {
        return [
            GDT_Name::make('name'),
            GDT_Url::make('url')->allowAll()->schemes('http', 'https')->notNull(),
        ];
    }

    /**
     * @throws GDO_DBException
     */
    public function dogExecute(DOG_Message $message, string $name, URL $url): GDT
    {
        $feed = GDO_RSSFeed::blank([
            'feed_name' => $name,
            'feed_url' => $url->raw,
        ]);

        $feed->checkFeed();

        return $this->message('msg_rssfeed_added', [$feed->getName(), $feed->getItemCount()]);
    }

}
