import * as React from 'react';
import { DataGridPro } from '@mui/x-data-grid-pro';


/**
 * @global
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is for server side pagination of table in which pagination is handled by server
 * according to sending parameters
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received data from the component where it is called to show them in a table
 * form. currently it is using in event list page.
 * @param {Array} props.rows Array that consist information of a row data in the object form. eg - in event list table
 * all events data will be received as an array of object where each object contain separate event information.
 * @param {Array} props.columns Array that consist information of a column category data in the object form  to show
 * in the event list table's column.
 * @param {Function} props.disableCheckBox Function to manage rendering  checkboxes(for bulk actions) in the table.
 * @param {Function} props.selectHandler Function to listen event for a specific row in the table.
 * @returns {JSX.Element}
 * @constructor
 */
const DataTableServerSide = (props) => {
    const [currentPage, setCurrentPage] = React.useState(0);
    const [pageSize, setPageSize] = React.useState(10);
    const [sortModel, setSortModel] = React.useState([]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is for show text in table when data is empty to show.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     */
    function NoRowsOverlay() {
        return (
            <div className="noResultDiv" style={{
                height: "100%",
                alignItems: "center",
                alignContent: "center",
                justifyContent: "center",
                display: "flex"
            }}>
                No Data
            </div>
        );
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is for show text in table when there is no data to show.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @methods
     */
    function NoResultsOverlay() {
        return (
            <div className="noResultDiv" style={{
                height: "100%",
                alignItems: "center",
                alignContent: "center",
                justifyContent: "center",
                display: "flex"
            }}>
                No Data
            </div>
        );
    }


    return (
        <div style={{height: 454, width: '100%'}}>
            <DataGridPro
                {...props.rows}
                density="compact"
                className={props.className}
                rows={props.rows}
                getRowId={props.getRowId}
                components={{NoRowsOverlay, NoResultsOverlay}}
                columns={props.columns}
                rowsPerPageOptions={[10, 20, 30, 40, 50]}
                // onRowsPerPageChange= {(model)=>console.log('roowww',model)}
                filterMode='client'
                checkboxSelection={!props.disableCheckBox}
                disableSelectionOnClick
                disableColumnMenu
                onSelectionModelChange={props.selectHandler}
                sortingOrder={['desc', 'asc']}
                sortModel={props.sortModel}
                onSortModelChange={(model) => {
                    props.setSortModel(model)
                }
                }
                paginationMode={"server"}

                rowCount={props.totalItems}
                page={currentPage}
                // current page related keys
                onPageChange={(page) => {
                    setCurrentPage(page);
                    props.onPageChange(page);
                }}
                // page size related keys
                pageSize={pageSize}

                onPageSizeChange={(size) => {

                    setPageSize(size);
                    props.onPageSizeChange(size);
                }}
                pagination
                pinnedRows={props.pinnedData}
                experimentalFeatures={{ rowPinning: props.disableRowPinned }}
                sortingMode="server"
            />
        </div>
    );
}

export default DataTableServerSide;