import React from 'react'
import {IconButton} from '@material-ui/core'
import {useParams} from 'react-router-dom'
import AnalyticsIcon from "../../Svg/AnalyticsIcon";
import {connect} from "react-redux";
import Tooltip from "@material-ui/core/Tooltip";
import {useTranslation} from 'react-i18next';



/**
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is used for analytics button and when user click on analytics button it redirect to analytic page
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Passed from parent component
 * @returns {JSX.Element}
 * @constructor
 */
let AnalyticsBtn = (props) => {
    const {t} = useTranslation("sidebar");
    const {gKey} = useParams();

    const redirectToAnalytics = () => {
        props.history.push(`/${gKey}/v4/analytics`);
    }
    return (
        <>
            {!props.user_meta_data?.is_acc_analytics_enabled ?
                <Tooltip arrow placement="right" title={t('analytics_btn_disable_text')}>
                    <span>
                        <IconButton
                            onClick={redirectToAnalytics}
                            disabled={!props.user_meta_data?.is_acc_analytics_enabled}
                            className={`${!props.user_meta_data?.is_acc_analytics_enabled && "customDisableBtnColor"}`}
                        >
                            <AnalyticsIcon />
                        </IconButton>
                    </span>
                </Tooltip>
                :
                <IconButton
                    onClick={redirectToAnalytics}
                    disabled={!props.user_meta_data?.is_acc_analytics_enabled}
                >
                    <AnalyticsIcon />
                </IconButton>
            }
        </>
    )
}
const mapStateToProps = (state) => {
    return {
        user_meta_data: state.Auth.userMetaData,
    };
};

AnalyticsBtn = connect(mapStateToProps, null)(AnalyticsBtn)
export default AnalyticsBtn