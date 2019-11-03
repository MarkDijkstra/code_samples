<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;


class Tags extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tags}}';
    }

    public function rules(){
        return [
            [['label' , 'slug' , 'language'],'string'],
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            // not needed, used languages tags instead
        ];
    }

    /**
     * get all field by id
     *
     * @param bool $id
     * @return array
     */
    public static function getTagsByIdAndLanguage($id = false , $language = false){

        if($id != false && $language != false) {

            $result = (new \yii\db\Query())
                ->select(['order' , 'group_id' , 'language' , 'label' , 'slug'])
                ->from(['tags'])
                ->where(['seo_id' => $id , 'language' => strtolower($language) ])
                ->orderBy(['order' => SORT_ASC])
                ->all();

            return $result;

        }

    }

    /**
     * This will return all fields by group_id
     *
     * @param bool $id
     * @return array
     */
    public static function getTagsByGroup($id = false){

        $output =[];

        if($id != false) {

            $groupid = (new \yii\db\Query())
                ->select(['group_id'])
                ->from(['tags'])
                ->where(['seo_id' => $id])
                ->distinct()
                ->all();

            $groupid = ArrayHelper::getColumn($groupid, 'group_id');

            foreach($groupid as $group) {

                $result = (new \yii\db\Query())
                    ->select(['order', 'group_id', 'language', 'label', 'slug'])
                    ->from(['tags'])
                    ->where(['seo_id' => $id , 'group_id' => $group])
                    ->all();

                $result = ArrayHelper::index($result, 'language');

                $output[$group] = $result;

            }

            return $output;

        }

    }


}
