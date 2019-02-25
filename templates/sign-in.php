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
  <?php $classname = isset($errors) ? "form--invalid" : "";?>
  <form class="form container <?=$classname;?>" action="sign-in.php" method="post" enctype="multipart/form-data"> <!-- form--invalid -->
    <h2>Регистрация нового аккаунта</h2>
    <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
    $value = isset($user['email']) ? $user['email'] : "";
    $error = isset($errors['email']) ? $errors['email'] : "";?>
    <div class="form__item <?=$classname;?>"> <!-- form__item--invalid -->
      <label for="email">E-mail*</label>
      <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$value;?>">
      <span class="form__error"><?=$error ?></span>
    </div>
    <?php $classname = isset($errors['password']) ? "form__item--invalid" : "";
    $value = isset($user['password']) ? $user['password'] : "";
    $error = isset($errors['password']) ? $errors['password'] : "";?>
    <div class="form__item <?=$classname;?>">
      <label for="password">Пароль*</label>
      <input id="password" type="password" name="password" placeholder="Введите пароль" value="<?=$value;?>">
      <span class="form__error"><?=$error ?></span>
    </div>
    <?php $classname = isset($errors['name']) ? "form__item--invalid" : "";
    $value = isset($user['name']) ? $user['name'] : "";
    $error = isset($errors['name']) ? $errors['name'] : "";?>
    <div class="form__item <?=$classname;?>">
      <label for="name">Имя*</label>
      <input id="name" type="text" name="name" placeholder="Введите имя" value="<?=$value;?>">
      <span class="form__error"><?=$error ?></span>
    </div>
    <?php $classname = isset($errors['message']) ? "form__item--invalid" : "";
    $value = isset($user['message']) ? $user['message'] : "";
    $error = isset($errors['message']) ? $errors['message'] : "";?>
    <div class="form__item <?=$classname;?>">
      <label for="message">Контактные данные*</label>
      <textarea id="message" name="message" placeholder="Напишите как с вами связаться" ><?=$value;?></textarea>
      <span class="form__error"><?=$error ?></span>
    </div>
    <?php $classname = isset($errors['file']) ? "form__item--invalid" : "form__item--uploaded";
    $value = isset($user['path']) ? 'img/' . $user['path'] : "";
    $error = isset($errors['file']) ? $errors['file'] : "";?>
    <div class="form__item form__item--file form__item--last">
      <label>Аватар</label>
      <div class="preview">
        <button class="preview__remove" type="button">x</button>
        <div class="preview__img">
          <img src="<?=$value;?>" width="113" height="113" alt="Ваш аватар">
        </div>
      </div>
      <div class="form__input-file">
        <input class="visually-hidden" type="file" id="photo2" name="photo2" value="<?=$value;?>">
        <label for="photo2">
          <span>+ Добавить</span>
        </label>
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
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
  </form>
</main>
