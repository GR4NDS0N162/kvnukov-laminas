function add_item(button)
{
    let formId = button.closest('form').getAttribute('id');
    let container = $(`#${formId} [name="list"]`);
    let template = $(`#${formId} [name="list"] span`).data('template');

    if (!container[0].hasAttribute('current-index'))
        calculateIndex(formId, container[0]);

    let currentIndex = parseInt(container[0].getAttribute('current-index'));
    container.append(template.replace(/__index__/g, currentIndex));

    container[0].setAttribute('current-index', ++currentIndex);
}

function calculateIndex(formId, containerElement)
{
    let lastInput = $(`#${formId} [name="list"] > .item:last-child input`)[0];
    let currentIndex = (!lastInput) ? 0 : parseInt(lastInput.getAttribute('name').match(/\d+(?=])/)[0]) + 1;
    containerElement.setAttribute('current-index', currentIndex);
}

const cantBeEmpty = ['edit-email-form'];

function delete_item(button)
{
    let formId = button.closest('form').getAttribute('id');
    let currentCount = $(`#${formId} [name="list"] .item`).length;

    if (!(currentCount === 1 && cantBeEmpty.indexOf(formId) !== -1))
        button.closest('.item').remove();
    else {
        let container = $(`#${formId} [name="list"]`);
        let feedback = button.previousSibling.previousSibling;
        let input = feedback.previousSibling.previousSibling;

        feedback.innerText = 'Этот список не может быть пустым.';

        if (!container[0].hasAttribute('current-index'))
            calculateIndex(formId, container[0]);

        let currentIndex = parseInt(container[0].getAttribute('current-index'));

        input.value = '';
        input.setAttribute('name', `list[${currentIndex}]`);

        container[0].setAttribute('current-index', ++currentIndex);
    }
}
