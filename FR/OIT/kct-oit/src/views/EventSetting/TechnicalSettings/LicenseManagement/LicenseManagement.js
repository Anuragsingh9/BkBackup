import {Grid} from "@material-ui/core";
import React, {useEffect, useState} from "react";
import LicenseDetail from "../Common/LicenseDetail/LicenseDetail";

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This LicenseManagement component is a container component . this is for management of license of
 * account like Zoom meetings and Zoom webinar and stores details regarding to license and passes to child components
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {Function} props.getKeyData To get the key data
 * @param {String} props.dataKey Key of data
 * @returns {JSX.Element}
 * @constructor
 */
const LicenseManagement = (props) => {

    const [broadcastData, setBroadcastData] = useState({
        webinar_data: {
            available_license: 0,
            licenses: [],
        },
        meeting_data: {
            available_license: 0,
            licenses: [],
        },
        is_assigned: 1,
        authenticate_url: "",
        user_can_update: 0,
    })


    useEffect(() => {
        const data = props.getKeyData(props.dataKey);
        // update state of basic details of Zoom data 
        setBroadcastData({
            webinar_data: data.value.webinar_data,
            meeting_data: data.value.meeting_data,
            is_assigned: data.value.is_assigned,
            enabled: data.value.enabled,
            user_can_update: data.value.user_can_update,
            authenticate_url: data.value.authenticate_url,
        })
    }, []);

    return (
        <div>
            <Grid container xs={12} className="licenseBlockWrap_main">
                {/* if license assigned then only show the meeting and webinar data */}
                {(broadcastData.is_assigned === 1 && broadcastData.user_can_update === 1) &&
                <>
                    <LicenseDetail
                        heading={'ZOOM MEETING'}
                        data={broadcastData.meeting_data} />
                    <LicenseDetail
                        heading={'ZOOM WEBINAR'}
                        data={broadcastData.webinar_data} />
                </>
                }
            </Grid>
        </div>
    )
}

export default LicenseManagement;