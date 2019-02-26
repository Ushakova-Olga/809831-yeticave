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
  <form class="form container <?=$classname;?>" action="login.php" method="post">
    <h2>Вход</h2>
    <?php $classname = isset($errors['email']) ? "form__item--invalid" : "";
    $value = isset($user['email']) ? $user['email'] : "";
    $error = isset($errors['email']) ? $errors['email'] : "";?>
    <div class="form__item <?=$classname;?>">
      <label for="email">E-mail*</label>
      <input id="email" type="text" name="email" placeholder="Введите e-mail" value="<?=$value;?>">
      <span class="form__error">Введите e-mail</span>
    </div>
    <?php $classname = isset($errors['password']) ? "form__item--invalid" : "";
    $value = isset($user['password']) ? $user['password'] : "";
    $error = isset($errors['password']) ? $errors['password'] : "";?>
    <div class="form__item form__item--last <?=$classname;?>">
      <label for="password">Пароль*</label>
      <input id="password" type="password"  name="password" placeholder="Введите пароль" value="<?=$value;?>">
      <span class="form__error">Введите пароль</span>
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
    <button type="submit" class="button">Войти</button>
  </form>
</main>
