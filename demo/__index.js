import GSTC from '../plugins/gantt-schedule-timeline-calendar/dist/gstc.esm.min.js';

const rowsFromDB = [
    {
        id: '1',
        label: 'Row 1',
    },
    {
        id: '2',
        label: 'Row 2',
    },
];

const itemsFromDB = [
    {
        id: '1',
        label: 'Item 1',
        rowId: '1',
        time: {
            start: GSTC.api.date('2020-01-01').startOf('day').valueOf(),
            end: GSTC.api.date('2020-01-02').endOf('day').valueOf(),
        },
    },
    {
        id: '2',
        label: 'Item 2',
        rowId: '1',
        time: {
            start: GSTC.api.date('2020-02-01').startOf('day').valueOf(),
            end: GSTC.api.date('2020-02-02').endOf('day').valueOf(),
        },
    },
    {
        id: '3',
        label: 'Item 3',
        rowId: '2',
        time: {
            start: GSTC.api.date('2020-01-15').startOf('day').valueOf(),
            end: GSTC.api.date('2020-01-20').endOf('day').valueOf(),
        },
    },
];

const columnsFromDB = [
    {
        id: 'id',
        label: 'ID',
        data: ({ row }) => GSTC.api.sourceID(row.id), // show original id (not internal GSTCID)
        sortable: ({ row }) => Number(GSTC.api.sourceID(row.id)), // sort by id converted to number
        width: 80,
        header: {
            content: 'ID',
        },
    },
    {
        id: 'label',
        data: 'label',
        sortable: 'label',
        isHTML: false,
        width: 230,
        header: {
            content: 'Label',
        },
    },
];

// Configuration object
const config = {
    // for free key for your domain please visit https://gstc.neuronet.io/free-key
    // if you need commercial license please visit https://gantt-schedule-timeline-calendar.neuronet.io/pricing

    licenseKey:
        '====BEGIN LICENSE KEY====\\nZiLPlk9/lrQSTNjRdRyb0E2EJbDtTCSa3V0wEDHBaY9pES+yarblynJMNMkMjcaFv1Bid5Vgmlq5luov3kD+VWim592U/dXePpwFFsEhvLceTepQ1MftH66F8zmKaxi2KHYGFlOeCWexKo/aas8KaTW99xEYFsJT8zvfWUMywLgj4pOi932E0AZQYhispPVYcljpzkMkoQnSHZwKOZ30al98yEvHwXNNhv0Qmcs1grC9nset3+AIR72WPHdiKQGtOhfPD5Exnso3tc2DI/zX50KcUMQtOd1qqe5TWM1F0rY32UiSJNB/ChOtFD5HkGaEeuGnmEQ6R742cJhqwnXn5Q==||U2FsdGVkX18Jb05PgcQmNIztN9nNkW+U0EiwALnJhm1AqywQCetmDaTQ/1IwhxMsIesRuiV3eChv9CBH5ld6S3WqN41pfRaaj0lddXzZc+E=\\nP/yHzbVoBdOOiAANQEw6KRSvQsWFNgpZ2TVMhEMEJ2LVWW1gdVFqOS+7c7QdjEyi+QPDHdpdijcKWkh3WjC3gDix3lfJeHw1DoDl9RVpyO/YpbWf0dCj6ZOL7SvArNAOuNvLfdySbHtCorJGy6Pm/OovAf9xbR4+99XOskj4aUiazx4xvRwh1TU/epfkKhBSek2JbDqaI1QH+FpA8jZNDXc5C86PYTPYnBCydAKAwVTInQ5rEQhlGVyshnJz/07qLCwf9rMLkAxdO1/SHuUYHYG03sruImg0YdXjGaBY4q4a4ojsv5ZSIfhD0ezysuCU3TwgF2eBAJ+lIy35OjL8Zw==\\n====END LICENSE KEY====',

    list: {
        columns: {
            data: GSTC.api.fromArray(columnsFromDB),
        },
        rows: GSTC.api.fromArray(rowsFromDB),
    },
    chart: {
        items: GSTC.api.fromArray(itemsFromDB),
    },
};

// Generate GSTC state from configuration object
const state = GSTC.api.stateFromConfig(config);

// for testing
// @ts-ignore
window.state = state;

// Mount the component
const app = GSTC({
    element: document.getElementById('gstc'),
    state,
});

//for testing
// @ts-ignore
window.gstc = app;
