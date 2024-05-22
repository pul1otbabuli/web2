<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
    <title>Форма</title>
</head>
<body>
    <h2>Форма</h2>
    <div class="statistics">
        <h3>
            Статистика
        </h3>
        <div>
            <h4>Количество выбранных ЯП</h4>
            <ul>
                <?php
                    foreach ($fpls_count as $fpl => $fpl_count) {
                        echo "<li><p>" . $fpl . ":  <span>" . $fpl_count . "</span></p></li>";
                    }
                ?>
            </ul>
        </div>
    </div>
    <div class="form-submissions">
        <?php
            foreach ($submissions as $submission) {
                echo '
                    <div class="submission-item">
                        <form action="./admin.php" method="POST">
                            <input type="text" name="user-id" value="'. $submission['user_id'] .'" hidden>
                            <input type="checkbox" name="check-1" value="accepted" checked hidden>
                            <div>
                                <label>Имя: </label>
                                <input type="text" name="field-name-1" placeholder="Ваше имя" value="'. $submission['name'] .'">
                            </div>
                            <div>
                                <label>Телефон: </label>
                                <input type="tel" name="field-tel" placeholder="Ваш телефон" value="'. $submission['phone'] .'">
                            </div>
                            <div>
                                <label>Почта: </label>
                                <input type="email" name="field-email" placeholder="Ваша почта" value="'. $submission['email'] .'">
                            </div>
                            <div>
                                <label>Дата рождения: </label>
                                <input type="date" name="field-date" value="'. $submission['bdate'] .'">
                            </div>
                            <div>
                                <label>Gender: </label>
                                <div class="radio-field-button">
                                    <input name="radio-group-2" type="radio" value="m"';
                echo $submission['gender'] == '1'? 'checked' : '';
                echo '>
                                    <span>Мужчина</span>
                                </div>
                                <div class="radio-field-button">
                                    <input name="radio-group-2" type="radio" value="f"';
                echo $submission['gender'] == '0'? 'checked' : '';
                echo '>
                                    <span>Женщина</span>
                                </div>
                            </div>
                            <div class="select-field-outter form-field">
                                <label for="field-pl[]">Любимый язык программирования: </label>
                                <select name="field-pl[]" multiple="multiple">
                                    <option value="pascal"';
                echo in_array('pascal', $submission['fpls'])? 'selected' : '';
                echo '>Pascal</option>
                                    <option value="c"';
                echo in_array('c', $submission['fpls'])? 'selected' : '';
                echo '>C</option>
                                    <option value="cpp"';
                echo in_array('cpp', $submission['fpls'])? 'selected' : '';
                echo '>C++</option>
                                    <option value="js"';
                echo in_array('js', $submission['fpls'])? 'selected' : '';
                echo '>JavaScript</option>
                                    <option value="php"';
                echo in_array('php', $submission['fpls'])? 'selected' : '';
                echo '>PHP</option>
                                    <option value="python"';
                echo in_array('python', $submission['fpls'])? 'selected' : '';
                echo '>Python</option>
                                    <option value="java"';
                echo in_array('java', $submission['fpls'])? 'selected' : '';
                echo '>Java</option>
                                    <option value="haskel"';
                echo in_array('haskel', $submission['fpls'])? 'selected' : '';
                echo '>Haskel</option>
                                    <option value="clojure"';
                echo in_array('clojure', $submission['fpls'])? 'selected' : '';
                echo '>Clojure</option>
                                    <option value="prolog"';
                echo in_array('prolog', $submission['fpls'])? 'selected' : '';
                echo '>Prolog</option>
                                    <option value="scala"';
                echo in_array('scala', $submission['fpls'])? 'selected' : '';
                echo '>Scala</option>
                                </select>
                            </div>
                            <div>
                                <label for="field-bio">Биография: </label>
                                <textarea name="field-bio">'. $submission['bio'] .'</textarea>
                            </div>
                            <div class="submission-controls">
                                <button name="button-action" class="edit-button" type="submit" value="EDIT">Изменить</button>
                                <button name="button-action" class="delete-button" type="submit" value="DELETE">Удалить</button>
                            </div>
                        </form>
                    </div>
                ';
            }
        ?>
    </div>
</body>
</html>
