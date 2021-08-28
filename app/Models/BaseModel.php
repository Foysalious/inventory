<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

class BaseModel extends Model
{
    use HasFactory,HasRelationships;
    public static  $savedEventClass = null;
    public static $createdEventClass = null;
    public static $updatedEventClass = null;
    public static $deleteEventClass = null;

    private $blockEvent = false;

    public static function boot()
    {
        parent::boot();

        if (property_exists(new static, 'createdEventClass') && static::$createdEventClass) {
            self::created(function (BaseModel $model) {
                event(new static::$createdEventClass($model));
            });
        }
        if (property_exists(new static, 'updatedEventClass') && static::$updatedEventClass) {
            self::updated(function (BaseModel $model) {
                if (!$model->blockEvent) {
                    event(new static::$updatedEventClass(new static($model->getOriginal()), $model));
                }
                $model->setBlockEvent(false);
            });
        }
        if (property_exists(new static, 'savedEventClass') && static::$savedEventClass) {
            self::saved(function (BaseModel $model) {
                event(new static::$savedEventClass($model));
            });
        }
        if (property_exists(new static, 'deleteEventClass') && static::$deleteEventClass) {
            self::deleted(function (BaseModel $model) {
                event(new static::$deleteEventClass($model));
            });
        }
    }

    public function setBlockEvent($blockEvent)
    {
        $this->blockEvent = $blockEvent;
        return $this;
    }

    public function clean()
    {
        foreach ($this->attributes as $key => $value) {
            if (array_key_exists($key, $this->original)) {
                $this->attributes[$key] = $this->original[$key];
            }
        }
    }

    public function reload()
    {
        $instance = new static;
        $instance = $instance->newQuery()->find($this->{$this->primaryKey});
        $this->attributes = $instance->attributes;
        $this->original = $instance->original;
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public static function getTableName()
    {
        return (new static)->getTable();
    }
}
