<?php

if (!empty($items)): ?>
    <table class="table table-bordered" style="margin-bottom: 60px">
        <thead>
        <tr>
            <th scope="col">id</th>
            <th scope="col">Имя страницы</th>
            <th scope="col">Тип</th>
            <th scope="col">Идентификатор из 1С</th>
            <th scope="col">Активность</th>
            <th scope="col">Сохранение</th>
        </tr>
        </thead>
        <?php
        foreach ($items as $category):
            ?>
            <tr>
                <td data-new-id="<?= $category['id'] ?>" class="id">
                    <?= $category['id'] ?>
                </td>
                <td>
                    <a rel="nofollow noopener"
                       target="_blank"
                       href="<?= umiHierarchy::getInstance()->getPathById($category['id']) ?>">
                        <?= $category['name'] ?>
                    </a>
                </td>
                <td>
                    <span>
                        <?php
                        if ($category['ext'] === 'category'): ?>
                            Категория
                        <?php
                        elseif ($category['ext'] === 'object'): ?>
                            Товар
                        <?php
                        else: ?>
                            Не удалось определить
                        <?php
                        endif; ?>
                    </span>
                </td>
                <td>
                    <?php
                    if (isset($category['old_id'])) {
                        $value = $category['old_id'];
                    } else {
                        $value = '';
                    } ?>

                    <!--suppress HtmlFormInputWithoutLabel -->
                    <input
                            placeholder="<?= $value === '' ? 'Нет 1С идентификатора' : '1С идентификатор' ?>"
                            class="newId form-control"
                            type="text"
                            value="<?= $value ?>">
                </td>
                <td>
                    <span class="<?= (int)$category['active'] === 1 ? 'text-success' : 'text-danger'; ?>">
                        <?= (int)$category['active'] === 1 ? 'Активна' : 'Не активна'; ?>
                    </span>
                </td>
                <td class="position-relative">
                    <a data-old-id="<?= $category['old_id'] ?? '' ?>"
                       class="changeId"
                       href="#"
                       data-toggle="tooltip">
                        Сохранить
                    </a>
                </td>
            </tr>
        <?php
        endforeach; ?>
    </table>
<?php
else: ?>
    <p>Ничего не найдено :(</p>
<?php
endif;
