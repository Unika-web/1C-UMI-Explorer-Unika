<?php

require_once __DIR__ . '/const.php';

class OneS
{
    /**
     * Возвращает категории и их id
     * @param string $name
     * @param string $ext
     * @param int $page
     * @param int $perPage
     * @return array|int[]|null[]|null
     * @throws databaseException
     */
    public function getItems(string $name, string $ext, int $page = 0, int $perPage = PER_PAGE_MIN): array
    {
        $by = $_GET['by'] ?? '';
        $sort = $_GET['sort'] ?? '';

        switch ($by) {
            case SORT_BY_NAME:
                $by = 'cms3_objects.name';
                break;
            case SORT_BY_1S:
                $by = 'old_id';
                break;
            case SORT_BY_ACTIVE:
                $by = 'is_active';
                break;
            default:
                $by = 'cms3_hierarchy.id';
        }

        $connection = ConnectionPool::getInstance()->getConnection();

        $typeId = $this->getHierarchyType($connection, $name, $ext);

        $sourceId = $this->getSourceId();

        $page *= $perPage;

        if ($sort === 'desc') {
            $selectSql = <<<SQL
SELECT cms3_hierarchy.id, is_active, obj_id, cms3_objects.name, old_id, ext FROM cms3_hierarchy
LEFT JOIN cms3_objects ON cms3_objects.id = obj_id
LEFT JOIN cms3_import_relations ON new_id = cms3_hierarchy.id AND source_id = '{$sourceId}'
LEFT JOIN cms3_hierarchy_types ON cms3_hierarchy_types.id = '{$typeId}'
WHERE cms3_hierarchy.type_id = '{$typeId}' ORDER BY $by DESC LIMIT $page, $perPage
SQL;
        } else {
            $selectSql = <<<SQL
SELECT cms3_hierarchy.id, is_active, obj_id, cms3_objects.name, old_id, ext FROM cms3_hierarchy
LEFT JOIN cms3_objects ON cms3_objects.id = obj_id
LEFT JOIN cms3_import_relations ON new_id = cms3_hierarchy.id AND source_id = '{$sourceId}'
LEFT JOIN cms3_hierarchy_types ON cms3_hierarchy_types.id = '{$typeId}'
WHERE cms3_hierarchy.type_id = '{$typeId}' ORDER BY $by ASC LIMIT $page, $perPage
SQL;
        }

        return $this->getResponse($connection, (string)$selectSql);
    }

    /**
     * Возвращает количество товаров
     * @param $name - название шаблона данных (catalog)
     * @param $ext - название объекта (category|object)
     * @return int
     * @throws databaseException
     */
    public function getCountItems(string $name, string $ext): int
    {
        $connection = ConnectionPool::getInstance()->getConnection();

        $typeId = $this->getHierarchyType($connection, (string)$name, (string)$ext);

        $selectSql = <<<SQL
SELECT cms3_hierarchy.id FROM cms3_hierarchy WHERE cms3_hierarchy.type_id = '{$typeId}'
SQL;

        $result = $connection->queryResult($selectSql);
        return $result->length();
    }

    /**
     * Возвращает количество страниц
     * @param $count - количество объектов
     * @param $perPage - размер страницы
     * @return int
     */
    public function getPageCount(int $count, int $perPage): int
    {
        return ceil($count / $perPage);
    }

    /**
     * Обновляет идентификатор 1с, если ранее уже был идентификатор
     * @throws databaseException
     */
    public function updateOldId(): void
    {
        $connection = ConnectionPool::getInstance()->getConnection();

        $oldId = $connection->escape($_POST['change']);
        $newId = $connection->escape($_POST['on']);

        $sql = <<<SQL
UPDATE `cms3_import_relations` SET `old_id` = '{$newId}' WHERE `old_id` = '{$oldId}'
SQL;

        $connection->query($sql);
    }

