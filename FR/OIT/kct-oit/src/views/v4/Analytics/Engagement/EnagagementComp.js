import React, {useState, useEffect} from "react";
import AnalyticsTable from "./AnalyticsTable";
import useAnalyticsData from "../Containers/AnalyticsContainer";
import DateRangeDropdown from "../DateRangeDropdown/DateRangeDropdown";
import CustomDateRangePicker from "../DateRangePicker/CustomDateRangePicker";
import "./Engagement.css"
import _ from "lodash"
import {connect} from 'react-redux';
import groupAction from '../../../../redux/action/reduxAction/group';
import Constants from "../../../../Constants";
import SearchBar from "../../../Common/SearchBar/SearchBar";

/**
 * @class
 * @component
 * 
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This is a engagement component which hold complete analytics data for all events with some additional 
 * features of filtering.
 * ---------------------------------------------------------------------------------------------------------------------
 * 
 * @param {Object} props Object that contains redux methods and values 
 * @param {Method} props.changeDateRange Method of redux to change range dropdown value.
 * @returns {JSX.Element}
 */
let EngagementComp = (props) => {
    // const [analyticsData, setAnalyticsData] = useState(null);
    const analyticsData = useAnalyticsData(props);
    
    const [tableMetaData, setTableMetaData] = useState({
        reFetch: false,
        page: 1,
        rowPerPage: 10,
    });

    // Re-initial analytics dropdown and range picker redux value while mounting engagement component.
    useEffect(() => {
        if (_.has(props, ['changeDateRange'])) {
            props.changeDateRange(Constants.analyticsDateRange.YESTERDAY.val)
        }
    }, [])

    const callBackForReFetchAnalyticsData = () => {
        setTableMetaData({...tableMetaData, reFetch: true})
    }

    return (
        <>

            <div className="analyticsTopflexDiv">
                <div>
                    <DateRangeDropdown />
                    <CustomDateRangePicker />
                </div>
                <div>
                    <SearchBar />
                </div>
            </div>
            <AnalyticsTable
                analyticsData={analyticsData}
                // setRow={checkUserDeleteOrNot}
                setTableMetaData={setTableMetaData}
                className="analyticsCustomDataGrid"
                tableMetaData={tableMetaData}
                callBack={callBackForReFetchAnalyticsData}
                {...props}
            />
        </>
    )
}
const mapStateToProps = (state) => {
    return {
        range_picker_val: state.Group.engagement_tab_data.range_picker_val,
        searched_key: state.Group.searched_key,
    };
};

const mapDispatchToProps = (dispatch) => {
    return {
        changeDateRange: selectedDateRange => dispatch(groupAction.updateEngagementTabDropdownData(selectedDateRange)),
    }
};


EngagementComp = connect(mapStateToProps, mapDispatchToProps)(EngagementComp);
export default EngagementComp;
