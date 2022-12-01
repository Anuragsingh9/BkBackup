import React, {useEffect, useState} from "react";
import {useTranslation} from "react-i18next";
import ServerSideDataTable from "../../Common/ServerSideDataTable/ServerSideDataTable";
import eventV4Api from "../../../../redux/action/apiAction/v4/event";
import {connect} from "react-redux";
import moment from "moment-timezone";
import RecurrenceIcon from "../../Svg/RecurrenceIcon";
import _ from "lodash";

/**
 * -----------------------------------------------------------------------------------------
 * @description This component is used for renter the table list
 * -----------------------------------------------------------------------------------------
 *
 * @param {Object} props passed from parent component
 * @param {Object} props.analyticsData Analytics data(data, links, meta)
 * @param {Array} props.analyticsData.data Array of Analytics data
 * @param {Array} props.analyticsData.meta Array of Analytics meta data
 * @param {String} props.getRowId Event uuid
 * @returns {JSX.Element}
 * @constructor
 */
let AnalyticsTable = (props) => {
    const {t} = useTranslation(["analyticsData", "confirm", "common"]);
    const eventTypes = {1: "Cafeteria", 2: "Executive", 3: "Manager"};

    const [tableData, setTableData] = useState({rows: [], bottomRow: null});
    const [callApi, setCallApi] = useState(false);
    const [sortModelForAnalytics, setSortModelForAnalytics] = React.useState([
        {
            field: "event_date", //event date
            sort: "desc",
        },
    ]);

    useEffect(() => {
        callAPI(true)
    }, [props.fetch_analytic_group_dropdown, props.range_picker_val, props.searched_key])

    useEffect(() => {
        if (props.analyticsData?.data) {
            let data = calculateTotal(props.analyticsData?.data);
            setTableData({...props.analyticsData, ...data});
        }
    }, [props.analyticsData]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is prepare the column data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {String||Number} data column value
     * @returns {JSX.Element}
     */
    const renderCell = (data) => {
        return <span>{data}</span>
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method will simply open(redirect) the event analytics in new tab.
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @param {String} eventUuid Event uuid
     */
    const redirectToEventHandler = (eventUuid) => {
        let link = `event/analytics/` + eventUuid;
        window.open(link, '_blank', 'noopener,noreferrer');
    }

    let analyticTableColumns = [
        {
            field: 'event_name', headerName: t("eventTitle"), minWidth: 200, sortable: true,
            renderCell: (params) => {
                const {row} = params;
                let enableLink = _.has(row,['is_bottom_row']) && row?.is_bottom_row === true
                return (
                    <>
                        {!enableLink ?
                            <span className="userNameLink" onClick={() => redirectToEventHandler(row.event_uuid)}>
                            {row.event_title} {row.is_recurrence ? ` - ${row.rec_count}` : ''}
                        </span>
                            :
                            <span className="">
                            {row.event_title} {row.is_recurrence ? ` - ${row.rec_count}` : ''}
                        </span>
                        }
                    </>
                );
            }
        },
        {
            field: 'event_type', headerName: t("eventType"), minWidth: 110, sortable: true, flex: 1,
            renderCell: (params) => {
                const {row} = params;
                return (
                    <span className={"reccCellWithIcon"}>
                        {eventTypes[params.row.event_type]}
                        {" "}
                        {row.is_recurrence === 1 && <RecurrenceIcon />}
                    </span>
                )
            }
        },
        {
            field: 'zoom_meeting', headerName: t("zoomMeeting"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.is_zoom_meeting === 1 ? 'Yes' : params.row.is_zoom_meeting === '' ? '' : 'No'),
        },
        {
            field: 'zoom_webinar', headerName: t("zoomWebinar"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.is_zoom_webinar === 1 ? 'Yes' : params.row.is_zoom_webinar === '' ? '' : 'No'),
        },
        {
            field: 'media_video', headerName: t("mediaVideo"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.media_video),
        },
        {
            field: 'media_image', headerName: t("mediaImage"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.media_image),
        },
        {
            field: 'total_registration', headerName: t("totalRegistration"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.total_registration)
        },
        {
            field: 'total_attendees', headerName: t("totalAttendees"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.total_attendance),
        },
        {
            field: 'total_conv_count', headerName: t("totalConv"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.total_conv_count),
        },
        {
            field: 'total_duration', headerName: t("totalDuration"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.total_duration)
        },
        {
            field: 'sh_conv_count', headerName: t("SHConv"), width: 100, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.sh_conv_count)
        },
        {
            field: 'event_date', headerName: t("common:date"), width: 100, minWidth: 250, sortable: true, flex: 1,
            renderCell: (params) => renderCell(params.row.event_date)
        },

    ];

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for ignore the seconds in time and return the only minutes value
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} seconds Seconds
     * @returns {number}
     */
    const roundOffSecond = seconds => (seconds - (seconds % 60)) / 60;

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for calculating the time from seconds
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Number} seconds Seconds
     * @param {Boolean} roundOff if ignore the seconds
     * @returns {String}
     */
    const getTimeFromSeconds = (seconds, roundOff = true) => {
        let minutes = roundOff ? roundOffSecond(seconds) : seconds;

        let hours = (minutes - (minutes % 60)) / 60;
        minutes = minutes % 60;

        return `${hours}h ${minutes}m`;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for calculating the total for
     * 1. Table columns value count(media video, media image, total registration)
     * 2. Time(hours and minutes)
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {Array} rows Event object
     * @returns {Object}
     */
    const calculateTotal = (rows) => {
        let result = {
            event_title: rows.length + ' Events ',
            is_bottom_row: true,
            event_type: '',
            media_video: 0,
            media_image: 0,
            total_registration: 0,
            total_attendance: 0,
            total_conv_count: 0,
            conv_two: 0,
            conv_three: 0,
            conv_four: 0,
            total_duration: 0,
            sh_conv_count: 0,
            sh_conv_duration: 0,
            event_date: '',
            is_zoom_webinar: '',
            is_zoom_meeting: '',
            event_uuid: '',
            recurrence_uuid: '',
        }

        let mappedRows = [...rows].map(row => {

            result['media_video'] += row.media_video;
            result['media_image'] += row.media_image;
            result['total_registration'] += row.total_registration;
            result['total_attendance'] += row.total_attendance;
            result['total_conv_count'] += row.total_conv_count;
            result['conv_two'] += roundOffSecond(row.conv_two);
            result['conv_three'] += roundOffSecond(row.conv_three);
            result['conv_four'] += roundOffSecond(row.conv_four);
            result['total_duration'] += roundOffSecond(row.total_duration);
            result['sh_conv_count'] += row.sh_conv_count;
            result['sh_conv_duration'] += roundOffSecond(row.sh_conv_duration);

            row.conv_two = getTimeFromSeconds(row.conv_two);
            row.conv_three = getTimeFromSeconds(row.conv_three)
            row.conv_four = getTimeFromSeconds(row.conv_four)
            row.total_duration = getTimeFromSeconds(row.total_duration)
            row.sh_conv_duration = getTimeFromSeconds(row.sh_conv_duration)

            return row;
        });

        result.conv_two = getTimeFromSeconds(result.conv_two);
        result.conv_three = getTimeFromSeconds(result.conv_three)
        result.conv_four = getTimeFromSeconds(result.conv_four)
        result.total_duration = getTimeFromSeconds(result.total_duration, false)
        result.sh_conv_duration = getTimeFromSeconds(result.sh_conv_duration)

        return {
            rows: mappedRows,
            bottomRow: result,
        };
    }

    const rowsData = React.useMemo(() => {
        let additional = {};
        if (tableData.bottomRow) {
            additional = {
                pinnedRow: {
                    bottom: [tableData.bottomRow],
                }
            }
        }
        return {
            rows: {
                data: tableData.rows,
                meta: props.analyticsData?.meta,
            },
            ...additional
        };
    }, [tableData]);

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for render the rows data and apply calculation on the rows data
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventObj} data Event object
     * @returns {Object}
     */
    const renderRows = (data) => {
        let d = calculateTotal(data);
        data = d.rows;
        return data;
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description This method is used for render the bottom row data and apply calculation on the bottom row
     * -----------------------------------------------------------------------------------------------------------------
     *
     * @method
     * @param {EventObj} data Event object
     * @returns {Object}
     */
    const renderBottomRow = (data) => {
        let dd = calculateTotal(data);
        return {
            bottom: [dd.bottomRow],
        }
    }

    const urlPayloadData = {
        groupKey: props.fetch_analytic_group_dropdown,
        from_date: moment(props.range_picker_val[0]).format('YYYY-MM-DD HH:mm:ss'),
        to_date: moment(props.range_picker_val[1]).format('YYYY-MM-DD HH:mm:ss'),
        key: props.searched_key,
        order_by: sortModelForAnalytics[0].field,
        order: sortModelForAnalytics[0].sort,

    }

    const callAPI = (boolean) => {
        setCallApi(boolean);
    }

    return (
        <div>
            <ServerSideDataTable
                url={eventV4Api.getAnalyticsData}
                columns={analyticTableColumns}
                rows={rowsData.rows}
                getRowId={(row) => row.recurrence_uuid}
                className="analyticsCustomDataGrid"
                disableColumnMenu={true}
                disableCheckBox={true}
                pinnedData={rowsData.pinnedRow}
                disableRowPinned={true}
                renderResponse={renderRows}
                renderBottom={renderBottomRow}
                urlPayloadData={urlPayloadData}
                onPayloadDataChange={callAPI}
                callAPI={callApi}
                setSortModel={(model) => {
                    if (
                        !_.isEmpty(model) &&
                        JSON.stringify(model[0]) !== JSON.stringify(sortModelForAnalytics[0])
                    ) {
                        setSortModelForAnalytics(model);
                    }
                }}
                sortModel={sortModelForAnalytics}
            />
        </div>
    )
}

const mapStateToProps = (state) => {
    return {
        fetch_analytic_group_dropdown: state.Group.analytic_group_dropdown,
        range_picker_val: state.Group.engagement_tab_data.range_picker_val,
        searched_key: state.Group.searched_key,
    };
};

AnalyticsTable = connect(mapStateToProps, null)(AnalyticsTable)

export default AnalyticsTable;