    /**
     * Записывает объект в таблицу cms3_import_relations, для дальнейшей синхронизации с 1С
     * @throws databaseException
     */
    public function newObjRelation(): void
    {
        $connection = ConnectionPool::getInstance()->getConnection();
        $newId = $connection->escape(trim($_POST['change']));
        $oldId = $connection->escape(trim($_POST['on']));
        $sourceId = $this->getSourceId();

        $sql = <<<SQL
INSERT INTO `cms3_import_relations` (`source_id`, `old_id`, `new_id`) VALUES ('{$sourceId}', '{$oldId}', '{$newId}')
SQL;

        $connection->query($sql);
    }

    /**
     * При передаче пустого значения old_id из поля ввода, удаляет запись, если она была
     * @throws databaseException
     */
    public function removeOldId(): void
    {
        $connection = ConnectionPool::getInstance()->getConnection();
        $newId = $connection->escape(trim($_POST['change']));
        $sourceId = $this->getSourceId();

        $sql = <<<SQL
DELETE FROM `cms3_import_relations` WHERE `new_id` = '{$newId}' AND `source_id` = '{$sourceId}'
SQL;

        $connection->query($sql);
    }

    /**
     * Осуществляет поиск
     * @return array
     * @throws databaseException
     */
    public function search(): array
    {
        $connection = ConnectionPool::getInstance()->getConnection();

        $sourceId = $this->getSourceId();

        $by = $connection->escape(trim($_GET['searchBy']));

        switch ($by) {
            case SORT_BY_NAME:
                $by = 'cms3_objects.name';
                break;
            case SORT_BY_1S:
                $by = 'old_id';
                break;
            case SORT_BY_ACTIVE:
                $by = 'is_active';
                break;
            default:
                $by = 'cms3_hierarchy.id';
        }

        $search = $connection->escape(trim($_GET['search']));

        $selectSql = <<<SQL
SELECT cms3_hierarchy.id, is_active, obj_id, cms3_objects.name, old_id, ext FROM cms3_hierarchy
LEFT JOIN cms3_objects ON cms3_objects.id = obj_id
LEFT JOIN cms3_import_relations ON new_id = cms3_hierarchy.id AND source_id = '{$sourceId}'
LEFT JOIN cms3_hierarchy_types ON cms3_hierarchy_types.id = cms3_hierarchy.type_id
WHERE $by LIKE '%{$search}%' AND cms3_hierarchy_types.name = 'catalog'
SQL;

        return $this->getResponse($connection, (string)$selectSql);
    }

    /**
     * Выполняет sql запрос и возвращает массив с данными
     * @param $connection - подключение к БД
     * @param $sql - запрос
     * @return array
     */
    private function getResponse($connection, string $sql): array
    {
        $result = $connection->queryResult($sql);
        $result->setFetchType(IQueryResult::FETCH_ASSOC);

        $objList = [];
        if ($result->length() > 0) {
            while ($row = $result->fetch()) {
                $objList[] = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'old_id' => $row['old_id'],
                    'active' => $row['is_active'],
                    'ext' => $row['ext']
                ];
            }
        }

        return $objList;
    }

    /**
     * Получить идентификатор ресурса (Каждому сценарию импорта в таблице cms3_import_sources соответствует свой ресурс)
     * @param string $name - имя ресурса
     * @return bool|int - id ресурса
     */
    private function getSourceId(string $name = SOURCE_NAME): int
    {
        return umiImportRelations::getInstance()->getSourceId($name);
    }

    /**
     * Возвращает type_id страниц каталога
     * @param $connection - подключение к БД
     * @param $name
     * @param $ext
     * @return int|null
     */
    private function getHierarchyType($connection, string $name, string $ext): ?int
    {
        $selectSql = <<<SQL
SELECT `id` FROM `cms3_hierarchy_types` WHERE `name` = '{$name}' AND `ext` = '{$ext}' LIMIT 0, 1
SQL;

        $result = $connection->queryResult($selectSql);
        $result->setFetchType(IQueryResult::FETCH_ROW);
        $id = null;

        if ($result->length() > 0) {
            $fetchResult = $result->fetch();
            $id = (int)array_shift($fetchResult);
        }

        return $id;
    }
}
