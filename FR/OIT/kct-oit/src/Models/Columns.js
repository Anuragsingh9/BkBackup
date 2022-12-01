import User from '../Models/User'
import DraftEvent from '../Models/DraftEvent'

/**
 * @type {Array}
 * @property {String} field Unique uuid of for the event
 * @property {String} headerName Event's title
 * @property {String} headerAlign Event's title
 * @property {String} headerClassName Event's title
 * @property {String} sortable Event's title
 * @property {Number} width Date of the event
 * @property {Boolean} editable Start time of event
 */
// Event list columns
const Columns = [
    {
        "field": "title",
        "headerName": "Event Title",
        "width": 220,
        "editable": true
    },
    {
        "field": "organizer",
        "headerName": "Organizer",
        "width": 220,
        "editable": true
    },
    {
        "field": "type",
        "headerName": "Type",
        "width": 160
    },
    {
        "field": "date",
        "headerName": "Date",
        "width": 180
    },
    {
        "field": "start_time",
        "headerName": "Start time",
        "width": 170
    },
    {
        "field": "end_time",
        "headerName": "End Time",
        "width": 170
    },
    {
        "field": "",
        "headerName": "",
        "headerClassName": "ActionTab",
        "width": 70,
        "headerAlign": "center",
        "sortable": false
    }
]

export default Columns;

