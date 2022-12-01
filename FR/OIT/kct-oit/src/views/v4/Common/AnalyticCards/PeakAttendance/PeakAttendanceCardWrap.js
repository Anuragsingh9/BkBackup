import React, {useState} from 'react'
import CardTab from '../../InfoCard/CardTab'
import InfoCard from '../../InfoCard/InfoCard'
import PeakAttendanceCard from './PeakAttendanceCard.js'
import "../AnalyticsCard.css"
import Constants from "../../../../../Constants";
import {MenuItem} from "@material-ui/core";
import SelectField from "../../CoreInputs/SelectField";
import {useTranslation} from "react-i18next";
import {connect} from "react-redux";

/**
 * @component
 * @class
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This wrapper component is used for peak attendance card
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props props is used for redux data
 * @returns {JSX.Element}
 * @constructor
 */
let PeakAttendanceCardWrap = (props) => {
    const {t} = useTranslation('profile')
    const [recUuid, setRecUuid] = useState();
    const [gradeOption, setGradeOption] = useState(Constants.gradeOptions.ALL);

    // Prepare the date filter for peek attendance card
    let peekAttendanceCardTab = [];
    props.recurrences_analytics.map((list, i) => {

        peekAttendanceCardTab[i] = {
            label: list.rec_start_date.format("DD MMM"),
            rec_uuid: list.recurrence_uuid,
            component: <PeakAttendanceCard
                recUuid={recUuid}
                gradeOption={gradeOption}
            />
        }
    })

    return (
        <InfoCard className="PeakAttendenceCard analyticsCardCSS">
            <CardTab
                setRecUuid={setRecUuid}
                tabs={peekAttendanceCardTab}
                tabHeading={'Peak Attendance'}
                gradeFilter={
                    <SelectField
                        disabled={false}
                        size={'small'}
                        className="cardFooterDropdown"
                        onChange={(e) => {
                            setGradeOption(e.target.value)
                        }}
                        defaultValue={Constants.gradeOptions.ALL}
                    >
                        {
                            Object.values(Constants.gradeOptions).map((option, index) => (
                                <MenuItem
                                    value={option}
                                >
                                    {t(option)}
                                </MenuItem>
                            ))
                        }
                    </SelectField>
                }
            />


        </InfoCard>
    )
}

const mapStateToProps = (state) => {
    return {
        recurrences_analytics: state.Analytics.recurrences_analytics,
    }
}

PeakAttendanceCardWrap = connect(mapStateToProps, null)(PeakAttendanceCardWrap);
export default PeakAttendanceCardWrap;