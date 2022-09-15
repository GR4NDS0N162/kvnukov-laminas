const formSelector = $('#AdminFilterForm');
const sortSelector = $(`#sort`);
const pageSelector = $(`#page`);

let oldWhere = formSelector.serialize();
let count;
let maxPageCount;

updateContent();

formSelector[0].addEventListener('submit', (e) =>
{
    e.preventDefault();

    oldWhere = formSelector.serialize();

    $.ajax({
        url: '/admin/list/get',
        method: 'post',
        dataType: 'json',
        async: false,
        data: getData(true),
    }).done((data) =>
    {
        count = data.count;
        maxPageCount = data.maxPageCount;

        const pageCount = Math.ceil(count / maxPageCount);

        let options = `<option value="1">1</option>`;
        for (let i = 2; i <= pageCount; i++) {
            options += `<option value="${i}">${i}</option>`;
        }
        pageSelector[0].innerHTML = options;
    });

    updateContent();
})
;

pageSelector[0].addEventListener('change', () =>
{
    updateContent();
});

sortSelector[0].addEventListener('change', () =>
{
    updateContent();
});

formSelector[0].dispatchEvent(new Event('submit'));

function getData(updatePage = false)
{
    const data = {
        where: oldWhere,
        page: pageSelector[0].value || 1,
        order: sortSelector[0].value || 'fullname',
    };

    if (updatePage) {
        data.updatePage = true;
    }

    return data;
}

function updateContent()
{
    $.ajax({
        url: '/admin/list/get',
        method: 'post',
        dataType: 'html',
        data: getData(),
    }).done((data) =>
    {
        $(`#user-list`).html(data);
    });
}