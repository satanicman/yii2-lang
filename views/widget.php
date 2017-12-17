<?php
    use common\models\Lang;
    use yii\helpers\Html;
?>
<div class="dropdown">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $current->name ?> <span class="caret"></span></a>
    <? if(count($langs)) : ?>
        <ul class="dropdown-menu">
            <?php foreach($langs as $iso_code => $name) : ?>
                <li><?php echo Html::a($name, ['lang/default/index', 'lang' => $iso_code], ['class' => 'language '.$iso_code] ) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>