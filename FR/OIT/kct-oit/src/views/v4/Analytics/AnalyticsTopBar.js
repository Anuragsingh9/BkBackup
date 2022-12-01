import Constants from "../../../Constants";
import BreadcrumbsInput from "../Common/Breadcrumbs/BreadcrumbsInput";
import React from "react";
import {Box, Button, Grid, Tooltip, Typography} from "@mui/material";
import Helper from "../../../Helper";
import TabContext from "@mui/lab/TabContext";
import CustomContainer from "../../Common/CustomContainer/CustomContainer";
import {connect} from "react-redux";
import AnalyticsTabComponent from "./AnalyticsTabComponent";

/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for analytics to bar to render the analytics tab(Engagement, Users, Event Type)
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @param {String} props.organisation_name Organization name
 * @returns {JSX.Element}
 * @constructor
 */
let AnalyticsTopBar = (props) => {
    console.log('analy props',props);

    const [value, setValue] = React.useState(Constants.analyticsTabType.ENGAGEMENT);

    return (
        <>
            <BreadcrumbsInput
                links={[
                    Constants.breadcrumbsOptions.ANALYTICS,
                    props.analyticsTabValue === 0 ?
                        Constants.breadcrumbsOptions.ENGAGEMENT : null
                ]}
            />
            <CustomContainer className="EventManageWrap">
                <Grid container specing={0} lg={12} sm={8}>

                    <Grid item lg={12} sm={12} className="publishEvent_Tabwrap">
                        <div className='publishEvent_wrap'>
                            <span>
                                <h3>{Helper.limitText('Event Analytics', 25)}</h3>
                            </span>
                        </div>
                        <TabContext value={value}>
                            <AnalyticsTabComponent
                                value={value}
                                setValue={setValue}
                                {...props}
                            />
                        </TabContext>
                    </Grid>
                </Grid>
            </CustomContainer>

        </>
    )
}

const mapStateToProps = (state) => {
    return {
        organisation_name: state.Group.organisation_name,
    };
};

AnalyticsTopBar = connect(mapStateToProps, null)(AnalyticsTopBar);

export default AnalyticsTopBar;