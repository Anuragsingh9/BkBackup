import * as React from 'react';
import {DataGrid} from '@material-ui/data-grid';
import DataTableServerSide from "../Common/DataTable/serverSidePaginated"


/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description It is a common container component for rendering Data Table of future, past and draft events list.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Props passed from parent
 * @param {Function} props.fetchList To fetch all the events
 * @param {Function} props.getRowId To fetch the unique id of the selected row
 * @param {Columns} props.columns Columns required for event list table
 * @param {Event} props.rows Data for each row of events in the event list table
 * @returns {JSX.Element}
 * @constructor
 */
const EventTable = (props) => {
    return (
        <div>
            <DataTableServerSide
                disableCheckBox={true}
                {...props}
            />
        </div>
    );
}

export default EventTable;