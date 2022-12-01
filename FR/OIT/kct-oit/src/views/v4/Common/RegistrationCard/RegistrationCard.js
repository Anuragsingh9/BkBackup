import React, {useEffect, useState} from 'react';
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import {connect} from "react-redux";
import {Chart} from "react-google-charts";
import {Button, Grid} from "@mui/material";
import {useTranslation} from "react-i18next";
import NavigateNextIcon from '@mui/icons-material/NavigateNext';
import NavigateBeforeIcon from '@mui/icons-material/NavigateBefore';
import eventAction from "../../../../redux/action/reduxAction/event";
import Constants from "../../../../Constants";
import ChartOptionsHandler from "../AnalyticCards/ChartOptionsHandler"
import moment from "moment-timezone";
import NoDataCard from '../AnalyticCards/NoDataCard/NoDataCard';


/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description To provide the chart view for the registration and analytics data
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param props
 * @returns {JSX.Element}
 * @constructor
 */
let RegistrationCard = (props) => {

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description Default options for the registration chart
     * -----------------------------------------------------------------------------------------------------------------
     * @type {Object}
     */
    let initialOptions = ChartOptionsHandler?.barChart();

    const [regAtnChartData, setRegAtnChartData] = useState([]);
    const [regChartOption, setRegChartOption] = useState(initialOptions);
    const [showSepratorLine, setShowSepratorLine] = useState(false)
    const [showNoDataIcon, setshowNoDataIcon] = useState(true)
    const [regAtnTotal, setRegAtnTotal] = useState({
        reg: 0, attendance: 0,
        avgReg: 0, avgAttendance: 0,
    });
    const [regChartPagination, setRegChartPagination] = useState({
        enabled: false,
        currentPage: 0,
        itemPerPage: Constants.GOOGLE_CHART.DEFAULT_PAGINATION,
        totalPage: 0,
    })
    const [paginationrange, setPaginationrange] = useState({
        startDate: '',
        endDate: ''
    })
    let itemStart = (regChartPagination.itemPerPage * regChartPagination.currentPage);
    let itemEnd = itemStart + regChartPagination.itemPerPage;

    const {t} = useTranslation('common');

    useEffect(() => {
        setshowNoDataIcon(regAtnTotal?.avgReg === 0 && regAtnTotal?.avgAttendance === 0);
    }, [regAtnTotal.avgReg, regAtnTotal.avgAttendance])

    /**
     * This use effect is for handling the page wise data
     */
    useEffect(() => {
        let resultData = [];
        let itemStart = (regChartPagination.itemPerPage * regChartPagination.currentPage);
        let itemEnd = itemStart + regChartPagination.itemPerPage;

        props.recurrences_analytics?.forEach((analytics, index) => {
            if (itemStart <= index && index < itemEnd) {
                resultData.push([
                    analytics.rec_start_date?.format('MMM D'),
                    analytics.total_registration,
                    analytics.total_attendance,
                ]);
            }
        });

        updateRegChartData(resultData);
    }, [regChartPagination])

    /**
     * This use effect is to handle the recurrence data from the redux store to local state,
     * As the local state contains data only which need to show and redux store contain the data for all of the
     * recurrences
     */
    useEffect(() => {


        setRegChartPagination({
            ...regChartPagination,
            enabled: props.recurrences_analytics?.length > regChartPagination.itemPerPage,
            currentPage: 0,
            totalPage: Math.ceil(props.recurrences_analytics?.length / regChartPagination.itemPerPage),
        })
        updateTotalRegistration();

    }, [props.recurrences_analytics]);

    useEffect(() => {
        console.log('regAtnChartDataaa', regAtnChartData)
        let filteredRangeOptions = regAtnChartData.filter((item) => {
            let time = item[0]
            return moment(time, "MMM D", true).isValid()
        })
        console.log('filteredRangeOptions', regAtnChartData, filteredRangeOptions)
        // let filteredArray = 
        setPaginationrange({
            ...paginationrange,
            startDate: filteredRangeOptions?.[0]?.[0],
            endDate: filteredRangeOptions[filteredRangeOptions.length - 1]?.[0],
        })
        setShowSepratorLine(regAtnChartData.length >= 3 ? true : false)
    }, [regAtnChartData])


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will update the current chart data,
     * If event is recurring type it will add the average of the column
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param chartData
     * @returns {*[]}
     */
    const updateRegChartData = (chartData,) => {
        let avgColumn = [];
        if (chartData.length > 1) {
            if (props.recurrences_analytics?.length > 1) {
                avgColumn = [
                    [null, null, null],
                    ['Average', regAtnTotal.avgReg, regAtnTotal.avgAttendance]
                ];
            }

            // updating the min max value of the chart as there is no data to display, and by default the chart will
            // show 1 0 -1 in vAxis so keeping the 0 as min to remove the -1 as min with no data
            setRegChartOption(regAtnTotal.avgReg === 0 && regAtnTotal.avgAttendance === 0 ? {
                ...regChartOption,
                vAxis: {
                    viewWindow: {
                        min: 0,
                        max: 100,
                    },
                },
                hAxis: {
                    viewWindow: {
                        min: 0
                    }
                }
            } : initialOptions)
        }
        console.log('ddddddddddddd chart data', chartData, avgColumn);
        setRegAtnChartData([
            ["Reg Date", t("Registration"), t("Attendance")], // column adding
            ...chartData, // adding rows
            ...avgColumn, // adding average if event is recurring
        ]);
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will update the total registration and total attendance count value from the total
     * available data of recurrences
     * -----------------------------------------------------------------------------------------------------------------
     */
    const updateTotalRegistration = () => {
        let totalReg = 0;
        let totalAttendant = 0;
        let totalRecord = 0;

        props.recurrences_analytics?.forEach(analytics => {
            totalRecord += 1;
            totalReg += analytics.total_registration;
            totalAttendant += analytics.total_attendance;
        });

        totalRecord = totalRecord || 1;

        setRegAtnTotal({
            ...regAtnTotal,
            reg: totalReg,
            attendance: totalAttendant,
            avgReg: totalReg / totalRecord,
            avgAttendance: totalAttendant / totalRecord,
        })
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the previous page display
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handlePreviousPageShow = () => {
        if (regChartPagination.currentPage > 0) {
            setRegChartPagination({
                ...regChartPagination,
                currentPage: regChartPagination.currentPage - 1,
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the next page display of chart
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handleNextPageShow = () => {
        if (regChartPagination.currentPage < regChartPagination.totalPage) {
            setRegChartPagination({
                ...regChartPagination,
                currentPage: regChartPagination.currentPage + 1,
            })
        }
    }

    let availableRange = regChartPagination.currentPage >= (regChartPagination.totalPage - 1);
    return (
        <>
            {
                showNoDataIcon
                    ? <NoDataCard
                        infotext={"no_data_in_occurrence"}
                    />
                    :
                    <Grid
                        container
                        justifyContent={"center"}
                    >
                        <Grid
                            container
                            justifyContent={'flex-end'}
                            className="paginationRow"
                        >
                            {regChartPagination.enabled &&
                            <Grid item>
                                <Button onClick={handlePreviousPageShow}
                                        disabled={regChartPagination?.currentPage === 0}>
                                    <NavigateBeforeIcon />
                                </Button>
                                {`${paginationrange.startDate} - ${paginationrange.endDate}`}
                                <Button onClick={handleNextPageShow}
                                        disabled={regChartPagination.currentPage >= (regChartPagination.totalPage - 1)}>
                                    <NavigateNextIcon />
                                </Button>
                            </Grid>
                            }

                        </Grid>
                        <Grid item lg={12} className={"chartBackground"}>
                            {console.log('regAtnChartData.length', regAtnChartData.length, regAtnChartData)}
                            <Grid>
                                {regAtnChartData.length >= 2 &&
                                <Chart
                                    data={regAtnChartData}
                                    chartType={"ComboChart"}
                                    options={regChartOption}
                                />
                                }
                            </Grid>
                        </Grid>
                        {showSepratorLine && <div className='verticleLine'></div>}
                        {console.log('showSepratorLine', showSepratorLine, regAtnChartData, regAtnChartData.length, regAtnChartData.length >= 3)}
                    </Grid>
            }
        </>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_list: state.Analytics.recurrences_list,
        recurrences_analytics: state.Analytics.recurrences_analytics,
        current_event: eventAction.getCurrentEvent(state),
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
        getEventAnalytics: (eventUuid) => dispatch(eventV4Api.getEventAnalytics(eventUuid)),
    }
}
RegistrationCard = connect(mapStateToProps, mapDispatchToProps)(RegistrationCard);
export default RegistrationCard;