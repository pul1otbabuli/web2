<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    if (!empty($messages)) {
        print ('<div id="messages">');
        foreach ($messages as $message) {
            print ($message);
        }
        print ('</div>');
    }
    ?>
    <form action="./index.php" method="POST" id="form">
        <h2>Форма</h2>
        <label>
            ФИО:<br>
            <input name="field-name-1" placeholder="Заславец Богдан Сергеевич" type="name" <?php if ($errors['field-name-1']) {
                print 'class="error"';
            } ?> value="<?php print $values['field-name-1']; ?>">
        </label><br>

        <label>
            Телефон:
            <input name="field-tel" placeholder="79182748252" type="tel" <?php if ($errors['field-tel']) {
                print 'class="error"';
            } ?> value="<?php print $values['field-tel']; ?>">
        </label><br>
        <label>
            Email:<br>
            <input name="field-email" placeholder="bogdanzaslav@gmail.com" type="email" <?php if ($errors['field-email']) {
                print 'class="error"';
            } ?> value="<?php print $values['field-email']; ?>">
        </label><br>
        <label>
            Дата рождения:<br>
            <input name="field-date" value="2004-10-21" type="date" <?php if ($errors['field-date']) {
                print 'class="error"';
            } ?> value="<?php print $values['field-date']; ?>">
        </label>
        <label><br>
            Пол:<br>
            <label>
                <input type="radio" name="radio-group-2" value="m" <?php if ($errors['radio-group-2']) {
                    print 'class="error"';
                } ?> <?php print empty($values['radio-group-2']) ? '' : ($values['radio-group-2'] == 'm' ? 'checked' : ''); ?>>
                М
            </label>
            <label>
                <input type="radio" name="radio-group-2" value="f" <?php if ($errors['radio-group-2']) {
                    print 'class="error"';
                } ?> <?php print empty($values['radio-group-2']) ? '' : ($values['radio-group-2'] == 'f' ? 'checked' : ''); ?>>
                Ж
            </label>
        </label><br>
        <label>
            Любимый язык программирования:
            <br />
            <select name="field-pl[]" multiple="multiple" size="3" <?php if ($errors['field-pl']) {
                print 'class="error"';
            } ?>>
                <option value="pascal" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@pascal@') ? 'selected' : ''); ?>>Pascal</option>
                <option value="c" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@c@') ? 'selected' : ''); ?>>C</option>
                <option value="cpp" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@cpp@') ? 'selected' : ''); ?>>C++</option>
                <option value="js" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@js@') ? 'selected' : ''); ?>>JavaScript</option>
                <option value="php" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@php@') ? 'selected' : ''); ?>>PHP</option>
                <option value="python" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@python@') ? 'selected' : ''); ?>>Python</option>
                <option value="java" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@java@') ? 'selected' : ''); ?>>Java</option>
                <option value="haskel" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@haskel@') ? 'selected' : ''); ?>>Haskel</option>
                <option value="clojure" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@clojure@') ? 'selected' : ''); ?>>Clojure</option>
                <option value="prolog" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@prolog@') ? 'selected' : ''); ?>>Prolog</option>
                <option value="scala" <?php print empty($values['field-pl']) ? "" : (str_contains($values['field-pl'], '@skala@') ? 'selected' : ''); ?>>Scala</option>
            </select>
        </label><br>
        <label>
            Биография:<br>
            <textarea name="field-bio" <?php if ($errors['field-bio']) {
                print 'class="error"';
            } ?>
                value="<?php print $values['field-bio']; ?>"></textarea>
        </label><br>
        <label <?php if ($errors['check-1']) {
            print 'class="error"';
        } ?>>
            С контрактом ознакомлен(а):<br></label>
        <input value="accepted" type="checkbox" name="check-1">
        <br>
        <input type="submit" value="Отправить">
    </form>
</body>

</html>
