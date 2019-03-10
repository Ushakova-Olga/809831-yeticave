<main>
    <nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $item): ?>
          <li class="nav__item">
            <a href="all-lots.php?category=<?=$item['id']?>"><?=$item['name'];?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <section class="lot-item container">
      <h2><?=convert_text($lot['name'])?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?=$lot['url']; ?>" width="730" height="548" alt="<?=convert_text($lot['name'])?>">
          </div>
          <p class="lot-item__category">Категория: <span><?=$lot['category'] ?></span></p>
          <p class="lot-item__description"><?=convert_text($lot['description']) ?></p>
        </div>
        <div class="lot-item__right">
            <?php $value = '';
                  $classname_timer = "";
            if (seconds_free($lot['date_end'])) {
                $value = seconds_free($lot['date_end']);
                $classname_timer = "timer--finishing";
            } else {
                $value = "Торги закончены";
                $classname_timer = "timer--end";
            };?>
          <div class="lot-item__state">
            <div class="lot-item__timer timer <?=$classname_timer ?>">
                <?=$value?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <span class="lot-item__cost"><?=number_format($lot['price'],0,'',' ') ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?=number_format($lot['price']+$lot['step'],0,'',' ') ?> р</span>
              </div>
            </div>
            <?php $classname = ($is_auth && (actually($lot['date_end'])) && ($user_id != $lot['user_author_id']) && (!$rate_user)) ? "" : "visually-hidden";?>
            <form class="lot-item__form <?=$classname;?>" action="lot.php?id=<?=$lot['id'];?>" method="post">
              <?php $classname = isset($errors['cost']) ? "form__item--invalid" : "";
              $value = isset($lot['cost']) ? $lot['cost'] : "";
              $error = isset($errors['cost']) ? $errors['cost'] : "";?>
              <p class="lot-item__form-item form__item <?=$classname;?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" value="<?=$value;?>" placeholder="<?=number_format($lot['price']+$lot['step'],0,'',' ') ?>">
                <span class="form__error"><?=$error ?></span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
          </div>
          <div class="history">
            <h3>История ставок (<span><?=count($rates) ?></span>)</h3>
            <table class="history__list">
                <?php foreach ($rates as $item): ?>
                    <tr class="history__item">
                        <td class="history__name"><?=$item['name'] ?></td>
                        <td class="history__price"><?=number_format($item['summ'],0,'',' ') ?></td>
                        <td class="history__time"><?=rate_time($item['date_add']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
          </div>
        </div>
      </div>
    </section>
</main>
