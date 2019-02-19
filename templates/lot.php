<main>
    <nav class="nav">
      <ul class="nav__list container">
        <?php foreach ($categories as $item): ?>
          <li class="nav__item">
            <a href="pages/all-lots.html"><?=$item['name'];?></a>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
    <section class="lot-item container">
      <h2><?=$lot['name']?></h2>
      <div class="lot-item__content">
        <div class="lot-item__left">
          <div class="lot-item__image">
            <img src="<?=convert_text($lot['url']); ?>" width="730" height="548" alt="<?=$lot['name']?>">
          </div>
          <p class="lot-item__category">Категория: <span><?=$lot['category'] ?></span></p>
          <p class="lot-item__description"><?=$lot['description'] ?></p>
        </div>
        <div class="lot-item__right">
          <div class="lot-item__state">
            <div class="lot-item__timer timer">
                <?=seconds_tomorrow();?>
            </div>
            <div class="lot-item__cost-state">
              <div class="lot-item__rate">
                <span class="lot-item__amount">Текущая цена</span>
                <!--span class="lot-item__cost">10 999</span-->
                <span class="lot-item__cost"><?=number_format($lot['price'],0,'',' ') ?></span>
              </div>
              <div class="lot-item__min-cost">
                Мин. ставка <span><?=number_format($lot['price']+$lot['step'],0,'',' ') ?> р</span>
              </div>
            </div>
            <form class="lot-item__form" action="https://echo.htmlacademy.ru" method="post">
              <p class="lot-item__form-item form__item form__item--invalid">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" placeholder="<?=number_format($lot['price']+$lot['step'],0,'',' ') ?>">
                <span class="form__error">Введите наименование лота</span>
              </p>
              <button type="submit" class="button">Сделать ставку</button>
            </form>
          </div>
          <div class="history">
            <h3>История ставок (<span>10</span>)</h3>
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
