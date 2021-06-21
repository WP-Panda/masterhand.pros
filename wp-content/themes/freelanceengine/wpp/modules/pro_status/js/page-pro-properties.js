<script>
var modalWindow = {
    _block: null,
    _win: null,
    initBlock: function () {
        _block = document.getElementById('blockscreen'); //Получаем наш блокирующий фон по ID

        //Если он не определен, то создадим его
        if (!_block) {
            var parent = document.getElementsByTagName('body')[0]; //Получим первый элемент тега body
            var obj = parent.firstChild; //Для того, чтобы вставить наш блокирующий фон в самое начало тега body
            _block = document.createElement('div'); //Создаем элемент div
            _block.id = 'blockscreen'; //Присваиваем ему наш ID
            parent.insertBefore(_block, obj); //Вставляем в начало
            _block.onclick = function () {
                modalWindow.close();
            } //Добавим обработчик события по нажатию на блокирующий экран - закрыть модальное окно.
        }
        _block.style.display = 'inline'; //Установим CSS-свойство
    },
    initWin: function (name_tmp, id, type) {
        if (id && type) {
            var data = type + '_id=' + id + '&action=get_' + type + '&type_user='+'<?=$type_user?>'

            var xhr = new XMLHttpRequest();
            xhr.open("POST", "<?= $path ?>", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send(data);
            xhr.onreadystatechange = function () {
                if (xhr.readyState > 3 && xhr.status == 200) {
                    var arr = JSON.parse(xhr.responseText)
                    console.log(arr)
                    if (type == 'status') {
                        var form = document.forms.status
                        // document.querySelector('#modal_form_status h2[name="title_status"]').innerText='Edit status:'
                        for (var k = 0; k < form.length; k++) {
                            var item = form[k]
                            switch (item.name) {
                                case 'name':
                                    item.value = arr[0][1]
                                    break
                                case 'position':
                                    var options = item.options
                                    for (var i = 0; i < options.length; i++) {
                                        if (options[i].innerText.trim() == arr[0][1]) {
                                            options[i].selected = true
                                            document.getElementById('position_old_status').value = options[i].value
                                        }
                                    }
                                    break
                                case "status_id":
                                    item.value = arr[arr.length - 1][1]
                                    break
                                case "action":
                                    item.value = 'edit_status'
                                    break
                                default: // property
                                    for (var i = 0; i < arr.length; i++) {
                                        if (arr[i]['property_id'] == item.name) {
                                            if (arr[i]['property_type'] == 0) {
                                                item.checked = parseInt(arr[i][1])
                                            } else
                                                item.value = arr[i][1]
                                        }
                                    }
                                    break
                            }
                        }
                    } else if (type == 'property') {
                        var form = document.forms.property
                        // document.querySelector('#modal_form_property h2[name="title_prop"]').innerText='Edit property:'
                        for (var k = 0; k < form.length; k++) {
                            var item = form[k]
                            switch (item.name) {
                                case 'name':
                                    item.value = arr[0]['property_name']
                                    break
                                case 'display':
                                    if (arr[0]['property_display'] == item.value) {
                                        item.checked = 1
                                    }
                                    break
                                case 'published':
                                    if (arr[0]['property_published'] == item.value) {
                                        item.checked = 1
                                    }
                                    break
                                case 'type':
                                    if (arr[0]['property_type'] == item.value) {
                                        item.checked = 1
                                    }
                                    break
                                case 'position':
                                    var options = item.options
                                    for (var i = 0; i < options.length; i++) {
                                        // if (options[i].innerText.trim() == arr[0]['property_name']) {
                                        //     options[i].selected = true
                                        //     document.getElementById('position_old_property').value = options[i].value
                                        // }
                                        if (options[i].value == arr[0]['property_position']) {
                                            options[i].selected = true
                                            document.getElementById('position_old_property').value = options[i].value
                                        }
                                    }
                                    break
                                case "property_id":
                                    item.value = arr[0]['id']
                                    break
                                case "time":
                                    if(arr[0]['option_value'] != undefined) {
                                        item.value = arr[0]['option_value']
                                        item.parentNode.setAttribute('style', 'display: inherit')
                                    }
                                    break
                                case "action":
                                    item.value = 'edit_property'
                                    break
                            }
                        }
                    } else if (type == 'additional') {
                        var form = document.forms.additional
                        // document.querySelector('#modal_form_property h2[name="title_prop"]').innerText='Edit property:'
                        for (var k = 0; k < form.length; k++) {
                            var item = form[k]
                            switch (item.name) {
                                case 'name':
                                    item.value = arr[0]['property_name']
                                    break
                                case 'display':
                                    if (arr[0]['property_display'] == item.value) {
                                        item.checked = 1
                                    }
                                    break
                                case 'published':
                                    if (arr[0]['property_published'] == item.value) {
                                        item.checked = 1
                                    }
                                    break
                                case 'type':
                                    if (arr[0]['property_type'] == item.value) {
                                        item.checked = 1
                                    }
                                    break
                                case 'position':
                                    var options = item.options
                                    for (var i = 0; i < options.length; i++) {
                                        // if (options[i].innerText.trim() == arr[0]['property_name']) {
                                        //     options[i].selected = true
                                        //     document.getElementById('position_old_property').value = options[i].value
                                        // }
                                        if (options[i].value == arr[0]['property_position']) {
                                            options[i].selected = true
                                            document.getElementById('position_old_additional').value = options[i].value
                                        }
                                    }
                                    break
                                case "property_id":
                                    item.value = arr[0]['id']
                                    break
                                case "action":
                                    item.value = 'edit_additional'
                                    break
                            }
                        }
                    }
                }
            };
        }

        _win = document.getElementById(name_tmp); //Получаем наше диалоговое окно по ID
        _win.style.display = 'inline'; //Зададим CSS-свойство

        //Установим позицию по центру экрана
        _win.style.left = '50%'; //Позиция по горизонтали
        _win.style.top = '50%'; //Позиция по вертикали

        //Выравнивание по центру путем задания отрицательных отступов
        _win.style.marginTop = -(_win.offsetHeight / 2) + 'px';
        _win.style.marginLeft = -250 + 'px';
    },
    close: function (name_tmp = '') {
        document.getElementById('blockscreen').style.display = 'none';
        // document.getElementByIdt('modalwindow').style.display = 'none';
        // document.getElementById('modal_form_create').style.display = 'none';
        if (name_tmp !== '') // закрытие через кнопку
            document.getElementById(name_tmp).style.display = 'none';
        else {
            var forms_all = document.getElementsByName('modal_form')
            forms_all.forEach(function (form, i, forms_all) {
                if (form.style.display == 'inline') {
                    // var form_active = form.childNodes
                    // form_active.forEach(function (item_form, k, form_active) {
                    //     if(item_form.localName=='form') {
                    //         for (var d = 0; d < item_form.length; d++) {
                    //             if( item_form[d].name!='position' && item_form[d].type!='hidden' && item_form[d].type!='submit')
                    //                 item_form[d].value = ''
                    //         }
                    //     }
                    // })
                    form.style.display = 'none';
                }
            })
        }
    },
    show: function (name_tmp, id, type) {
        modalWindow.initBlock();
        modalWindow.initWin(name_tmp, id, type);
    },
    get_st: function () {

    }
}//Конец описания нашего объекта

function delete_status(button) {
    var table = document.getElementsByName('status_list');
    // var value = button.parentNode.children[1].innerText;
    var value = table[0].rows[button.children["status_position"].value].cells[1].innerText;
    var result = confirm("Delete status with value - " + value + " ?")
    if (result) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "<?= $path ?>", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send('position=' + button.children["status_position"].value + '&status_id=' + button.children["status_id"].value + '&action=delete_status' + '&type_user='+'<?=$type_user?>');
        xhr.onreadystatechange = function () {
            if (xhr.readyState > 3 && xhr.status == 200) {
                document.location.href = '<?= $path ?>'
            }
        };
    }
}

