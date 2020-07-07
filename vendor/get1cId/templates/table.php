<?php

if (!empty($items)): ?>
    <table class="table table-bordered" style="margin-bottom: 60px">
        <thead>
        <tr>
            <th class="align-middle" scope="col">id</th>
            <th class="align-middle" scope="col">Имя страницы</th>
            <th class="align-middle" scope="col">Тип</th>
            <th class="align-middle" scope="col">Идентификатор из 1С</th>
            <th class="align-middle" scope="col">Активность</th>
            <th class="align-middle" scope="col">Состояние</th>
            <th class="align-middle" scope="col">Сохранение</th>
        </tr>
        </thead>
        <?php
        foreach ($items as $category): ?>
            <tr>
                <td data-new-id="<?= $category['id'] ?>" class="id align-middle">
                    <?= $category['id'] ?>
                </td>
                <td class="align-middle">
                    <a rel="nofollow noopener"
                       target="_blank"
                        <?php
                        if (umiHierarchy::getInstance()->getPathById($category['id']) !== ''): ?>
                            href="<?= umiHierarchy::getInstance()->getPathById($category['id']) ?>"
                        <?php
                        endif; ?>>
                        <?= $category['name'] ?>
                    </a>
                </td>
                <td class="align-middle">
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
                <td class="align-middle">
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
                <td class="align-middle">
                    <span class="<?= (int)$category['active'] === 1 ? 'text-success' : 'text-danger'; ?>">
                        <?= (int)$category['active'] === 1 ? 'Активна' : 'Не активна'; ?>
                    </span>
                </td>
                <td class="align-middle">
                    <span class="<?= (int)$category['deleted'] === 1 ? 'text-danger' : 'text-success'; ?>">
                        <?= (int)$category['deleted'] === 1 ? 'Корзина' : 'Каталог'; ?>
                    </span>
                </td>
                <td class="position-relative align-middle">
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
