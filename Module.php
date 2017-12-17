<?php

namespace frontend\modules\lang;

use Yii;
use frontend\modules\lang\models\Lang;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'frontend\modules\lang\controllers';

    public function init()
    {
        if(YII_ENV == 'test') return; //для тестового приложения отключаем.

        /*
         * Включаем перевод сообщений
         */
        Yii::$app->i18n->translations['app'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'forceTranslation' => true,
            'basePath' => '@app/common/messages',
        ];

        $url = Yii::$app->request->url;

        //Получаем список языков в виде строки
        $string_languages = '';
        foreach (Lang::getLangs() as $lang) {
            $string_languages .= $lang->iso_code . '|';
        }

        preg_match("#^/($string_languages)(.*)#", $url, $match_arr);

        //Если URL содержит указатель языка - сохраняем его в параметрах приложения и используем
        if (isset($match_arr[1]) && $match_arr[1] != '/' && $match_arr[1] != '') {
            Lang::setCurrent($match_arr[1]);

            Yii::$app->response->cookies->add(new \yii\web\Cookie([
                'name' => 'lang',
                'value' => Lang::getCurrent()->id_lang,
            ]));

            Yii::$app->language = $match_arr[1];
            Yii::$app->formatter->locale = $match_arr[1];
            Yii::$app->homeUrl = '/' . $match_arr[1];

            /*
             * Если URL не содержит указатель языка
             */
        } else {
            $url = Yii::$app->request->absoluteUrl; //Возвращает абсолютную ссылку

            $lang = Lang::getCurrent()->iso_code;

            Yii::$app->response->redirect(['lang/default/index', 'lang' => $lang, 'url' => $url], 301);
        }
    }
}
