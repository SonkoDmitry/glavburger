<?php
namespace app\extended\yiisoft\yii2\behaviors;

use yii\base\Event;

class TimestampBehavior extends \yii\behaviors\TimestampBehavior
{
    /**
     * Evaluates the attribute value and assigns it to the current attributes.
     *
     * @param Event $event
     */
    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            $value = $this->getValue($event);
            foreach ($attributes as $attribute) {
                // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
                if ((is_string($attribute) && empty($this->owner->$attribute)) || $attribute==$this->updatedAtAttribute) {
                    $this->owner->$attribute = $value;
                }
            }
        }
    }
}