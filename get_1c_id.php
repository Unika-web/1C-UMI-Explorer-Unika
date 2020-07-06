<?php

use UmiCms\System\Auth\AuthenticationException;

require_once dirname(__FILE__) . '/standalone.php';
require_once dirname(__FILE__) . '/vendor/get1cId/OneS.php';

$auth = UmiCms\Service::Auth();

try {
    $auth->loginByEnvironment();
} catch (AuthenticationException $exception) {
    $buffer->clear();
    $buffer->status('401 Unauthorized');
    $buffer->setHeader('WWW-Authenticate', 'Basic realm="UMI.CMS"');
    $buffer->push('HTTP Authenticate failed');
    $buffer->end();
}

if (!permissionsCollection::getInstance()->isSv()) {
    echo '<p>Не хватает прав доступа</p>';
    exit;
}

$sync = new OneS();

if (isset($_GET['show'])) {
    $objName = $_GET['show'] === 'category' || $_GET['show'] === '' ? HIERARCHY_CATEGORY : HIERARCHY_ITEM;
} else {
    $objName = HIERARCHY_CATEGORY;
}

$by = $_GET['by'] ?? 'id';
$sort = $_GET['sort'] ?? 'asc';

$page = isset($_GET['page']) && $_GET['page'] > 1 ? (int)$_GET['page'] - 1 : 0;
$perPage = isset($_GET['per-page']) ? (int)$_GET['per-page'] : (int)PER_PAGE_MIN;

if (
    isset($_GET['search']) &&
    $_GET['search'] &&
    isset($_GET['searchBy']) &&
    $_GET['searchBy']
) {
    $items = $sync->search();
    $countItems = count($items);
    $pages = 1;
} else {
    $items = $sync->getItems((string)HIERARCHY_NAME, (string)$objName, (int)$page, (int)$perPage);
    $countItems = $sync->getCountItems((string)HIERARCHY_NAME, (string)$objName);
    $pages = $sync->getPageCount((int)$countItems, (int)$perPage);
}

if (isset($_POST['method'])) {
    if ($_POST['method'] === 'update') {
        $sync->updateOldId();
    } elseif ($_POST['method'] === 'new') {
        $sync->newObjRelation();
    } elseif ($_POST['method'] === 'remove') {
        $sync->removeOldId();
    }
}

require_once dirname(__FILE__) . '/vendor/get1cId/templates/renderBody.php';
