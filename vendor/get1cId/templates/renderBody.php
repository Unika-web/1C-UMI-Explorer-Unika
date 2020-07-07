<html lang="ru">
<head>
    <!--suppress JSUnresolvedLibraryURL -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css"
          integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <!--suppress JSUnresolvedLibraryURL -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <meta charset="UTF-8"/>
    <title>1С-UMI Explorer — Unika</title>
</head>
<body class="p-3">
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <a class="mr-2 btn <?= /** @noinspection PhpUndefinedVariableInspection */
        $objName === HIERARCHY_CATEGORY && !$_GET['search'] ? 'btn-primary' : 'btn-secondary' ?>"
           href="?show=category&page=1&per-page=<?= $perPage ?>&by=<?= $by ?>&sort=<?= $sort ?>">Категории</a>

        <a class="btn <?= $objName === HIERARCHY_ITEM && !$_GET['search'] ? 'btn-primary' : 'btn-secondary' ?>"
           href="?show=item&page=1&per-page=<?= $perPage ?>&by=<?= $by ?>&sort=<?= $sort ?>">Товары</a>
    </div>
    <div class="d-flex align-items-center">
        <a class="btn btn-primary <?= !isset($_GET['page']) || $_GET['page'] <= 1 ? 'disabled' : '' ?>"
           href="?show=<?=
           $objName
           ?>&page=<?=
           isset($_GET['page']) && $_GET['page'] > 2 ? $_GET['page'] - 1 : 1
           ?>&per-page=<?=
           $perPage
           ?>&by=<?=
           $by
           ?>&sort=<?= $sort ?>">Предыдущая</a>

        <a class="ml-2 btn btn-primary <?= /** @noinspection PhpUndefinedVariableInspection */
        isset($_GET['page']) && $_GET['page'] >= $pages ? 'disabled' : '' ?>"
           href="?show=<?=
           $objName
           ?>&page=<?=
           isset($_GET['page']) && $_GET['page'] > 0 ? $_GET['page'] + 1 : 2
           ?>&per-page=<?=
           $perPage
           ?>&by=<?=
           $by
           ?>&sort=<?= $sort ?>">Следующая</a>
    </div>
</div>

