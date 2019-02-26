<main>
  <nav class="nav">
    <ul class="nav__list container">
      <?php foreach ($categories as $item): ?>
        <li class="nav__item">
          <a href="all-lots.php?category=<?=convert_text($item['id'])?>"><?=$item['name'];?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <section class="rates container">
    <table class="rates__list">
      <?php foreach ($rates as $item): ?>
          <?php $value = '';
                $classname_item = "";
                $classname_timer = "";
                $contacts="";
          if (seconds_free($item['date_end'])) {
              $classname_item = "";
              $value = seconds_free($item['date_end']);
              $classname_timer = "timer--finishing";
          } else if (($user_id === $item['victor'])) {
              $classname_item = "rates__item--win";
              $value = "Ставка победила";
              $classname_timer = "timer--win";
              $contacts='<p> Контакты: '.$item['contacts'].'</p>';
          } else {
              $classname_item = "rates__item--end";
              $value = "Торги закончены";
              $classname_timer = "timer--end";
          };?>
        <tr class="rates__item <?=$classname_item ?>">
          <td class="rates__info">
            <div class="rates__img">
              <img src="<?=$item['url']?>" width="54" height="40" alt="">
            </div>
            <div>
              <h3 class="rates__title"><a href="lot.php?id=<?=$item['lot_id']?>"><?=$item['lot_name']?></a></h3>
              <?=$contacts;?>
            </div>
          </td>
          <td class="rates__category">
            <?=$item['category']?>
          </td>

          <td class="rates__timer">
            <div class="timer <?=$classname_timer ?>"><?=$value?></div>
          </td>
          <td class="rates__price">
            <?=number_format($item['summ'],0,'',' ').' р' ?>
          </td>
          <td class="rates__time">
            <?=rate_time($item['date_add'])?>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </section>
</main>
