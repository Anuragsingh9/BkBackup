import React, {useEffect, useState} from 'react';
import DataTableServerSide from "../../../Common/DataTable/serverSidePaginated";
import {connect, useDispatch} from "react-redux";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is for server side pagination of table in which pagination is handled by server
 * according to sending parameters
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props This component received data from the component where it is called to show them in a table
 * form.
 * @param {Array} props.columns Array that consist information of a row data in the object form. eg - in engagement list
 * table.
 * @param {Object} props.rows Data of the table which contain the records of the table to show in the table
 * @param {Array} props.rows.data Data of the single row
 * @param {Function} props.getRowId Function is used to get row id
 * @param {Object} props.rows.meta Meta data of the table which contain the total numbers of records in table
 * @param {Number} props.rows.meta.total Total numbers of records in the table
 * @param {Function} props.setRow This function give the numbers of selected rows in the table
 * @param {Boolean} props.disableColumnMenu Is used to disable column menu
 * @param {Boolean} props.disableCheckBox Is used to disable check box in the table
 * @param {Object} props.pinnedData Is used to disable check box in the table
 * @param {Boolean} props.disableRowPinned Is used to disable check box in the table
 * @returns {JSX.Element}
 * @constructor
 */
const ServerSideDataTable = (props) => {

    const dispatch = useDispatch();

    const [tableData, setTableData] = useState();
    const [pageSize, setPageSize] = useState(10);
    const [bottomRow, setBottomRow] = useState();
    const [page,setPage] = useState(1);

    useEffect(() =>{
        setTableData(props.rows)
    },[props.rows])

    useEffect(() => {
        if (props.callAPI === true){
            handleAPICall(page, pageSize)
            props.onPayloadDataChange(false)
        }
    }, [props.onPayloadDataChange])

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for clone the data for bottom pinned row
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventObj} target Object of event
     * @returns {Array}
     */
    const clone = (target) => {
        let result = [];
        target.forEach(row => {
            let tempRow = {};
            Object.keys(row).forEach(rowKey => {
                tempRow[rowKey] = row[rowKey];
            })
            result.push(tempRow);
        });
        return result;
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method used for handle the dynamic api call for server side table on page change and row per page
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page This is the current page number
     * @param {Number} pSize Numbers of row per page
     */
    const handleAPICall = (page = 1, pSize) => {
        const paginationData = {
            pagination:1,
            row_per_page:pSize,
            page:page,
        }
        const urlPayloadData = props.urlPayloadData || {};

        let data = {...urlPayloadData,...paginationData}
        dispatch(props.url(data))
            .then((res) => {
                    if (props.renderResponse) {
                        let data = props.renderResponse(clone(res.data.data))
                        setTableData({
                            links: res.data.links,
                            meta: res.data.meta,
                            data: data,
                        });
                        if (props.renderBottom) {
                            let bottomData = props.renderBottom(clone(res.data.data));
                            setBottomRow(bottomData);
                        }
                    } else {
                        setTableData(res.data)
                    }
                }
            )
            .catch(err => {
            });
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used to update the default state of server side pagination
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} page This is the current page number
     */
    const fetchDataByPage = (page) => {
        setPage(page + 1);
        handleAPICall(page + 1,pageSize);
    };

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for updating the number of data in one page
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} pSize Numbers of row per page
     */
    const handlePageSizeChange = (pSize) => {
        setPageSize(pSize);
        handleAPICall(1, pSize);
    };

    return (
        <DataTableServerSide
            columns={props.columns}
            rows={tableData?.data || []}
            getRowId={props.getRowId}
            totalItems={props.rows?.meta?.total}
            selectHandler={props.setRow}
            className="userListCustomDataGrid"
            disableColumnMenu={props.disableColumnMenu}
            disableCheckBox={props.disableCheckBox}
            onPageSizeChange={handlePageSizeChange}
            onPageChange={fetchDataByPage}
            pinnedData={bottomRow || props.pinnedData}
            disableRowPinned={props.disableRowPinned}
            setSortModel={props.setSortModel}
            sortModel={props.sortModel}
        />
    )
}

export default ServerSideDataTable;