import * as React from 'react';
import {DataGrid} from '@material-ui/data-grid';
import FavStar from '../FavStar/FavStar';
import SplitButton from '../SplitButton/SplitButton'
import "./GroupList.css"
import LoadingSkeleton from '../../../Common/Loading/LoadingSkeleton';
import TableSkeleton from '../../../v4/Skeleton/TableSkeleton';

/**
 * @deprecated
 */
const columns = [
    {
        field: "Favourite",
        headerName: " ",
        width: 70,
        sortable: false,
        headerAlign: 'center',
        className: "Fav_star_col",
        align: 'center',
        renderCell: (cellValues) => {
            return (
                <FavStar />
            );
        }

    },
    {
        field: 'groupName',
        headerName: 'Group Name',
        width: 180,
        headerAlign: 'center',
        sortable: true,
        editable: false,
    },
    {
        field: 'pilot',
        headerName: 'Pilot',
        width: 190,
        headerAlign: 'center',
        sortable: false,
        editable: false,
    },
    {
        field: 'crew',
        headerName: 'Crew',
        width: 120,
        headerAlign: 'center',
        sortable: false,
        editable: false,
    },
    {
        field: 'id',
        headerName: 'Event Count',
        width: 120,
        sortable: false,
        headerAlign: 'center',
        editable: false,
    },
    {
        field: 'nextEvent',
        headerName: 'Next Event',
        width: 350,
        headerAlign: 'center',
        sortable: true,
        editable: false,
    },
    {
        field: '',
        headerName: '',
        width: 200,
        headerAlign: 'center',
        sortable: false,
        editable: false,
        align: 'center',
        renderCell: (cellValues) => {
            return (
                <>
                    <SplitButton />
                </>
            );
        }
    }
];

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is developed to show group list.
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Object that contains all group list related information and functions.
 * @param {groupListColumnObj[]} props.column Column details array
 * @param {groupObj[]} props.fetchList Function to fetch all list data.
 * @param {Function} props.onPageChange Function to fetch data according to pagination.
 * @param {groupObj[]} props.rows Group list data.
 * @param {Function} props.setSortModel Function to get sorted data.
 * @param {Number} props.totalItems Total created groups.
 * @returns {JSX.Element}
 */
const GroupListTable = (props) => {
    const [currentPage, setCurrentPage] = React.useState(0);
    const [pageSize, setPageSize] = React.useState(10);
    const [sortModel, setSortModel] = React.useState([]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This function is for show text in table when data is empty to show
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element}
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
     * @description This function is for show text in table when data is empty to show.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @returns {JSX.Element}
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
        <LoadingSkeleton loading={props?.loading} skeleton={<TableSkeleton />}>
            <div style={{height: 650, width: '100%', }} className='Group_table_common'>
                {/* <DataGrid
                rows={props.rows}
                columns={props.columns}
                pageSize={10}
                rowsPerPageOptions={[10,20,30,40,50]}
                checkboxSelection={false}
                disableSelectionOnClick
                disableColumnMenu
                sortable={false}
                // density="compact"
            /> */}

                <DataGrid
                    // density="compact"
                    rows={props.rows}
                    components={{NoRowsOverlay, NoResultsOverlay}}
                    columns={props.columns}
                    rowsPerPageOptions={[10, 20, 30, 40, 50]}
                    // onRowsPerPageChange= {(model)=>console.log('roowww',model)}
                    filterMode='client'
                    // checkboxSelection={!props.disableCheckBox}
                    disableSelectionOnClick
                    onSelectionModelChange={props.selectHandler}
                    sortable={true}
                    disableColumnMenu
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
                />

            </div>
        </LoadingSkeleton>
    );
}
export default GroupListTable;
