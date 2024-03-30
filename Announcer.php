<?php
namespace GDO\DogRSS;

use GDO\Core\GDO_DBException;
use GDO\Dog\BasicAnnouncer;
use GDO\Dog\DOG_Room;

final class Announcer extends BasicAnnouncer
{

    /**
     * @throws GDO_DBException
     */
    public function announce(GDO_RSSItem $item): int
    {
        $sent = 0;
        $feed = $item->getFeed();
        $result = $feed->subscribers();
        while ($sub = $result->fetchObject())
        {
            /** @var GDO_RSSSubscription $sub **/
            $sent += $this->announceSubscription($feed, $item, $sub);
        }
        return $sent;
    }

    private function announceSubscription(GDO_RSSFeed $feed, GDO_RSSItem $item, GDO_RSSSubscription $sub): int
    {
        if ($room = $sub->getRoom())
        {
           return $this->announceToRoom($feed, $item, $room);
        }
        return $this->announceToUser($feed, $item, $sub->getDogUser(), $sub);
    }

    private function announceToRoom(GDO_RSSFeed $feed, GDO_RSSItem $item, DOG_Room $room): int
    {
        $room->send($item->render());
        return 1;
    }

    private function announceToUser(GDO_RSSFeed $feed, GDO_RSSItem $item, ?\GDO\Dog\DOG_User $getDogUser, GDO_RSSSubscription $sub)
    {
    }


}
