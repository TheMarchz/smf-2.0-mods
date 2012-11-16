<?php

$txt['bt_acp_settings_title'] = 'Настройки FXTracker';

$txt['fxt_ver'] = 'FXTracker Release 0.1 Alpha';

$txt['fxt_general'] = 'Основные настройки';
$txt['bt_enable'] = 'Включить баг-трекер
<div class="smalltext">Отключение запретит доступ к баг-трекеру всем, даже администраторам.</div>';
$txt['bt_show_button_important'] = 'Показывать количество важных записей внутри пункта меню
<div class="smalltext">Первая загрузка страницы будет медленной (из-за обработки данных).</div>';
$txt['bt_show_button_advanced'] = 'Показывать записи, требующие внимания, в виде подменю';

$txt['fxt_maintenance'] = 'Режим обслуживания
<div class="smalltext">Доступ к баг-трекеру будет только у администраторов. Be warned that the button is still there.</div>';
$txt['fxt_maintenance_enable'] = 'Эта функция позволяет активировать режим техобслуживания трекера.';
$txt['fxt_maintenance_message'] = 'Сообщение';

$txt['fxt_home'] = 'Главная страница';
$txt['bt_num_latest'] = 'Сколько последних записей выводить
<div class="smalltext">Поставьте 0 для отключения</div>';
$txt['bt_show_attention_home'] = 'Показывать записи, требующие внимания, на главной странице';

$txt['fxt_ppage'] = 'Страницы проекта';
$txt['bt_hide_done_button'] = 'Скрыть кнопку "Показать отработанные записи"
<div class="smalltext">На главной странице проекта будут отображаться все отработанные записи!</div>';
$txt['bt_hide_reject_button'] = 'Скрыть кнопку "Показать отклоненные записи"
<div class="smalltext">На главной странице проекта будут отображаться все отклоненные записи!</div>';
$txt['bt_show_description_ppage'] = 'Показывать описание проекта на его главной странице.';

$txt['fxt_notes'] = 'Комментарии';
$txt['bt_enable_notes'] = 'Разрешить комментирование записей
<div class="smalltext">Изменение этого параметра <strong>не</strong> влияет на существующие комментарии!</div>';
$txt['bt_quicknote'] = 'Включить форму быстрого комментария
<div class="smalltext">Позволяет быстрое комментирование любой записи, без загрузки новой страницы. Не рекомендуется отключать.</div>';
$txt['bt_quicknote_primary'] = 'По умолчанию комментировать с помощью формы быстрого комментария
<div class="smalltext">Опция выше должна быть включена.</div>';
$txt['bt_notes_order'] = 'Сортировка комментариев';
$txt['bt_no_asc'] = 'По возрастанию';
$txt['bt_no_desc'] = 'По убыванию';

$txt['fxt_entries'] = 'Записи';
$txt['bt_entry_progress_steps'] = 'Уровни изменения прогресса';
$txt['bt_eps_per5'] = '5 (5, 10, 15 и т. д.)';
$txt['bt_eps_per10'] = '10 (10, 20, 30 и т. д.)';

/**** PROJECT MANAGER ****/
$txt['fxt_pmanager'] = 'Управление проектами баг-трекера';
$txt['no_projects'] = 'Проектов пока нет; <a href="%s">добавьте первый</a> и начните работу!';
$txt['bt_add_project'] = 'Создать проект';
$txt['bugtracker_projects_desc'] = 'Здесь можно добавлять, редактировать и удалять проекты баг-трекера. Для редактирования проекта кликните на его названии.';
$txt['p_save_failed'] = 'Сохранение проекта не удалось.';
$txt['p_save_success'] = 'Проект сохранен!';
$txt['pedit_title'] = 'Редактирование проекта "%s"';
$txt['padd_title'] = 'Добавление проекта';
$txt['project_id'] = '#';
$txt['project_name'] = 'Название';
$txt['project_issues'] = 'Баги';
$txt['project_features'] = 'Фичи';
$txt['project_desc'] = 'Описание';
$txt['project_delete'] = 'Удалить';
$txt['project_really_delete'] = 'Хотите удалить этот проект, включая все записи и комментарии? Операция необратима, записи не будут перемещены в корзину!';
$txt['pedit_no_title'] = 'Вы не указали название проекта!';
$txt['pedit_no_desc'] = 'Описание слишком короткое!';
$txt['original_values'] = 'Оригинальные значения были восстановлены.';
$txt['oneormoreerrors'] = 'При сохранении проекта произошла одна ошибка (или несколько)';

$txt['pedit_submit'] = 'Подтвердить изменения';

$txt['fxt_post_topic'] = 'Создание темы при публикации новой записи';
$txt['enable_feature'] = 'Включить эту функцию';
$txt['fxt_place_in'] = 'Размещать тему в разделе';
$txt['fxt_show_prefix'] = 'Устанавливать префикс';
$txt['dont_show'] = 'Без префикса';
$txt['fxt_lock_topic'] = 'Блокировать тему после создания:<br />
<div class="smalltext">Только пользователи с особыми полномочиями смогут разблокировать её в дальнейшем.</div>';
$txt['fxt_topic_message'] = 'Текст темы:<br />
<div class="smalltext">Используйте %1$s для ссылки на запись, %2$s для указания автора записи и %3$s для вставки описания. Кроме того, можно использовать BB-теги.</div>';
$txt['fxt_post_topic_info'] = 'Эта функция позволяет создавать темы в выбранном разделе. Темы <strong>не</strong> будут обновляться ни после редактирования записей, ни после их комментирования, и <strong>даже при изменении типа записи</strong>.';