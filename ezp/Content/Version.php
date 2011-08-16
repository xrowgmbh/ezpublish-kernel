<?php
/**
 * File containing the ezp\Content\Version class.
 *
 * @copyright Copyright (C) 1999-2011 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace ezp\Content;
use ezp\Base\Model,
    ezp\Base\Locale,
    ezp\Base\Observer,
    ezp\Base\Observable,
    ezp\Content,
    ezp\Content\Field\Collection,
    ezp\Persistence\Content\Version as VersionValue;

/**
 * This class represents a Content Version
 *
 *
 * @property-read int $id
 * @property-read int $versionNo
 * @property-read int $state
 * @property-read \ezp\Content $content
 * @property int $creatorId
 * @property int $created
 * @property int $modified
 * @property \ezp\Base\Locale $locale
 * @property-read ContentField[] $fields An hash structure of fields
 */
class Version extends Model implements Observer
{
    /**
     * @todo taken from eZContentObjectVersion, to be redefined
     */
    const STATUS_DRAFT = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_PENDING = 2;
    const STATUS_ARCHIVED = 3;
    const STATUS_REJECTED = 4;
    const STATUS_INTERNAL_DRAFT = 5;
    const STATUS_REPEAT = 6;
    const STATUS_QUEUED = 7;

    /**
     * @var array Readable of properties on this object
     */
    protected $readWriteProperties = array(
        'id' => false,
        'versionNo' => false,
        'creatorId' => true,
        'created' => true,
        'modified' => true,
        'locale' => true,
        'fields' => false,
        "state" => false,
        'content' => false,
    );

    /**
     * @var array Dynamic properties on this object
     */
    protected $dynamicProperties = array(
        'locale' => false,
    );

    /**
     * @var Field[]
     */
    protected $fields;

    /**
     * Content object this version is attached to.
     *
     * @var Content
     */
    protected $content;

    /**
     * Locale
     *
     * @var Locale
     */
    protected $locale;

    /**
     * Create content version based on content and content type fields objects
     *
     * @param Content $content
     */
    public function __construct( Content $content, Locale $locale )
    {
        $this->properties = new VersionValue;
        $this->content = $content;
        $this->locale = $locale;
        $this->fields = new Collection( $this );
    }

    /**
     * Called when subject has been updated
     *
     * @param Observable $subject
     * @param string $event
     * @return Version
     */
    public function update( Observable $subject, $event = 'update' )
    {
        if ( $subject instanceof Content )
        {
            return $this->notify( $event );
        }
        return $this;
    }

    /**
     * Sets the locale of the version
     *
     * @param Locale $locale
     */
    protected function setLocale( Locale $locale )
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->id . ' ('  . $this->versionNo . ')';
    }

    /**
     * Clones the version
     *
     * @return void
     */
    public function __clone()
    {
        $this->id = false;
    }
}
?>
