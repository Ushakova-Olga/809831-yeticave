<main>
  <nav class="nav">
  <!--li class="nav__item nav__item--current"-->
      <ul class="nav__list container">
        <?php foreach ($categories as $item):
          $classname=($category['id']==$item['id']) ? "nav__item--current" : "";?>
          <li class="nav__item <?=$classname;?>">
            <a href="all-lots.php?category=<?=convert_text($item['id'])?>"><?=$item['name'];?></a>
          </li>
        <?php endforeach; ?>
      </ul>
  </nav>
  <div class="container">
    <section class="lots">
      <h2>Все лоты в категории <span>«<?=$category['name'];?>»</span></h2>
      <ul class="lots__list">
        <!-- Пагинация и вывод по 9 лотов сделано по своему усмотрению, переделаю после след лекции, когда об этом раскажут как правильно делать такие штуки-->
        <!-- Надо вывести по 9 лотов на странице -->
        <!--?php foreach ($lots_list as $item): ?-->
        <?php
        $start = 9*($p-1) < count($lots_list) ? 9*($p-1) : count($lots_list);
        $len = (9*($p-1)+9) < count($lots_list) ? (9*($p-1)+9) : count($lots_list);
        for($i= $start; $i< $len; $i++): $item=$lots_list[$i]; ?>
          <li class="lots__item lot">
            <div class="lot__image">
              <img src="<?=convert_text($item['url']); ?>" width="350" height="260" alt="">
            </div>
            <div class="lot__info">
              <span class="lot__category"><?=convert_text($item['category']);?></span>
              <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=convert_text($item['id']); ?>"><?=convert_text($item['name']);?></a></h3>
              <div class="lot__state">
                <div class="lot__rate">
                  <span class="lot__amount">Стартовая цена</span>
                  <span class="lot__cost"><?=format_rub(convert_text($item['price']));?></span>
                </div>
                <div class="lot__timer timer">
                  <?=seconds_tomorrow();?>
                </div>
              </div>
            </div>
          </li>
        <?php endfor; ?>
        <!--?php endforeach; ?-->
      </ul>
    </section>
    <ul class="pagination-list">
      <?php $count = count($lots_list);
      $p_1 = $p > 1 ? "": "visually-hidden";
      $p_2 = ($count / 9) > ($p) ? "": "visually-hidden";
      $p_3 = ($count / 9) > ($p+1) ? "": "visually-hidden";
      $previous = ($p > 1) ? ("all-lots.php?category=" . $category['id'] . "&p=" . ($p-1)) : "";
      $next = ($p > $count/9) ? "" : ("all-lots.php?category=" . $category['id'] . "&p=" . ($p+1));
      $value = isset($user['email']) ? $user['email'] : "";
      $error = isset($errors['email']) ? $errors['email'] : "";?>
      <li class="pagination-item pagination-item-prev"><a href="<?=$previous ?>">Назад</a></li>
      <li class="pagination-item <?=$p_1 ?>"><a href="all-lots.php?category=<?=$category['id']?>&p=<?=$p-1?>"><?=$p-1?></a></li>
      <li class="pagination-item pagination-item-active"><a><?=$p?></a></li>
      <li class="pagination-item <?=$p_2 ?>"><a href="all-lots.php?category=<?=$category['id']?>&p=<?=$p+1?>"><?=$p+1?></a></li>
      <li class="pagination-item <?=$p_3 ?>"><a href="all-lots.php?category=<?=$category['id']?>&p=<?=$p+2?>"><?=$p+2?></a></li>
      <li class="pagination-item pagination-item-next"><a href="<?=$next ?>">Вперед</a></li>
    </ul>
  </div>
</main>
