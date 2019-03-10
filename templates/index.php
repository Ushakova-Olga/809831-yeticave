<main class="container">
  <section class="promo">
    <h2 class="promo__title">Нужен стафф для катки?</h2>
      <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
          <?php foreach ($categories as $item): ?>
            <li class="promo__item promo__item--boards">
              <a class="promo__link" href="all-lots.php?category=<?=$item['id']?>"><?=$item['name'];?></a>
            </li>
          <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <?php foreach ($lots_list as $item): ?>
                <li class="lots__item lot">
                    <div class="lot__image">
                        <img src="<?=$item['url']; ?>" width="350" height="260" alt="">
                    </div>
                    <div class="lot__info">
                        <span class="lot__category"><?=$item['category'];?></span>
                        <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$item['id']; ?>"><?=convert_text($item['name']);?></a></h3>
                        <div class="lot__state">
                            <div class="lot__rate">
                                <span class="lot__amount"><?=rates_string($item['amount']);?></span>
                                <span class="lot__cost"><?=format_rub($item['price']);?></span>
                            </div>
                            <div class="lot__timer timer">
                                <?=seconds_free($item['date_end']);?>
                            </div>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>