function delete_property(button) {
    var table = document.getElementsByName('property_list');
    var value = table[0].rows[button.children["property_position"].value].cells[1].innerText;
    var result = confirm("Delete property with value in all statuses - " + value + " ?")
    if (result) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "<?= $path ?>", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send('position=' + button.children["property_position"].value + '&property_id=' + button.children["property_id"].value + '&action=delete_property' + '&type_user='+'<?=$type_user?>');
        xhr.onreadystatechange = function () {
            if (xhr.readyState > 3 && xhr.status == 200) {
                console.log(xhr.responseText)
                document.location.href = '<?= $path ?>'
            }
        };
    }
}

function delete_additional(button) {
    var table = document.getElementsByName('additional_list');
    var value = table[0].rows[button.children["property_position"].value].cells[1].innerText;
    var result = confirm("Delete additional with value in all statuses - " + value + " ?")
    if (result) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "<?= $path ?>", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send('position=' + button.children["property_position"].value + '&property_id=' + button.children["property_id"].value + '&action=delete_additional' + '&type_user='+'<?=$type_user?>');
        xhr.onreadystatechange = function () {
            if (xhr.readyState > 3 && xhr.status == 200) {
                console.log(xhr.responseText)
                document.location.href = '<?= $path ?>'
            }
        };
    }
}
</script>