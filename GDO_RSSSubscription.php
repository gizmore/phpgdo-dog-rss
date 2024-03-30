<?php
namespace GDO\DogRSS;

use GDO\Core\GDO;
use GDO\Core\GDO_DBException;
use GDO\Core\GDT_AutoInc;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_CreatedBy;
use GDO\Core\GDT_DeletedAt;
use GDO\Core\GDT_DeletedBy;
use GDO\Core\GDT_Object;
use GDO\DB\Result;
use GDO\Dog\DOG_Room;
use GDO\Dog\DOG_User;
use GDO\Dog\GDT_DogUser;
use GDO\Dog\GDT_Room;
use GDO\Subscription\GDT_SubscribeType;
use GDO\User\GDO_User;

/**
 * Subscribe to rooms or users.
 */
final class GDO_RSSSubscription extends GDO
{

    /**
     * @throws GDO_DBException
     */
    public static function subscribers(GDO_RSSFeed $feed): Result
    {
        return self::table()->select()->where("rsub_feed={$feed->getID()} OR rsub_feed IS NULL")->exec();
    }

    public function gdoColumns(): array
    {
        return [
            GDT_AutoInc::make('rsub_id'),
            GDT_Object::make('rsub_feed')->table(GDO_RSSFeed::table()), # null means all
            GDT_Room::make('rsub_room'),
            GDT_DogUser::make('rsub_user'),
            GDT_SubscribeType::make('rsub_type'),
            GDT_CreatedAt::make('rsub_created'),
            GDT_CreatedBy::make('rsub_creator'),
            GDT_DeletedAt::make('rsub_deleted'),
            GDT_DeletedBy::make('rsub_deletor'),
        ];
    }

    public function getUser(): GDO_User { return $this->getDogUser()->getGDOUser(); }

    public function getRoom(): ?DOG_Room { return $this->gdoValue('rsub_room'); }

    public function getDogUser(): ?DOG_User { return $this->gdoValue('rsub_user'); }

    public function getSubType(): string
    {
        if ($room = $this->getRoom())
        {
            return $room->getServer()->getConnector()->getSubscriberModule();
        }
        return $this->gdoVar('rsub_type');
    }

}
