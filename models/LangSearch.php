<?php

namespace frontend\modules\lang\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\lang\models\Lang;

/**
 * LangSearch represents the model behind the search form about `common\models\Lang`.
 */
class LangSearch extends Lang
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_lang', 'active'], 'integer'],
            [['name', 'iso_code', 'language_code', 'date_format_lite', 'date_format_full'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Lang::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_lang' => $this->id_lang,
            'active' => $this->active,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'iso_code', $this->iso_code])
            ->andFilterWhere(['like', 'language_code', $this->language_code])
            ->andFilterWhere(['like', 'date_format_lite', $this->date_format_lite])
            ->andFilterWhere(['like', 'date_format_full', $this->date_format_full]);

        return $dataProvider;
    }
}
