<main>
  <nav class="nav">
    <ul class="nav__list container">
      <?php foreach ($categories as $item): ?>
        <li class="nav__item">
          <a href="all-lots.php?category=<?$item['id']?>"><?=$item['name'];?></a>
        </li>
      <?php endforeach; ?>
    </ul>
  </nav>
  <!-- Атрибуты required в полях формы убраны, чтобы легче было проверять  правильность работы программы-->
  <?php $classname = isset($errors) ? "form--invalid" : "";?>
  <form class="form form--add-lot container <?=$classname;?>" action="add.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
      <?php $classname = isset($errors['lot-name']) ? "form__item--invalid" : "";
      $value = isset($lot['lot-name']) ? $lot['lot-name'] : "";
      $error = isset($errors['lot-name']) ? $errors['lot-name'] : "";?>
      <div class="form__item <?=$classname;?>">
        <label for="lot-name">Наименование</label>
        <input id="lot-name" type="text" name="lot-name" placeholder="Введите наименование лота" value="<?=$value;?>">
        <span class="form__error"><?=$error ?></span>
      </div>
      <?php $classname = isset($errors['category']) ? "form__item--invalid" : "";
      $value = isset($lot['category']) ? $lot['category'] : "";
      $error = isset($errors['category']) ? $errors['category'] : "";?>
      <div class="form__item <?=$classname;?>">
        <label for="category">Категория</label>
        <select id="category" name="category" required >
          <option>Выберите категорию</option>
          <?php foreach ($categories as $item): ?>
            <option value="<?=$item['id'] ?>" <?php $sel = ($value == $item['id']) ? 'selected' : "";?> <?=$sel; ?>>
                <?=$item['name']; ?></option>
          <?php endforeach; ?>
        </select>
        <span class="form__error"><?=$error ?></span>
      </div>
    </div>
    <?php $classname = isset($errors['message']) ? "form__item--invalid" : "";
    $value = isset($lot['message']) ? $lot['message'] : "";
    $error = isset($errors['message']) ? $errors['message'] : "";?>
    <div class="form__item form__item--wide <?=$classname;?>">
      <label for="message">Описание</label>
      <textarea id="message" name="message" placeholder="Напишите описание лота" ><?=$value;?></textarea>
      <span class="form__error"><?=$error ?></span>
    </div>
    <?php $classname = isset($errors['file']) ? "form__item--invalid" : "form__item--uploaded";
    $value = isset($lot['path']) ? 'img/' . $lot['path'] : "";
    $error = isset($errors['file']) ? $errors['file'] : "";?>
    <div class="form__item form__item--file <?=$classname;?>"> <!-- form__item--uploaded -->
      <label>Изображение</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="<?=$value;?>" width="113" height="113" alt="Изображение лота">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" id="photo2" value="<?=$value;?>" name="photo2">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
      </div>
    </div>
    <div class="form__container-three">
      <?php $classname = isset($errors['lot-rate']) ? "form__item--invalid" : "";
      $value = isset($lot['lot-rate']) ? $lot['lot-rate'] : "";
      $error = isset($errors['lot-rate']) ? $errors['lot-rate'] : "";?>
      <div class="form__item form__item--small <?=$classname;?>">
        <label for="lot-rate">Начальная цена</label>
        <input id="lot-rate" type="number" name="lot-rate" placeholder="0" value="<?=$value;?>">
        <span class="form__error"><?=$error ?></span>
      </div>
      <?php $classname = isset($errors['lot-step']) ? "form__item--invalid" : "";
      $value = isset($lot['lot-step']) ? $lot['lot-step'] : "";
      $error = isset($errors['lot-step']) ? $errors['lot-step'] : "";?>
      <div class="form__item form__item--small <?=$classname;?>">
        <label for="lot-step">Шаг ставки</label>
        <input id="lot-step" type="number" name="lot-step" placeholder="0" value="<?=$value;?>">
        <span class="form__error"><?=$error ?></span>
      </div>
      <?php $classname = isset($errors['lot-date']) ? "form__item--invalid" : "";
      $value = isset($lot['lot-date']) ? $lot['lot-date'] : "";
      $error = isset($errors['lot-date']) ? $errors['lot-date'] : "";?>
      <div class="form__item <?=$classname;?>">
        <label for="lot-date">Дата окончания торгов</label>
        <input class="form__input-date" id="lot-date" type="date" name="lot-date" value="<?=$value;?>">
        <span class="form__error"><?=$error ?></span>
      </div>
    </div>
    <span class="form__error form__error--bottom">
      <?php if (isset($errors)): ?>
        <div class="form__errors">
          <p>Пожалуйста, исправьте следующие ошибки в форме:</p>
          <ul>
            <?php foreach($errors as $err => $val): ?>
            <li><strong><?=$dict[$err];?>:</strong> <?=$val;?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
    </span>
    <button type="submit" class="button" name="send">Добавить лот</button>
  </form>
</main>
