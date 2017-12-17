<?php

namespace frontend\modules\lang\widgets;

use frontend\modules\lang\models\Lang;

class Widget extends \yii\base\Widget
{
    public $array_languages;

    public function init()
    {
        parent::init();

        //Создаем массив ссылок всех языков с соответствующими GET параметрами
        $array_lang = [];
        foreach (Lang::getLangs() as $lang) {
            if($lang->id_lang !== Lang::getCurrent()->id_lang)
                $array_lang[$lang->iso_code] = $lang->name;
        }

        $this->array_languages = $array_lang;
    }

    public function run()
    {
        return $this->render('@app/modules/lang/views/widget',[
            'current' => Lang::getCurrent(),
            'langs' => $this->array_languages
        ]);
    }
}
