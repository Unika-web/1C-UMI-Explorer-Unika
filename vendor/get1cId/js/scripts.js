$(document).ready(() => {
    /**
     * Событие на кнопку "сохранить"
     */
    $('.changeId').on('click', (e) => {
        e.preventDefault();

        let oldId = $(e.target).data('old-id');
        let newOldId = $(e.target).parents('tr').find('.newId').val();
        if ($.trim(newOldId) === '') {
            let newId = $(e.target).parents('tr').find('.id').data('new-id');
            updateTable(newId, newOldId, e.target, 'remove');
        } else if ($.trim(oldId)) {
            updateTable(oldId, newOldId, e.target, 'update');
        } else {
            let newId = $(e.target).parents('tr').find('.id').data('new-id');
            updateTable(newId, newOldId, e.target, 'new');
        }

    });

    /**
     * AJAX для сохранения изменений
     * @param id
     * @param newOldId
     * @param target
     * @param method
     */
    let updateTable = (id, newOldId, target, method) => {
        $.ajax({
            type: 'post',
            url: '/get_1c_id.php',
            data: 'method=' + method + '&change=' + id + '&on=' + newOldId,
            success: () => {
                $(target).addClass('text-success');
                $(target).text('Сохранено');
                setTimeout(() => {
                    $(target).removeClass('text-success');
                    $(target).text('Сохранить');
                }, 2000);
            }
        });
    }

    /**
     * Дописывает GET параметры для сортировки
     */
    $('#filter').on('click', (e) => {
        e.preventDefault();

        let href = window.location.href.split('?')[0];
        let params = window.location.search;

        let by = $('#by option:selected').val();
        let sort = $('#sort option:selected').val();

        if (params === '') {
            params = 'by=' + by + '&sort=' + sort;
        } else {
            params = params.substr(1);
            let paramsArr = [];
            params.split('&').forEach(item => {
                paramsArr[item.split('=')[0]] = item.split('=')[1]
            });

            params = '';

            if (paramsArr['show']) {
                params += 'show=' + paramsArr['show'] + '&';
            }

            if (paramsArr['page']) {
                params += 'page=' + paramsArr['page'] + '&';
            }

            if (paramsArr['per-page']) {
                params += 'per-page=' + paramsArr['per-page'] + '&';
            }

            params += 'by=' + by + '&sort=' + sort;
        }

        window.location.href = href + '?' + params;
    });

    /**
     * Дописывает GET параметры для изменения размера страницы
     */
    $('#perPageSubmit').on('click', (e) => {
        e.preventDefault();

        let href = window.location.href.split('?')[0];
        let params = window.location.search;

        let perPage = $('#perPage').val();


        if (params === '') {
            params = 'per-page=' + perPage;
        } else {
            params = params.substr(1);
            let paramsArr = [];
            params.split('&').forEach(item => {
                paramsArr[item.split('=')[0]] = item.split('=')[1]
            });

            params = '';

            if (paramsArr['show']) {
                params += 'show=' + paramsArr['show'] + '&';
            }

            if (paramsArr['page']) {
                params += 'page=' + paramsArr['page'] + '&';
            }

            if (paramsArr['by']) {
                params += 'by=' + paramsArr['by'] + '&';
            }

            if (paramsArr['sort']) {
                params += 'sort=' + paramsArr['sotr'] + '&';
            }

            params += 'per-page=' + perPage;
        }

        window.location.href = href + '?' + params;
    });
});
