NS.namespace('ajax');
NS.ajax = (function (document, $) {
    var _validate_string = /((?!@#\$%\^&\*\~&)[à-ÿÀ-ß³²¸¨\w\s\d,.!?()]){10,}/,
        _validate_name = /^([à-ÿÀ-ß³²¸¨a-zA-Z ]{4,128})$/,
        _validate_email = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/,
        _validate_phone = /^(\+38)\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/,
        _sys = NS.system,

        errorInfo = function (status, val, id, parent) {
            var el = $('#' + id);
            if (parent)
                el.parent().removeClass('form-error-parent');
            else
                el.removeClass('form-error');
            if (status) {
                return true;
            } else {
                if (parent)
                    el.parent().addClass('form-error-parent');
                else
                    el.addClass('form-error');
                return false;
            }
        },

        validateForm = function (available_fields) {
            if (available_fields.length <= 0)
                return false;

            var current_value = '',
                current_id = '',
                current_type = '';

            for (var i = 0, max = available_fields.length; i < max; i++) {
                current_id = available_fields[i].id;
                current_value = available_fields[i].value;
                current_type = available_fields[i].type;

                switch (current_type) {
                    case 'name':
                        if (errorInfo(_validate_name.test(current_value), current_value, current_id))
                            break;
                        else
                            return false;
                    case 'text':
                        if (errorInfo(_validate_string.test(current_value), current_value, current_id)) {
                            $('#user_avatar').attr('disabled', false);
                            break;
                        } else {
                            return false;
                        }
                    case 'email':
                        if (errorInfo(_validate_email.test(current_value), current_value, current_id))
                            break;
                        else
                            return false;
                    case 'phone':
                        if (errorInfo(_validate_phone.test(current_value), current_value, current_id))
                            break;
                        else
                            return false;
                    default:
                        if (errorInfo((current_value.length > 0), current_value, current_id, true))
                            break;
                        else
                            return false;
                }
            }
            return true;
        },

        remove_element = function (el) {
            $(el).addClass('fade-out');
            setTimeout(function () {
                $(el).remove();
            }, 450);

        },

        format_message = function (type, message_text) {
            $('.main-col .col-md-8').prepend('<div class="alert ' + type + ' fade-in"> ' + message_text + '</div>');
            setTimeout(function () {
                remove_element('.alert');
            }, 2000);
        },

        format_type_message = function (status, message) {
            if (status)
                format_message('alert-success', '<strong>Success!</strong> ' + message);
            else
                format_message('alert-danger', '<strong>Error!</strong> ' + message);
        },

        format_data = function (available_fields) {
            if (available_fields.length <= 0)
                return false;

            var send_obj = {},
                id = '',
                value = '';

            for (var i = 0, max = available_fields.length; i < max; i++) {
                id = available_fields[i].id;
                value = available_fields[i].value;

                send_obj[id] = value;
            }

            return send_obj;
        },
        setToDefault = function (available_fields) {
            if (available_fields.length <= 0)
                return false;

            for (var i = 0, max = available_fields.length; i < max; i++) {
                $('#' + available_fields[i].id).val('');
            }
        },
        sendAjax = function (form) {
            var available_fields = [
                {
                    id: 'user_name',
                    value: form['user_name'].value,
                    type: 'name'
                },
                {
                    id: 'user_email',
                    value: form['user_email'].value,
                    type: 'email'
                },
                {
                    id: 'user_phone',
                    value: form['user_phone'].value,
                    type: 'phone'
                },
                {
                    id: 'user_about',
                    value: form['user_about'].value,
                    type: 'text'
                },
                {
                    id: 'user_avatar_url',
                    value: form['user_avatar_url'].value
                }
            ];

            if (!validateForm(available_fields))
                return false;

            var formatted_data = format_data(available_fields);

            formatted_data['action'] = 'addUser';

            $.ajax({
                type: "POST",
                url: 'functions.php',
                data: formatted_data,
                error: function (xhr, status, errorThrown) {
                    console.log(errorThrown)
                },
                success: function (data) {
                    var response = JSON.parse(data);
                    format_type_message(response.status, response.message);
                    setToDefault(available_fields);
                }
            });

            return false;
        },

        setImageUrl = function (image_url) {
            if (image_url.length == 0)
                return false;

            $('#user_avatar_url').val(image_url);
        },

        ajaxUploader = function (inputFile) {
            if (inputFile.length <= 0)
                return false;
            var file = new FormData();
            file.append('file', inputFile[0].files[0]);

            $.ajax({
                type: "POST",
                url: 'functions.php',
                data: file,
                processData: false,
                contentType: false,
                error: function (xhr, status, errorThrown) {
                    console.log(errorThrown)
                },
                success: function (data) {
                    var response = JSON.parse(data);
                    format_type_message(response.status, response.message);
                    if(response.status)
                        setImageUrl(response.image_url);
                }
            })

        },
        fileUploader = function () {
            var user_avatar = $('#user_avatar'),
                uploaded = false;

            if (user_avatar.length > 0) {
                user_avatar.change(function () {
                    if (user_avatar.val() && !uploaded)
                        ajaxUploader(user_avatar);
                });
            } else {
                console.log('not found');
            }

        };

    _sys.registerAutoload(fileUploader);

    return {
        sendAjax: sendAjax
    }
})(document, jQuery);