<div class="row d-flex justify-content-between align-items-center mb-2">
    <div class="col-md-6">
        <form class="col-md-12 pl-0 mb-0" method="get">
            <div class="d-flex justify-content-start align-items-center">
                <div class="col-md-4 p-0">
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <select id="perPage" name="perPage" class="custom-select" required>
                        <option
                            <?= isset($_GET['per-page']) && (int)$_GET['per-page'] === PER_PAGE_MIN ? 'selected' : '' ?>
                                value="<?= PER_PAGE_MIN ?>"><?= PER_PAGE_MIN ?> на странице
                        </option>
                        <option
                            <?= isset($_GET['per-page']) && (int)$_GET['per-page'] === PER_PAGE_MIDDLE ? 'selected' : '' ?>
                                value="<?= PER_PAGE_MIDDLE ?>"><?= PER_PAGE_MIDDLE ?> на странице
                        </option>
                        <option
                            <?= isset($_GET['per-page']) && (int)$_GET['per-page'] === PER_PAGE_MAX ? 'selected' : '' ?>
                                value="<?= PER_PAGE_MAX ?>"><?= PER_PAGE_MAX ?> на странице
                        </option>
                    </select>
                </div>
                <div class="text-left col-md-3">
                    <input id="perPageSubmit" type="submit" class="btn btn-primary" value="Изменить">
                </div>
            </div>
        </form>
    </div>
    <div class="col-md-6">
        <form class="col-md-12 pr-0 mb-0" method="get" action="/get_1c_id.php">
            <div class="d-flex justify-content-between align-items-center">
                <div class="col-md-4">
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <select id="searchBy" name="searchBy" class="custom-select" required>
                        <option
                            <?= isset($_GET['searchBy']) && $_GET['searchBy'] === SORT_BY_ID ? 'selected' : '' ?>
                                value="<?= SORT_BY_ID ?>">По id
                        </option>
                        <option
                            <?= isset($_GET['searchBy']) && $_GET['searchBy'] === SORT_BY_NAME ? 'selected' : '' ?>
                                value="<?= SORT_BY_NAME ?>">По имени
                        </option>
                        <option
                            <?= isset($_GET['searchBy']) && $_GET['searchBy'] === SORT_BY_1S ? 'selected' : '' ?>
                                value="<?= SORT_BY_1S ?>">По идентификатору 1С
                        </option>
                    </select>
                </div>
                <div class="col-md-5">
                    <!--suppress HtmlFormInputWithoutLabel -->
                    <input type="text"
                           id="search"
                           name="search"
                           class="form-control"
                           required
                           value="<?= $_GET['search'] ?? '' ?>"
                           placeholder="Что искать?">
                </div>
                <div class="text-right col-md-3 pr-0">
                    <input type="submit" class="btn btn-primary" value="Поиск">
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row d-flex justify-content-between align-items-center">
    <div class="col-md-6">
        <p class="mb-0">Количество на странице: <b><?= /** @noinspection PhpUndefinedVariableInspection */
                count($items) ?></b></p>
        <p class="mb-0">Страница: <b><?= $page + 1 ?>/<?= /** @noinspection PhpUndefinedVariableInspection */
                $pages ?></b></p>
        <p>Всего объектов: <b><?= /** @noinspection PhpUndefinedVariableInspection */
                $countItems ?></b></p>
    </div>
    <form class="col-md-6" method="get">
        <!--        <p class="text-right">Сортировать:</p>-->
        <div class="d-flex justify-content-between align-items-center">
            <div class="col-md-1"></div>
            <div class="col-md-4">
                <!--suppress HtmlFormInputWithoutLabel -->
                <select id="by" name="sortBy" class="custom-select" required>
                    <option
                        <?= $by === SORT_BY_ID ? 'selected' : '' ?>
                            value="<?= SORT_BY_ID ?>">По id
                    </option>
                    <option
                        <?= $by === SORT_BY_NAME ? 'selected' : '' ?>
                            value="<?= SORT_BY_NAME ?>">По имени
                    </option>
                    <option
                        <?= $by === SORT_BY_1S ? 'selected' : '' ?>
                            value="<?= SORT_BY_1S ?>">По идентификатору 1С
                    </option>
                    <option
                        <?= $by === SORT_BY_ACTIVE ? 'selected' : '' ?>
                            value="<?= SORT_BY_ACTIVE ?>">По активности
                    </option>
                </select>
            </div>
            <div class="col-md-4">
                <!--suppress HtmlFormInputWithoutLabel -->
                <select id="sort" name="sort" class="custom-select" required>
                    <option
                        <?= $sort === 'asc' ? 'selected' : '' ?>
                            value="asc">По возрастанию
                    </option>
                    <option
                        <?= $sort === 'desc' ? 'selected' : '' ?>
                            value="desc">По убыванию
                    </option>
                </select>
            </div>
            <div class="text-right col-md-3 pr-0">
                <input id="filter" type="submit" class="btn btn-primary" value="Сортировать">
            </div>
        </div>
    </form>
</div>
<?php
require_once dirname(__FILE__) . '/table.php'; ?>

<footer class="col-md-12 bg-primary fixed-bottom d-flex justify-content-between align-items-center" style="max-height: 60px">
    <div class="col-md-4 py-2">
        <p class="text-white mb-0">Свободно распространяемая программа от Unika</p>
    </div>
    <div class="col-md-4 py-2">
        <p class="text-white mb-0 text-center">Версия 1.1</p>
    </div>
    <div class="col-md-4 text-right py-2">
        <a href="https://unikaweb.ru/"><img style="max-height: 50px" src="/vendor/get1cId/img/unika_logo.png" alt="Unika"></a>
    </div>
</footer>
<script src="/vendor/get1cId/js/scripts.js"></script>
</body>
</html>
