import React, {useEffect, useState} from 'react';
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import {connect} from "react-redux";
import {Grid} from "@mui/material";
import {useTranslation} from "react-i18next";
import eventAction from "../../../../redux/action/reduxAction/event";
import Constants from "../../../../Constants";
import {Chart} from "react-google-charts";
import ChartOptionsHandler from '../AnalyticCards/ChartOptionsHandler';
import PaginationBar from '../PaginationBar/PaginationBar';
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
let EngagementCard = (props) => {
    const {t} = useTranslation(['common', 'analytics']);

    let durationChartOptions = ChartOptionsHandler?.comboChart({
        // title: "Average durations",
        vAxis: {title: "Average Minutes"},
        hAxis: {title: "Date"},
        legend: "none",
        tooltip: {
            isHtml: true,
        }
    })

    let countChartOptions = ChartOptionsHandler?.pieChart({
        // title: "Total Conversations",
        // pieHole: 0,
        is3D: false,
        legend: "none",
        tooltip: {
            isHtml: true,
        }
    })


    const [totalConv, setTotalConv] = useState({count: 0, duration: 0,});
    const [convPagination, setConvPagination] = useState({
        itemPerPage: Constants.GOOGLE_CHART.ENGAGEMENT_PAGINATION,
        currentPage: 0,
        totalPage: 0,
    });
    const [countChartData, setCountChartData] = useState([]);
    const [durationChartData, setDurationChartData] = useState([]);
    const [currentChartCount, setCurrentChartCount] = useState(0);
    const [currentChartDuration, setCurrentChartDuration] = useState(0);

    useEffect(() => {
        let totalCount = 0;
        let totalDuration = 0;

        props.recurrences_analytics.forEach((rec, index) => {
            totalCount += rec.total_conv_count;
            totalDuration += rec.total_conv_duration;
        })

        console.log('propss', props);
        setTotalConv({
            ...totalConv,
            count: totalCount,
            duration: totalDuration,
        });

        setConvPagination({
            ...convPagination,
            currentPage: 0,
            totalPage: props.recurrences_analytics.length,
        })
    }, [props.recurrences_analytics]);

    const prepareToolTipForCountChart = (users, count) => {
        return `<div style="padding: 5px">
            <table>
                <tr><td>Conv Users: <b>${Constants.NUM_TO_ENG[users]}</b></td></tr>
                <tr><td>Conv Count: <b>${count}</b></td></tr>
            </table>
        </div>`;
    }

    useEffect(() => {
        let totalConvoCount = 0;
        let totalConvoDuration = 0;
        props.recurrences_analytics.forEach((rec, index) => {
            if (index === convPagination.currentPage) {
                let countChartData = [];
                let durationChartData = [];
                rec.conversations.forEach(convData => {
                    console.log('durationChartData', convData.users, convData.count)

                    countChartData.push([`${convData.users} Users`, convData.count, prepareToolTipForCountChart(convData.users, convData.count)]);
                    durationChartData.push([convData.users, Math.round(convData.duration / 60) / (convData.count || 1)]);
                    totalConvoCount += convData.count;
                    totalConvoDuration += convData.duration;
                })
                console.log('dtotalConvoDuration', totalConvoDuration)
                updateConvChartData(countChartData, durationChartData, rec.rec_start_date);
            }
        })

        console.log('totalConvoCount', totalConvoDuration)
        setCurrentChartCount(totalConvoCount);
        setCurrentChartDuration(totalConvoDuration);
    }, [convPagination, props.recurrences_analytics]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To update the chart data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param countChartData
     * @param durationChartData
     * @param startDate
     */
    const updateConvChartData = (countChartData = null, durationChartData = null, startDate = null) => {
        if (countChartData) {
            console.log('countChartData', countChartData)
            setCountChartData([
                ['Users Count', 'Conversation Count', {'type': 'string', 'role': 'tooltip', 'p': {'html': 'true'}}],
                ...countChartData,
            ]);
        }
        if (durationChartData && startDate) {
            let columns = ['Date'];
            let data = [startDate.format('MMM, D')];


            durationChartData.forEach(duration => {
                columns.push(`Conv of ${duration[0]}`)
                columns.push({'type': 'string', 'role': 'tooltip', 'p': {'html': 'true'}});
                data.push(duration[1]);
                let word = Constants.NUM_TO_ENG[duration[0]] || null;
                let tooltip = `<div style="padding: 6px">
                    <table class="medals_layout">
                        <tr style="border-bottom: 5px">                      
                            <td><b>${startDate.format('MMM, D')}</b></td>
                        </tr>
                        <tr style="border-bottom: solid 10px black">                      
                            <td>Conv Users: <b>${word}</b></td>
                        </tr>
                        <tr>                      
                            <td>Avg Duration: <b>${duration[1].toFixed(2)}m</b></td>
                        </tr>
                    </table>
                </div>`;
                data.push(tooltip);

            })
            setDurationChartData([columns, data]);
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the previous page display
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handlePreviousPageShow = () => {
        if (convPagination.currentPage > 0) {
            setConvPagination({
                ...convPagination,
                currentPage: convPagination.currentPage - 1,
            })
        }
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description To handle the next page display of chart
     * -----------------------------------------------------------------------------------------------------------------
     */
    const handleNextPageShow = () => {
        if (convPagination.currentPage < convPagination.totalPage) {
            setConvPagination({
                ...convPagination,
                currentPage: convPagination.currentPage + 1,
            })
        }
    }


    const engagementTime = `${Math.floor(currentChartDuration / 3600)} hr ${Math.floor((currentChartDuration % 3600) / 60)} min`;
    console.log('currentChartDuration', currentChartDuration)
    return (
        <>
            {totalConv.count === 0 ?
                <NoDataCard />
                :
                <>
                    <Grid
                        container
                        justifyContent={'flex-end'}
                        className={"chartBackground engagementCard paginationRow"}
                    >

                        {
                            convPagination.totalPage > 1 &&
                            <PaginationBar
                                handlePreviousPageShow={handlePreviousPageShow}
                                disablePrevious={convPagination.currentPage <= 0}
                                handleNextPageShow={handleNextPageShow}
                                disableNext={convPagination.currentPage >= (convPagination.totalPage - 1)}
                                selectedVal={props.recurrences_analytics[convPagination?.currentPage]?.rec_start_date}
                            />
                        }
                    </Grid>
                    <Grid container justifyContent="center" alignItems="center" item lg={12}>
                        <div className="customLegendsLabels mainLabel">Conversation of</div>

                        <div className="customLegends convo2"></div>
                        <div className="customLegendsLabels">2 Users</div>

                        <div className="customLegends convo3"></div>
                        <div className="customLegendsLabels">3 Users</div>

                        <div className="customLegends convo4"></div>
                        <div className="customLegendsLabels">4 Users</div>

                        <div className="customLegends convo5"></div>
                        <div className="customLegendsLabels">5 Users</div>

                        <div className="customLegends convo6"></div>
                        <div className="customLegendsLabels">6 Users</div>

                        <div className="customLegends convo7"></div>
                        <div className="customLegendsLabels">7 Users</div>

                    </Grid>
                    <Grid container item lg={12} alignItems={"center"} justifyContent={"center"}>
                        {currentChartCount === 0 ?
                            <NoDataCard />
                            : <>
                                <Grid item lg={6}>
                                    {
                                        countChartData.length ?
                                            <Chart
                                                data={countChartData}
                                                chartType={"PieChart"}
                                                options={countChartOptions}
                                            /> :
                                            <span>No Data</span>
                                    }
                                </Grid>
                                <Grid item lg={6}>
                                    <Chart
                                        data={durationChartData}
                                        chartType={"ComboChart"}
                                        options={durationChartOptions}
                                    />
                                </Grid>
                            </>
                        }
                        {console.log('allProps', props)}
                    </Grid>
                    <Grid
                        container
                        item lg={12}
                        alignItems={"center"}
                        justifyContent={"space-around"}
                    >

                        <p className='m-0'>{t('common:total number')} <b
                            className='color_primary'>{currentChartCount} {t('common:conversation')}</b></p>
                        <p className='m-0'>{t('common:total duration')} <b
                            className='color_primary'>{engagementTime}</b></p>
                        {console.log('recurrences_analyticsssw', props.recurrences_analytics)}
                    </Grid>
                </>

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
EngagementCard = connect(mapStateToProps, mapDispatchToProps)(EngagementCard);
export default EngagementCard;