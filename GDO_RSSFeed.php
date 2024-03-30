<?php
namespace GDO\DogRSS;

use GDO\Core\Application;
use GDO\Core\GDO;
use GDO\Core\GDO_DBException;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_DeletedBy;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Name;
use GDO\Core\GDT_Text;
use GDO\Date\GDT_Timestamp;
use GDO\Date\Time;
use GDO\DB\Result;
use GDO\Net\GDT_Url;
use GDO\Net\HTTP;
use GDO\Net\URL;
use GDO\UI\GDT_Title;

final class GDO_RSSFeed extends GDO
{

    public function gdoCached(): bool
    {
        return false;
    }

    public function gdoColumns(): array
    {
        return [
            GDT_AutoInc::make('feed_id'),
            GDT_Name::make('feed_name'),
            GDT_Url::make('feed_url')->allowAll()->notNull(),
            GDT_Title::make('feed_title'),
            GDT_Text::make('feed_description'),
            GDT_Timestamp::make('feed_updated'),
            GDT_CreatedAt::make('feed_created'),
            GDT_CreatedBy::make('feed_creator'),
            GDT_DeletedAt::make('feed_deleted'),
            GDT_DeletedBy::make('feed_deletor'),
        ];
    }

    public function getName(): string { return $this->gdoVar('feed_name'); }

    public function getURL(): URL { return $this->gdoValue('feed_url'); }

    public function getUpdated(): ?string { return $this->gdoVar('feed_updated'); }

    public function isDeleted(): bool { return $this->gdoVar('res_deleted') !== null; }

    public function getItemCount(): int { return GDO_RSSItem::table()->countWhere("item_feed={$this->getID()}"); }

    /**
     * @throws GDO_DBException
     */
    public function checkFeed(bool $announce=false): array
    {
        $new = 0;
        $sent = 0;
        $url = $this->getURL();
        $line = HTTP::getFromURL($url->raw);
        if (preg_match_all('/<channel>.*<title>(.*)<\/title>.*<description>(.*)<\/description>/sU', $line, $matches))
        {
            $this->setVar('feed_title', $matches[1][0]);
            $this->setVar('feed_description', $matches[2][0]);
            if (preg_match_all('/<item>.*<title>(.*)<\/title>.*<description>(.*)<\/description>.*<pubDate>(.*)<\/pubDate>/sU', $line, $matches))
            {
                $this->save();
                $j = count($matches[1]) - 1;
                foreach (array_reverse($matches[1]) as $i => $title)
                {
                    $time = strtotime($matches[3][$j]);
                    if ($this->isNewTime($time))
                    {
                        $item = GDO_RSSItem::blank([
                            'item_feed' => $this->getID(),
                            'item_title' => $title,
                            'item_text' => $matches[2][$j--],
                            'item_created' => Time::getDate($time),
                        ])->save();
                        $new++;
                        $this->saveVar('feed_updated', Time::getDate($time));
                        if ($announce)
                        {
                            $sent += $this->announce($item);
                        }
                    }
                  }
            }
        }
        return [$new, $sent];
    }

    private function isNewTime(int $time): bool
    {
        $u = $this->getUpdated();
        return !$u || Time::getTimestamp($u) < $time;
    }

    /**
     * @throws GDO_DBException
     */
    public function subscribers(): Result
    {
        return GDO_RSSSubscription::subscribers($this);
    }

    /**
     * @throws GDO_DBException
     */
    private function announce(GDO_RSSItem $item): int
    {
        if (Application::instance()->isWebserver())
        {
            GDT_Hook::callWithIPC('FeedAdded', $item);
        }
        else
        {
            return (new Announcer())->announce($item);
        }
    }


}
