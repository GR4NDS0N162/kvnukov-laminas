const password_form = document.getElementById('password-form');
const password_current_field = document.getElementById('password-current-field');
const password_current_error = document.getElementById('password-current-error');
const password_new_field = document.getElementById('password-new-field');
const password_new_error = document.getElementById('password-new-error');
const password_check_field = document.getElementById('password-check-field');
const password_check_error = document.getElementById('password-check-error');

const validationEventType = 'input';

password_current_field.addEventListener(validationEventType, function (event)
{
    updateError(password_current_field, password_current_error,
        password_current_field.validity.valueMissing, 'Необходимо ввести текущий пароль.');
});

password_new_field.addEventListener(validationEventType, function (event)
{
    password_check_field.setAttribute('pattern', password_new_field.value);
    updateError(password_new_field, password_new_error,
        password_new_field.validity.valueMissing, 'Необходимо ввести новый пароль.',
        password_new_field.validity.patternMismatch, 'Введённый пароль слишком лёгкий.<br>' +
        'Введённый пароль должен удовлетворять условиям:<br>' +
        '- Цифра должна встречаться хотя бы один раз<br>' +
        '- Строчная буква должна встречаться хотя бы один раз<br>' +
        '- Заглавная буква должна встречаться хотя бы один раз<br>' +
        '- Специальный символ должен встречаться хотя бы один раз (@#$%^&+=)<br>' +
        '- Не допускаются пробелы<br>' +
        '- Количество символов - от 8 до 32)');
    updateError(password_check_field, password_check_error,
        !password_new_field.validity.valueMissing &&
        password_check_field.validity.patternMismatch, 'Пароли не совпадают.');
});

password_check_field.addEventListener(validationEventType, function (event)
{
    password_check_field.setAttribute('pattern', password_new_field.value);
    updateError(password_check_field, password_check_error,
        password_check_field.validity.valueMissing, 'Необходимо подтвердить пароль.',
        password_check_field.validity.patternMismatch, 'Пароли не совпадают.');
});

password_form.addEventListener('submit', function (event)
{
    validateForm(event, password_form,
        password_current_field, password_new_field, password_check_field);
});

const new_phone_form = document.getElementById('new-phone-form');
const new_phone_input = document.getElementById('new-phone-input');
const new_phone_submit = document.getElementById('new-phone-submit');
const new_phone_error = document.getElementById('new-phone-error');
const phones = document.getElementById('phones');

const phonePattern = '^\\+7([0-9]){10}$';
new_phone_input.setAttribute("pattern", phonePattern);

new_phone_input.addEventListener(validationEventType, function (event)
{
    updateError(new_phone_input, new_phone_error,
        new_phone_input.validity.valueMissing, 'Необходимо ввести телефонный номер.',
        new_phone_input.validity.patternMismatch, 'Введённая строка - не телефон');
});

new_phone_form.addEventListener('submit', function (event)
{
    event.preventDefault();
    new_phone_submit.blur();

    if (!new_phone_input.validity.valid) {
        new_phone_input.focus();
        return;
    }

    const phone_el = document.createElement('div');
    const phone_row_el = document.createElement('div');
    const phone_content_el = document.createElement('div');
    const phone_input_el = document.createElement('input');
    const phone_actions_el = document.createElement('div');
    const phone_edit_el = document.createElement('button');
    const phone_delete_el = document.createElement('button');
    const phone_error_el = document.createElement('div');

    phone_el.appendChild(phone_row_el);

    phone_row_el.appendChild(phone_content_el);
    phone_row_el.appendChild(phone_actions_el);
    phone_row_el.appendChild(phone_error_el);

    phone_content_el.appendChild(phone_input_el);
    phone_actions_el.appendChild(phone_edit_el);
    phone_actions_el.appendChild(phone_delete_el);

    phone_el.classList.add('col-12');
    phone_row_el.classList.add('row', 'gx-3');
    phone_content_el.classList.add('col');
    phone_actions_el.classList.add('col-auto', 'btn-group');
    phone_error_el.classList.add('col-12', 'invalid-feedback', 'd-none');
    phone_input_el.classList.add('form-control', 'phone-input');
    phone_edit_el.classList.add('btn', 'btn-outline-warning');
    phone_delete_el.classList.add('btn', 'btn-outline-danger');

    phone_input_el.type = 'tel';
    phone_input_el.value = new_phone_input.value;
    phone_input_el.setAttribute('readonly', 'readonly');
    phone_input_el.setAttribute('required', 'required');
    phone_input_el.setAttribute("pattern", phonePattern);

    phone_edit_el.type = 'button';
    phone_edit_el.innerText = 'Edit';

    phone_delete_el.type = 'button';
    phone_delete_el.innerText = 'Del';

    phones.appendChild(phone_el);

    new_phone_input.value = '';

    phone_input_el.addEventListener(validationEventType, function (event)
    {
        updateError(phone_input_el, phone_error_el,
            phone_input_el.validity.valueMissing, 'Нельзя оставлять поле пустым.',
            phone_input_el.validity.patternMismatch, 'Введённая строка - не телефон');
    });

    phone_edit_el.addEventListener('click', function (event)
    {
        if (phone_edit_el.innerText.toLowerCase() == 'edit') {
            phone_input_el.removeAttribute('readonly');
            phone_input_el.focus();
            phone_edit_el.classList.replace('btn-outline-warning', 'btn-outline-success');
            phone_edit_el.innerText = 'Save';
        } else if (phone_input_el.validity.valid) {
            phone_input_el.setAttribute('readonly', 'readonly');
            phone_edit_el.classList.replace('btn-outline-success', 'btn-outline-warning');
            phone_edit_el.innerText = 'Edit';
            phone_edit_el.blur();
        }
    });

    phone_delete_el.addEventListener('click', function (event)
    {
        phones.removeChild(phone_el);
    });
});

