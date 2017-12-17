<?php

namespace frontend\modules\lang\models;

use Yii;
use common\models\Configuration;

/**
 * This is the model class for table "lang".
 *
 * @property integer $id_lang
 * @property string $name
 * @property integer $active
 * @property string $iso_code
 * @property string $language_code
 * @property string $date_format_lite
 * @property string $date_format_full
 * @property integer $create_at
 * @property integer $update_at
 *
 * @property ConfigurationLang[] $configurationLangs
 * @property Configuration[] $idConfigurations
 */
class Lang extends \yii\db\ActiveRecord
{
    /**
     * Переменная, для хранения текущего объекта языка
    **/
    static $current = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lang';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'iso_code', 'language_code', 'date_format_lite', 'date_format_full'], 'required'],
            [['active', 'create_at', 'update_at'], 'integer'],
            [['name', 'date_format_lite', 'date_format_full'], 'string', 'max' => 32],
            [['iso_code'], 'string', 'max' => 2],
            [['language_code'], 'string', 'max' => 5],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_lang' => 'Id Lang',
            'name' => 'Name',
            'active' => 'Active',
            'iso_code' => 'Iso Code',
            'language_code' => 'Language Code',
            'date_format_lite' => 'Date Format Lite',
            'date_format_full' => 'Date Format Full',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfigurationLangs()
    {
        return $this->hasMany(ConfigurationLang::className(), ['id_lang' => 'id_lang']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdConfigurations()
    {
        return $this->hasMany(Configuration::className(), ['id_configuration' => 'id_configuration'])->viaTable('configuration_lang', ['id_lang' => 'id_lang']);
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_at', 'update_at'],
                    \yii\db\ActiveRecord::EVENT_BEFORE_UPDATE => ['update_at'],
                ],
            ],
        ];
    }

    /**
     * получаем массив со всеми активными языками
     */
    public static function getLangs()
    {
        return Lang::find()->where('`active` = 1')->all();
    }

    /**
     * Получение текущего объекта языка
     * @return array|null|\yii\db\ActiveRecord
     */
    static function getCurrent()
    {
        if( self::$current === null ){
            self::$current = self::getDefaultLang();
        }
        return self::$current;
    }

    /**
     * Установка текущего объекта языка и локаль пользователя
     * @param null $url
     */
    static function setCurrent($url = null)
    {
        $language = self::getLangByUrl($url);
        self::$current = ($language === null) ? self::getDefaultLang() : $language;
        Yii::$app->language = self::$current->language_code;
    }

    /**
     * Получения объекта языка по умолчанию
     * @return array|null|\yii\db\ActiveRecord
     */
    static function getDefaultLang()
    {
        $cookies = Yii::$app->request->cookies;
        if(($lang =$cookies->get('lang')) === null)
            $id_default_lang = Configuration::get('LANG_DEFAULT');
        else {
            $id_default_lang = $lang->value;
        }

        return Lang::find()->where('`id_lang` = :id_lang', [':id_lang' => (int)$id_default_lang])->one();
    }

    /**
     * Получения объекта языка по буквенному идентификаторуПолучения объекта языка по умолчанию
     * @param null $url
     * @return array|null|\yii\db\ActiveRecord
     */
    static function getLangByUrl($url = null)
    {
        if ($url === null) {
            return null;
        } else {
            $language = Lang::find()->where('`iso_code` = :iso_code', [':iso_code' => $url])->one();
            if ( $language === null ) {
                return null;
            }else{
                return $language;
            }
        }
    }

    /**
     * Создает URL с меткой языка
     * Разбивает URL на подмассив $match_arr
     * 0. http://site.loc/ru/contact
     * 1. http://site.loc
     * 2. ru или uk или en
     * 3. остальная часть
     */
    public static function parsingUrl($language, $url_referrer)
    {
        //Получаем список языков в виде строки
        $string_languages = '';
        foreach (self::getLangs() as $lang) {
            $string_languages .= $lang->iso_code . '|';
        }

        $host = Yii::$app->request->hostInfo;

        preg_match("#^($host)/($string_languages)(.*)#", $url_referrer, $match_arr);

        //добавляем разделитель
        if (isset($match_arr[3]) && !empty($match_arr[3]) && !preg_match('#^\/#', $match_arr[3])){
            $separator = '/';
        } else {
            $separator = '';
        }

        $match_arr[2] = '/'.$language.$separator;

        // создание нового URL
        $url = $match_arr[1].$match_arr[2].$match_arr[3];
        return $url;
    }
}
