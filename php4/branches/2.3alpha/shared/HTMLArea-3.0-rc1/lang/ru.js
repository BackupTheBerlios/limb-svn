// I18N constants

// LANG: "ru", ENCODING: UTF-8 | ISO-8859-1
// Author: Mihai Bazon, http://dynarch.com/mishoo

// FOR TRANSLATORS:
//
//   1. PLEASE PUT YOUR CONTACT INFO IN THE ABOVE LINE
//      (at least a valid email address)
//      "LIMB dev team" <support@limb-project.com>
//      http://limb-project.com

//   2. PLEASE TRY TO USE UTF-8 FOR ENCODING;
//      (if this is not possible, please include a comment
//       that states what encoding is necessary.)

HTMLArea.I18N = {

  // the following should be the filename without .js extension
  // it will be used for automatically load plugin language.
  lang: "en",

  tooltips: {
    bold:           "Жирный",
    italic:         "Наклонный",
    underline:      "Подчеркнутый",
    strikethrough:  "Зачеркнутый",
    subscript:      "Нижний индекс",
    superscript:    "Верхний индекс",
    justifyleft:    "Выровнять по левому краю",
    justifycenter:  "Выровнять по центру",
    justifyright:   "Выровнять по правому краю",
    justifyfull:    "Выровнять по ширине",
    orderedlist:    "Нумерованный список",
    unorderedlist:  "Маркированный список",
    outdent:        "Уменьшить отступ",
    indent:         "Увеличить отступ",
    forecolor:      "Цвет шрифта",
    hilitecolor:    "Цвет фона",
    horizontalrule: "Горизонтальная черта",
    createlink:     "Вставить ссылку",
    insertimage:    "Вставить/изменить изображение",
    inserttable:    "Вставить таблицу",
    htmlmode:       "Переключиться в редактирование HTML кода",
    popupeditor:    "Увеличить реадктор",
    about:          "О редакторе",
    showhelp:       "Помощь",
    textindicator:  "Текущий стиль",
    undo:           "Отменить последнее действие",
    redo:           "Выполнить последнее действие",
    cut:            "Вырезать",
    copy:           "Копировать",
    paste:          "Вставить из буфера обмена",
    lefttoright:    "Направление текста слева направо",
    righttoleft:    "Направление текста справа налево"
  },

  buttons: {
    "ok":           "OK",
    "cancel":       "Отмена"
  },

  msg: {
    "Path":         "Путь",
    "TEXT_MODE":    "Вы находитесь в ТЕКСТОВОМ РЕЖИМЕ.  Чтобы переключиться в визуальный редактор нажмите кнопку [<>].",

    "IE-sucks-full-screen" :
    // translate here
    "Известны проблемы работы с Internet Explorer в полностраничном режиме" +
    "из-за ошибок браузера, которые мы не смогли обойти. У Вас может появиться 'мусор'"+
    "(лишний текст), не будут работать некоторые функции редактора и/или неожиданный откажется работать "+
    "браузер. Если вы используете Windows 9x, очень вероятно что Вам встретиться ошибка " +
    "'General Protection Fault' и Вам потребуется перезагрузить компьютер.\n\n"+
    "Вы предупреждены. Нажмите ОК если Вы желаете преключиться в полностраничный режим"
  },

  dialogs: {
    "Cancel"                                            : "Отмена",
    "Insert/Modify Link"                                : "Вставить/Изменить ссылку",
    "New window (_blank)"                               : "Новое окно (_blank)",
    "None (use implicit)"                               : "Нет (по умоланию)",
    "OK"                                                : "OK",
    "Other"                                             : "Другой",
    "Same frame (_self)"                                : "Этот же фрейм (_self)",
    "Target:"                                           : "Открыть в (target):",
    "Title (tooltip):"                                  : "Заголовок (tooltip):",
    "Top frame (_top)"                                  : "Фрейм верхнего уровня (_top)",
    "URL:"                                              : "URL:",
    "You must enter the URL where this link points to"  : "Вы должны ввести URL на который указывает данная ссылка"
  }
};
