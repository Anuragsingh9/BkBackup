import * as React from "react";
import {DataGrid} from "@material-ui/data-grid";
import Constants from "../../../Constants";


/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a common component used to render a table structure if we need to show lots of informational
 * data in readable form.This component is currently using in event list,users list pages.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received data from the component where it is called to show them in a table
 * form. Currently it is using in event list page.
 * @param {Array} props.rows Array that consist information of a row data in the object form. eg - in event list table
 * all events data will be received as an array of object where each object contain separate event information.
 * @param {Array} props.columns Array that consist information of a column category data in the object form  to show
 * in the event list table's column.
 * @param {Function} props.disableCheckBox Function to manage rendering  checkboxes(for bulk actions) in the table.
 * @param {Function} props.selectHandler Function to listen event for a specific row in the table.
 * @returns {JSX.Element}
 * @constructor
 */
const DataTable = (props) => {
    const [page, setPage] = React.useState(10);
    const [sortModel, setSortModel] = React.useState([]);

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will show text("No data") if there is no data available in the table.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element}
     */
    function NoRowsOverlay() {
        return (
            <div
                className="noResultDiv"
                style={{
                    height: "100%",
                    alignItems: "center",
                    alignContent: "center",
                    justifyContent: "center",
                    display: "flex",
                }}
            >
                No Data
            </div>
        );
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This function will show text("No data") if there is no data available in the table.
     * <br>
     * <br>
     * This component will render when user search for specific data from search component and there is no data
     * available for the search term.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element}
     */
    function NoResultsOverlay() {
        return (
            <div
                className="noResultDiv"
                style={{
                    height: "100%",
                    alignItems: "center",
                    alignContent: "center",
                    justifyContent: "center",
                    display: "flex",
                }}
            >
                No Data
            </div>
        );
    }

    /**
     * -------------------------------------------------------------------------------------------------------------------
     * @description This method will handle the maximum height of the table based on pagination value and available data
     * in the table.
     * -------------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {Number} Table Height
     */
    const tableHeightHandler = () => {
        let tableHeight = 0;
        const additionalSpace = 37;
        if (page > 10) {
            if (props.rows.length > page) { //handle height as per page data size
                tableHeight = page * additionalSpace;
            } else { //handle height as per no of row in page data size
                tableHeight = props.rows.length * additionalSpace;
            }
        } else {
            tableHeight = page * additionalSpace;
        }
        return tableHeight + Constants.DATA_GRID.PAGINATION_DIV_SPACE;
    }
        ;
    return (
        <div
            style={{
                // maxHeight: '500px',
                width: "100%",
                height: tableHeightHandler()
            }}
        >
            <DataGrid
                density="compact"
                rows={props.rows}
                components={{NoRowsOverlay, NoResultsOverlay}}
                columns={props.columns}
                rowsPerPageOptions={[10, 20, 30, 40, 50, 60, 70, 80, 90, 100]}
                onPageSizeChange={setPage}
                pageSize={page}
                filterMode="client"
                checkboxSelection={!props.disableCheckBox}
                disableSelectionOnClick
                onSelectionModelChange={props.selectHandler}
                // sortingOrder={[null, 'desc', 'asc']}
                sortModel={sortModel}
                onSortModelChange={(model) => setSortModel(model)}
                paginationMode={"server"}
                rowCount={50}
            />
        </div>
    );
};

export default DataTable;
