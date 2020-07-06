[![Unika](./vendor/get1cId/img/unika_logo_black.png "Unika")](https://unikaweb.ru/)
# 1C-UMI Explorer — Unika
Расширение для юми, позволяющее в удобной форме просматривать и редактировать идентификаторы 1С.  
`Свободно распространяемая программа от Unika`
![Пример вывода программы](./vendor/get1cId/img/example.png "Пример вывода программы")
## Установка
1.  Поместить все файлы в корневую папку сайта, так, чтобы файл `get_1c_id.php` был доступен по адресу `https://site.ru/get_1c_id.php`.
2.  В конец файла `.htaccess` добавить правило:  
    ```apacheconfig
    <FilesMatch "^get_1c_id\.php$">
      Allow from all
      php_flag engine on
    </FilesMatch>
    ```
## Использование
1.  Инструмент использует учетную запись и требует прав супервайзера, поэтому сначала нужно авторизоваться.
2.  Дописать после домена `/get_1c_id.php`. Ссылка должна выглядеть таким образом `https://site.ru/get_1c_id.php`.