const phone_form_error = document.getElementById('phone-form-error');

phones.addEventListener('submit', function (event)
{
    let isFormValid = true;
    $('.phone-input').each(function ()
    {
        let attr = $(this).attr('readonly');
        isFormValid = isFormValid && (typeof attr !== 'undefined' && attr !== false);
    });
    if (isFormValid) {
        hideError(phone_form_error);
    } else {
        event.preventDefault();
        phone_form_error.innerHTML = 'Все поля должны быть сохранены.';
        phone_form_error.classList.replace('d-none', 'd-block');
    }
});

const new_email_form = document.getElementById('new-email-form');
const new_email_input = document.getElementById('new-email-input');
const new_email_submit = document.getElementById('new-email-submit');
const new_email_error = document.getElementById('new-email-error');
const emails = document.getElementById('emails');

const emailPattern = '^[a-z0-9._%+-]+@[a-z0-9.-]+\\.[a-z]{2,4}$';
new_email_input.setAttribute("pattern", emailPattern);

new_email_input.addEventListener(validationEventType, function (event)
{
    updateError(new_email_input, new_email_error,
        new_email_input.validity.valueMissing, 'Необходимо ввести электронную почту.',
        new_email_input.validity.patternMismatch, 'Введённая строка - не электронная почта');
});

new_email_form.addEventListener('submit', function (event)
{
    event.preventDefault();
    new_email_submit.blur();

    if (!new_email_input.validity.valid) {
        new_email_input.focus();
        return;
    }

    const email_el = document.createElement('div');
    const email_row_el = document.createElement('div');
    const email_content_el = document.createElement('div');
    const email_input_el = document.createElement('input');
    const email_actions_el = document.createElement('div');
    const email_edit_el = document.createElement('button');
    const email_delete_el = document.createElement('button');
    const email_error_el = document.createElement('div');

    email_el.appendChild(email_row_el);

    email_row_el.appendChild(email_content_el);
    email_row_el.appendChild(email_actions_el);
    email_row_el.appendChild(email_error_el);

    email_content_el.appendChild(email_input_el);
    email_actions_el.appendChild(email_edit_el);
    email_actions_el.appendChild(email_delete_el);

    email_el.classList.add('col-12');
    email_row_el.classList.add('row', 'gx-3');
    email_content_el.classList.add('col');
    email_actions_el.classList.add('col-auto', 'btn-group');
    email_error_el.classList.add('col-12', 'invalid-feedback', 'd-none');
    email_input_el.classList.add('form-control', 'email-input');
    email_edit_el.classList.add('btn', 'btn-outline-warning');
    email_delete_el.classList.add('btn', 'btn-outline-danger');

    email_input_el.type = 'tel';
    email_input_el.value = new_email_input.value;
    email_input_el.setAttribute('readonly', 'readonly');
    email_input_el.setAttribute('required', 'required');
    email_input_el.setAttribute("pattern", emailPattern);

    email_edit_el.type = 'button';
    email_edit_el.innerText = 'Edit';

    email_delete_el.type = 'button';
    email_delete_el.innerText = 'Del';

    emails.appendChild(email_el);

    new_email_input.value = '';

    email_input_el.addEventListener(validationEventType, function (event)
    {
        updateError(email_input_el, email_error_el,
            email_input_el.validity.valueMissing, 'Нельзя оставлять поле пустым.',
            email_input_el.validity.patternMismatch, 'Введённая строка - не электронная почта');
    });

    email_edit_el.addEventListener('click', function (event)
    {
        if (email_edit_el.innerText.toLowerCase() == 'edit') {
            email_input_el.removeAttribute('readonly');
            email_input_el.focus();
            email_edit_el.classList.replace('btn-outline-warning', 'btn-outline-success');
            email_edit_el.innerText = 'Save';
        } else if (email_input_el.validity.valid) {
            email_input_el.setAttribute('readonly', 'readonly');
            email_edit_el.classList.replace('btn-outline-success', 'btn-outline-warning');
            email_edit_el.innerText = 'Edit';
            email_edit_el.blur();
        }
    });

    email_delete_el.addEventListener('click', function (event)
    {
        emails.removeChild(email_el);
    });
});

const email_form_error = document.getElementById('email-form-error');
const allSavedError = 'Все поля должны быть сохранены.';
const atLeastOneError = 'Должен быть хотя бы один email.';

emails.addEventListener('submit', function (event)
{
    let isFormValid = true;
    let emailCount = 0;
    $('.email-input').each(function ()
    {
        ++emailCount;
        let attr = $(this).attr('readonly');
        isFormValid = isFormValid && (typeof attr !== 'undefined' && attr !== false);
    });
    if (isFormValid && emailCount) {
        hideError(email_form_error);
    } else {
        event.preventDefault();
        email_form_error.innerHTML = (emailCount) ? allSavedError : atLeastOneError;
        email_form_error.classList.replace('d-none', 'd-block');
    }
});
