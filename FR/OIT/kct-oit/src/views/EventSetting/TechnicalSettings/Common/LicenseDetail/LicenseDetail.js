import {Grid} from "@material-ui/core";
import React from "react";
import InfoOutlinedIcon from '@material-ui/icons/InfoOutlined';
import Tooltip from '@material-ui/core/Tooltip';
import {useTranslation} from 'react-i18next';

/**
 * @class
 * @component
 *
 * ---------------------------------------------------------------------------------------------------------------------
 * @description This component is for showing/viewing license details on technical setting page . This component
 * contains basic details of license like number of license , available license , details of assignee - first name ,
 * last name , email .
 * ---------------------------------------------------------------------------------------------------------------------
 *
 * @param {Object} props Inherited from parent component
 * @param {String} props.heading Heading of license type
 * @param {Object} props.data License details
 * @param {Object} props.data.available_license Available licenses
 * @param {Array} props.data.licenses Array of licenses
 * @returns {JSX.Element}
 * @constructor
 */
const LicenseDetail = (props) => {
    const {t} = useTranslation("events");
    return (
        <Grid container xs={12} className="licenseBlockWrap">
            <Grid container xs={12} className="licenseBlock">
                <Grid container xs={12} className="FlexRow">
                    <p className="primaryHeading">{props.heading}</p>
                </Grid>
                <Grid container xs={12} className="FlexRow">
                    <Grid item xs={3}>
                        <p className='customPara customPara-2'>Number of Licenses:
                            <Tooltip arrow title={t("zoom_licenses")}><InfoOutlinedIcon /></Tooltip></p>
                    </Grid>
                    <Grid item xs={9}>
                        <p className='customPara customPara-2'>{props.data.available_license}</p>
                    </Grid>
                </Grid>
                {props.data.licenses.map(license => {
                    return <>
                        <Grid container xs={12} className="FlexRow">
                            <Grid item xs={3}>
                                <p className='customPara customPara-2'>License:</p>
                            </Grid>
                            <Grid item xs={9}>
                                {`${license.fname} ${license.lname} (${license.email}) - 
                               ${license.number_of_participants} Participants`}
                            </Grid>
                        </Grid>

                    </>
                })}

            </Grid>
        </Grid>
    )
}

export default LicenseDetail;